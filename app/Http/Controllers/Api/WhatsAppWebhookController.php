<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasantri;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;
use App\Services\GeminiOcrService;
use App\Services\PaymentVerificationService;
use Carbon\Carbon;
use Exception;

class WhatsAppWebhookController extends Controller
{
    protected $geminiOcr;
    protected $verificationService;

    public function __construct(GeminiOcrService $geminiOcr, PaymentVerificationService $verificationService)
    {
        $this->geminiOcr = $geminiOcr;
        $this->verificationService = $verificationService;
    }

    /**
     * Handle incoming WhatsApp Webhook
     */
    public function handle(Request $request)
    {
        Log::info('📞 WhatsApp Webhook Received', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'body_preview' => substr($request->getContent(), 0, 500)
        ]);

        // 1. Authenticate webhook request
        $webhookToken = config('services.whatsapp.webhook_key', 'siakad_wa_webhook_secret_key');
        // Accept token from X-Webhook-Token header or 'token' query param
        $requestToken = $request->header('X-Webhook-Token') ?? $request->query('token');

        if ($requestToken !== $webhookToken) {
            Log::warning('⚠️ Unauthorized WhatsApp Webhook attempt', [
                'received_token' => $requestToken
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid webhook token.'
            ], 401);
        }

        try {
            // 2. Parse payload fields (Flexible handling for direct payloads or Evolution API)
            $parsedPayload = $this->parsePayload($request);

            // Handle billing query request
            if ($request->input('is_query') || ($parsedPayload['is_query'] ?? false)) {
                $mahasiswa = $this->findMahasiswa($parsedPayload['sender'], $parsedPayload['text'] ?? '');
                
                if (!$mahasiswa) {
                    // Check if NIM is present in query to provide helpful security instructions
                    $nim = null;
                    if (!empty($parsedPayload['text'])) {
                        preg_match('/\b[0-9]{6,15}\b/', $parsedPayload['text'], $matches);
                        if (!empty($matches[0])) {
                            $nim = $matches[0];
                        }
                    }

                    if ($nim) {
                        $possibleMhs = Mahasantri::where('numb_nim', $nim)->first();
                        if ($possibleMhs) {
                            $this->sendWhatsAppReply(
                                $parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 
                                'Untuk keamanan tautan baru, silakan kirim ulang dengan format: cek tagihan [NIM] [4 digit terakhir nomor HP Anda yang terdaftar di SIAKAD] (contoh: cek tagihan ' . $nim . ' 6763).'
                            );
                            return response()->json([
                                'success' => false,
                                'message' => 'NIM match found but PIN verification required.'
                            ], 422);
                        }
                    }

                    $this->sendWhatsAppReply(
                        $parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 
                        'Maaf, sistem tidak dapat mengidentifikasi data mahasiswa Anda dari nomor WhatsApp ini. Silakan kirim ulang pesan cek tagihan dengan mencantumkan NIM Anda (contoh: cek tagihan NIM 12345678).'
                    );
                    return response()->json([
                        'success' => false,
                        'message' => 'Student not identified for query'
                    ], 422);
                }

                // Query bills
                $tagihans = TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                    ->whereIn('status', [TagihanKuliah::PAYMENT_PENDING, 'Cicilan', 'cicilan', 'CICILAN'])
                    ->orderBy('due_date', 'asc')
                    ->get();

                $reply = "🕌 *INFORMASI TAGIHAN & DEPOSIT*\n\n" .
                         "👤 *Nama:* {$mahasiswa->name}\n" .
                         "🆔 *NIM:* " . ($mahasiswa->numb_nim ?? '-') . "\n" .
                         "💼 *Deposit Saldo:* Rp " . number_format($mahasiswa->deposit_balance ?? 0, 0, ',', '.') . "\n\n";

                if ($tagihans->isEmpty()) {
                    $reply .= "✅ *Status:* Alhamdulillah, Anda tidak memiliki tagihan aktif yang perlu dibayar saat ini.";
                } else {
                    $reply .= "📋 *Daftar Tagihan Aktif:*\n";
                    $totalPending = 0;
                    foreach ($tagihans as $index => $t) {
                        $remaining = (float)($t->remaining_amount ?? $t->amount);
                        $totalPending += $remaining;
                        $groupName = $t->group->name ?? 'Tagihan Kuliah';
                        
                        $reply .= ($index + 1) . ". *{$groupName}*\n" .
                                  "   • Sisa Tagihan: Rp " . number_format($remaining, 0, ',', '.') . "\n";
                    }
                    $reply .= "\n💵 *Total Harus Dibayar:* Rp " . number_format($totalPending, 0, ',', '.') . "\n\n" .
                              "_Untuk konfirmasi bayar, silakan kirim foto/screenshot struk bukti transfer._";
                }

                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], $reply);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Billing query processed.'
                ]);
            }

            if (!$parsedPayload['has_media']) {
                Log::info('ℹ️ Webhook message has no image media attachment. Ignoring.');
                return response()->json([
                    'success' => true,
                    'message' => 'Message ignored: no image media attachment found.'
                ]);
            }

            Log::info('📥 Parsed Webhook Payload', [
                'sender' => $parsedPayload['sender'],
                'message_text' => $parsedPayload['text'],
                'media_type' => $parsedPayload['media_type']
            ]);

            // 3. Find the student/mahasiswa
            $mahasiswa = $this->findMahasiswa($parsedPayload['sender'], $parsedPayload['text']);

            if (!$mahasiswa) {
                Log::warning('❌ Mahasiswa could not be identified from sender phone or message text', [
                    'sender' => $parsedPayload['sender'],
                    'text' => $parsedPayload['text']
                ]);
                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 'Maaf, sistem tidak dapat mengidentifikasi data mahasiswa Anda dari nomor WhatsApp ini. Silakan kirim pesan konfirmasi dengan mencantumkan NIM Anda (contoh: Konfirmasi NIM 12345678).');
                return response()->json([
                    'success' => false,
                    'message' => 'Student not identified'
                ], 422);
            }

            Log::info('🎓 Mahasiswa Identified', [
                'id' => $mahasiswa->id,
                'name' => $mahasiswa->name,
                'nim' => $mahasiswa->nim
            ]);

            // 4. Find active unpaid tagihans
            $tagihans = TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', [TagihanKuliah::PAYMENT_PENDING, 'Cicilan', 'cicilan', 'CICILAN'])
                ->orderBy('due_date', 'asc')
                ->get();

            if ($tagihans->isEmpty()) {
                Log::info('ℹ️ No active pending tagihans found for student', [
                    'mahasiswa_id' => $mahasiswa->id
                ]);
                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], "Halo {$mahasiswa->name}, saat ini Anda tidak memiliki tagihan pembayaran aktif yang perlu dilunasi. Terima kasih.");
                return response()->json([
                    'success' => true,
                    'message' => 'No active tagihan found for this student.'
                ]);
            }

            // 5. Download the image media content
            $imageContent = $this->downloadMedia($parsedPayload);

            if (empty($imageContent)) {
                Log::error('❌ Failed to download or retrieve image content from webhook');
                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 'Gagal mengunduh gambar bukti pembayaran. Silakan kirim ulang bukti transfer Anda dengan jelas.');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve image content'
                ], 400);
            }

            // 6. Run Gemini OCR to extract payment details
            $ocrResult = $this->geminiOcr->extractReceiptDetails($imageContent);

            if (!$ocrResult['success'] || empty($ocrResult['data'])) {
                Log::warning('❌ Gemini API failed to extract details from receipt image');
                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 'Sistem gagal membaca data struk bukti transfer Anda secara otomatis. Harap pastikan gambar bukti transfer yang dikirim bersih dan jelas.');
                return response()->json([
                    'success' => false,
                    'message' => 'OCR extraction failed: ' . ($ocrResult['message'] ?? 'Unknown API error')
                ], 422);
            }

            $ocrData = $ocrResult['data'];
            $ocrAmount = (float)($ocrData['amount'] ?? 0);
            $ocrSuccess = (bool)($ocrData['is_success'] ?? false);

            if (!$ocrSuccess || $ocrAmount <= 0) {
                Log::warning('❌ Receipt status is not successful or amount is invalid', [
                    'ocr_data' => $ocrData
                ]);
                $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], 'Bukti transfer yang dikirim tidak terbaca sebagai transaksi "SUKSES" atau nominal tidak valid. Silakan periksa kembali struk transfer Anda.');
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt does not show successful transfer or amount is zero.'
                ], 422);
            }

            // 7. Match extracted nominal with student's tagihans
            // Try to find a tagihan that matches the OCR amount
            $selectedTagihan = null;
            foreach ($tagihans as $tagihan) {
                $remainingAmount = (float)($tagihan->remaining_amount ?? $tagihan->amount);
                // Allow a small offset of +/- 100 rupiahs for bank code unique numbers
                if (abs($remainingAmount - $ocrAmount) <= 100) {
                    $selectedTagihan = $tagihan;
                    Log::info('🎯 Matched invoice amount exactly with OCR nominal', [
                        'tagihan_id' => $tagihan->id,
                        'amount' => $remainingAmount,
                        'ocr_amount' => $ocrAmount
                    ]);
                    break;
                }
            }

            // Fallback to the oldest unpaid tagihan if no exact match is found
            if (!$selectedTagihan) {
                $selectedTagihan = $tagihans->first();
                Log::info('⚠️ No exact invoice match. Defaulted to the oldest pending tagihan', [
                    'tagihan_id' => $selectedTagihan->id,
                    'amount' => $selectedTagihan->amount,
                    'ocr_amount' => $ocrAmount
                ]);
            }

            // 8. Store the image locally on storage/public/payment_proofs (mimics student manual upload)
            $filename = 'whatsapp_payment_' . time() . '_' . uniqid() . '.jpg';
            Storage::disk('public')->put('payment_proofs/' . $filename, $imageContent);
            $localProofPath = 'payment_proofs/' . $filename;

            // 9. Database Transaction: Create transaction and queue it for verification
            $verificationResult = DB::transaction(function () use ($mahasiswa, $selectedTagihan, $ocrAmount, $ocrData, $localProofPath, $parsedPayload) {
                // Find manual bank transfer payment method
                $paymentMethod = PaymentMethod::where('type', 'bank_transfer')->first();
                if (!$paymentMethod) {
                    $paymentMethod = PaymentMethod::first();
                }

                // Get first active bank account (optional)
                $bankAccount = BankAccount::where('is_active', true)->first();

                // Create the PaymentTransaction in "processing" state
                $transaction = PaymentTransaction::create([
                    'transaction_code' => PaymentTransaction::generateTransactionCode(),
                    'invoice_number' => PaymentTransaction::generateInvoiceNumber(),
                    'mahasiswa_id' => $mahasiswa->id,
                    'tagihan_id' => $selectedTagihan->id,
                    'payment_method_id' => $paymentMethod?->id,
                    'bank_account_id' => $bankAccount?->id,
                    'amount' => $selectedTagihan->amount,
                    'fee_amount' => 0,
                    'total_amount' => $selectedTagihan->amount,
                    'paid_amount' => $ocrAmount,
                    'status' => PaymentTransaction::STATUS_PROCESSING,
                    'payment_proof' => $localProofPath,
                    'is_manual' => true,
                    'notes' => 'Konfirmasi pembayaran otomatis melalui WhatsApp Bot. Pembacaan OCR sukses.',
                    'metadata' => [
                        'whatsapp_incoming' => true,
                        'sender_jid' => $parsedPayload['sender_jid'] ?? $parsedPayload['sender'],
                        'ocr_extracted' => $ocrData,
                        'payment_date' => $ocrData['date'] ?? now()->format('Y-m-d')
                    ]
                ]);

                // Create the manual verification entry
                $verificationData = [
                    'amount' => $ocrAmount,
                    'payment_date' => $ocrData['date'] ? Carbon::parse($ocrData['date']) : now(),
                    'sender_name' => $ocrData['sender_name'] ?? $mahasiswa->name,
                    'sender_account' => $ocrData['sender_account'] ?? null,
                    'bank_reference' => $ocrData['reference_number'] ?? null,
                    'notes' => 'Otomatis diunggah via WA. Struk berhasil dideteksi AI Gemini.',
                    'proof_file' => $localProofPath,
                ];

                $res = $this->verificationService->createManualVerification($transaction->id, $verificationData);

                if (!$res['success']) {
                    throw new Exception('Failed to insert verification queue: ' . $res['message']);
                }

                return [
                    'transaction' => $transaction,
                    'verification' => $res['verification']
                ];
            });

            Log::info('✅ Webhook flow completed successfully. Transaction queued for admin approval.', [
                'transaction_id' => $verificationResult['transaction']->id,
                'verification_code' => $verificationResult['verification']->verification_code
            ]);

            // 10. Send WA reply back to student
            $groupName = $selectedTagihan->group->name ?? 'Uang Kuliah';
            $amountFormatted = 'Rp ' . number_format($ocrAmount, 0, ',', '.');
            $replyMsg = "Halo {$mahasiswa->name},\n\n" .
                       "Konfirmasi pembayaran Anda sebesar *{$amountFormatted}* untuk tagihan *{$groupName}* " .
                       "telah berhasil diunggah ke SIAKAD (Kode Verifikasi: {$verificationResult['verification']->verification_code}).\n\n" .
                       "Saat ini sedang menunggu verifikasi akhir oleh petugas keuangan. Kami akan memberi tahu Anda setelah pembayaran Anda diverifikasi. Terima kasih!";
            
            $this->sendWhatsAppReply($parsedPayload['sender_jid'] ?? $parsedPayload['sender'], $replyMsg);


            return response()->json([
                'success' => true,
                'message' => 'Payment confirmation processed and verification queue created.',
                'verification_code' => $verificationResult['verification']->verification_code,
                'matched_student' => $mahasiswa->name,
                'extracted_amount' => $ocrAmount
            ]);

        } catch (Exception $e) {
            Log::error('❌ WhatsApp Webhook Processing Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse incoming webhook payload parameters
     */
    private function parsePayload(Request $request): array
    {
        $payload = $request->all();
        $isQuery = $payload['is_query'] ?? false;

        // 1. Check if Evolution API structure
        if (isset($payload['event']) && isset($payload['data']['key']['remoteJid'])) {
            $data = $payload['data'];
            $remoteJid = $data['key']['remoteJid'];
            $sender = explode('@', $remoteJid)[0]; // strip JID domain
            
            $text = '';
            $hasMedia = false;
            $mediaType = null;
            $mediaKey = null;
            $messageId = $data['key']['id'] ?? null;

            $message = $data['message'] ?? [];
            
            // Check text contents
            if (isset($message['conversation'])) {
                $text = $message['conversation'];
            } elseif (isset($message['extendedTextMessage']['text'])) {
                $text = $message['extendedTextMessage']['text'];
            }

            // Check image media content
            if (isset($message['imageMessage'])) {
                $hasMedia = true;
                $mediaType = 'image';
                $text = $message['imageMessage']['caption'] ?? $text;
            }

            // Evolution API sometimes includes mediaUrl or mediaBase64 in details
            $mediaUrl = $payload['data']['mediaUrl'] ?? $payload['data']['url'] ?? null;
            $mediaBase64 = $payload['data']['base64'] ?? null;

            return [
                'sender' => $sender,
                'sender_jid' => $remoteJid,
                'text' => $text,
                'has_media' => $hasMedia,
                'media_type' => $mediaType,
                'media_url' => $mediaUrl,
                'media_base64' => $mediaBase64,
                'evolution_message_id' => $messageId,
                'evolution_instance' => $payload['instance'] ?? null,
                'is_query' => $isQuery
            ];
        }

        // 2. Direct Webhook Format (Fonnte, Wablas, or Custom manual curl testing)
        $originalSender = $payload['sender'] ?? $payload['from'] ?? '';
        $sender = explode('@', $originalSender)[0]; // strip JID if present
        
        $text = $payload['message'] ?? $payload['text'] ?? $payload['caption'] ?? '';
        $mediaUrl = $payload['media_url'] ?? $payload['image_url'] ?? $payload['url'] ?? null;
        $mediaBase64 = $payload['media_base64'] ?? $payload['base64'] ?? null;
        
        $hasMedia = !empty($mediaUrl) || !empty($mediaBase64);

        return [
            'sender' => preg_replace('/[^0-9]/', '', $sender),
            'sender_jid' => $originalSender,
            'text' => $text,
            'has_media' => $hasMedia,
            'media_type' => $hasMedia ? 'image' : null,
            'media_url' => $mediaUrl,
            'media_base64' => $mediaBase64,
            'is_query' => $isQuery
        ];
    }

    /**
     * Identify Mahasiswa/Student based on phone number or message text containing NIM
     */
    private function findMahasiswa(string $senderPhone, string $messageText): ?Mahasantri
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        $localPhone = preg_replace('/^62/', '0', $cleanPhone);

        // 1. Prioritas Utama: Jika pesan mengandung NIM (angka 6-15 digit)
        if (!empty($messageText)) {
            preg_match('/\b[0-9]{6,15}\b/', $messageText, $matches);
            if (!empty($matches[0])) {
                $nim = $matches[0];
                Log::info("🔍 NIM detected in message text. Searching by NIM.", ['nim' => $nim]);
                
                $mahasiswa = Mahasantri::where('numb_nim', $nim)->first();
                if ($mahasiswa) {
                    // Check if PIN (last 4 digits of student's registered phone) is present in the message text to link the JID
                    $cleanDbPhone = preg_replace('/[^0-9]/', '', $mahasiswa->phone);
                    $pin = substr($cleanDbPhone, -4);
                    
                    if (strpos($messageText, $pin) !== false) {
                        if (empty($mahasiswa->whatsapp_jid)) {
                            $mahasiswa->update(['whatsapp_jid' => $cleanPhone]);
                            Log::info("🔗 Linked whatsapp_jid {$cleanPhone} to student {$mahasiswa->name} by NIM and PIN match");
                        } else {
                            Log::info("ℹ️ Student {$mahasiswa->name} already has linked JID ({$mahasiswa->whatsapp_jid}). Displaying info without overwriting.");
                        }
                    } else {
                        Log::info("ℹ️ Displaying info for {$mahasiswa->name} via NIM query without linking JID (no PIN or PIN mismatch).");
                    }
                    return $mahasiswa;
                }
            }
        }

        // 2. Prioritas Kedua: Jika tidak ada NIM di pesan, cari berdasarkan whatsapp_jid yang sudah tertaut
        Log::info('🔍 Searching student by linked phone/JID', [
            'wa_phone' => $cleanPhone,
            'local_phone' => $localPhone
        ]);

        $mahasiswa = Mahasantri::where('whatsapp_jid', $cleanPhone)->first();
        if ($mahasiswa) {
            Log::info("🎯 Student found directly via linked whatsapp_jid: {$mahasiswa->name}");
            return $mahasiswa;
        }

        // 3. Prioritas Ketiga: Jika tidak ada NIM dan tidak ada JID tertaut, coba cari nomor HP yang cocok di DB untuk otomatis ditautkan
        $mahasiswa = Mahasantri::where('phone', $cleanPhone)
            ->orWhere('phone', $localPhone)
            ->orWhere('phone', 'like', '%' . substr($cleanPhone, -9))
            ->first();

        if ($mahasiswa) {
            $mahasiswa->update(['whatsapp_jid' => $cleanPhone]);
            Log::info("🔗 Linked whatsapp_jid {$cleanPhone} to student {$mahasiswa->name} by phone match");
            return $mahasiswa;
        }

        return null;
    }

    /**
     * Download or retrieve binary image content from webhook payload
     */
    private function downloadMedia(array $payload): ?string
    {
        // 1. If base64 content is directly provided
        if (!empty($payload['media_base64'])) {
            Log::info('📄 Loading media directly from base64 payload data');
            return base64_decode($payload['media_base64']);
        }

        // 2. If a direct download URL is provided
        if (!empty($payload['media_url'])) {
            Log::info('🌐 Downloading image from media url', ['url' => $payload['media_url']]);
            $response = Http::timeout(20)->get($payload['media_url']);
            if ($response->successful()) {
                return $response->body();
            }
            Log::warning('⚠️ Direct media URL download failed: ' . $response->status());
        }

        // 3. Fallback for Evolution API: Fetch base64 via API if keys are set
        if (!empty($payload['evolution_message_id']) && !empty($payload['evolution_instance'])) {
            $gatewayUrl = env('WHATSAPP_GATEWAY_URL');
            $apiKey = env('WHATSAPP_GATEWAY_API_KEY');
            
            // Clean up sendText path to base Evolution URL if needed
            // e.g. from http://localhost:3000/message/sendText to http://localhost:3000
            $baseUrl = preg_replace('/\/message\/sendText.*/', '', $gatewayUrl);

            if (!empty($baseUrl) && !empty($apiKey)) {
                $apiUrl = "{$baseUrl}/message/getBase64FromMedia/{$payload['evolution_instance']}";
                Log::info('🔄 Evolution API: Requesting media base64 from endpoint', [
                    'url' => $apiUrl,
                    'message_id' => $payload['evolution_message_id']
                ]);

                $response = Http::timeout(25)
                    ->withHeaders(['apikey' => $apiKey])
                    ->post($apiUrl, [
                        'key' => [
                            'id' => $payload['evolution_message_id'],
                            'fromMe' => false,
                            'remoteJid' => $payload['sender'] . '@s.whatsapp.net'
                        ],
                        'convertToMp4' => false
                    ]);

                if ($response->successful()) {
                    $base64Data = $response->json('base64');
                    // Evolution API might return raw base64 or prefixed with data:image/jpeg;base64,
                    if (!empty($base64Data)) {
                        if (strpos($base64Data, 'base64,') !== false) {
                            $base64Data = explode('base64,', $base64Data)[1];
                        }
                        return base64_decode($base64Data);
                    }
                }
                Log::warning('⚠️ Evolution API getBase64FromMedia request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        }

        return null;
    }

    /**
     * Send automated reply message to WhatsApp student via Gateway
     */
    private function sendWhatsAppReply(string $recipient, string $message)
    {
        $gatewayUrl = config('services.whatsapp.url');
        $apiKey = config('services.whatsapp.api_key');
        $instance = config('services.whatsapp.instance', 'siakad_instance');

        if (empty($gatewayUrl)) {
            Log::info('📭 WA Gateway URL is not configured. Reply message log:', [
                'recipient' => $recipient,
                'message' => $message
            ]);
            return;
        }

        try {
            // Support Evolution API endpoint
            // Endpoint Evolution: /message/sendText/{instance}
            $apiUrl = $gatewayUrl;
            if (strpos($apiUrl, '{instance}') !== false) {
                $apiUrl = str_replace('{instance}', $instance, $apiUrl);
            } elseif (substr($apiUrl, -1) !== '/' && strpos($apiUrl, 'sendText') !== false && strpos($apiUrl, $instance) === false) {
                // If it ends with sendText, append instance
                $apiUrl = rtrim($apiUrl, '/') . '/' . $instance;
            }

            $headers = [];
            if (!empty($apiKey)) {
                $headers['apikey'] = $apiKey;
            }

            // 1. Calculate dynamic typing delay (20ms per character, capped between 1.5s and 3.0s)
            // Capped at 3.0 seconds to prevent webhook timeouts
            $typingDelayMs = min(3000, max(1500, strlen($message) * 20));

            // 2. Trigger "typing..." status if using Evolution API
            if (strpos($apiUrl, '/message/sendText') !== false) {
                $presenceUrl = str_replace('/message/sendText', '/chat/sendPresence', $apiUrl);
                try {
                    Http::timeout(5)
                        ->withHeaders($headers)
                        ->post($presenceUrl, [
                            'number' => $recipient,
                            'presence' => 'composing',
                            'delay' => $typingDelayMs
                        ]);
                    Log::info('⏳ WhatsApp typing status triggered', ['recipient' => $recipient, 'delay_ms' => $typingDelayMs]);
                } catch (Exception $presenceEx) {
                    Log::warning('⚠️ Failed to send presence status: ' . $presenceEx->getMessage());
                }
            }

            // 3. Sleep to simulate natural human typing time
            usleep($typingDelayMs * 1000);

            Log::info('📤 Sending WhatsApp reply through gateway', [
                'url' => $apiUrl,
                'recipient' => $recipient
            ]);

            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->post($apiUrl, [
                    'number' => $recipient,
                    'text' => $message
                ]);

            if ($response->failed()) {
                Log::error('❌ WhatsApp Gateway reply request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } else {
                Log::info('✅ WhatsApp reply sent successfully.');
            }

        } catch (Exception $e) {
            Log::error('❌ Exception sending WhatsApp reply: ' . $e->getMessage());
        }
    }
}
