<?php

namespace App\Services;

use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentReceipt;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptGenerationService
{
    /**
     * Generate receipt for payment transaction
     */
    public function generateReceipt(PaymentTransaction $transaction, $template = 'default')
    {
        try {
            // Create or get existing receipt
            $receipt = PaymentReceipt::where('payment_transaction_id', $transaction->id)->first();
            
            if (!$receipt) {
                $receipt = PaymentReceipt::createFromTransaction($transaction);
            }

            // Generate PDF if not exists or needs regeneration
            if (!$receipt->pdf_file || $receipt->isDraft()) {
                $pdfFile = $this->generatePDF($receipt, $template);
                $receipt->markAsGenerated($pdfFile);
            }

            return [
                'success' => true,
                'receipt' => $receipt,
                'pdf_url' => $receipt->pdf_url
            ];

        } catch (Exception $e) {
            Log::error('Receipt Generation Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate PDF receipt
     */
    private function generatePDF(PaymentReceipt $receipt, $template = 'default')
    {
        $transaction = $receipt->paymentTransaction;
        $mahasiswa = $receipt->mahasiswa;
        $tagihan = $receipt->tagihan;

        // Prepare data for PDF
        $data = [
            'receipt' => $receipt,
            'transaction' => $transaction,
            'mahasiswa' => $mahasiswa,
            'tagihan' => $tagihan,
            'institution' => $this->getInstitutionData(),
            'generated_at' => now(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView("receipts.templates.{$template}", $data);
        $pdf->setPaper('A4', 'portrait');

        // Save PDF file
        $fileName = 'receipt_' . $receipt->receipt_code . '.pdf';
        $filePath = 'receipts/' . $fileName;
        
        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Send receipt via email
     */
    public function sendReceiptByEmail(PaymentReceipt $receipt, $email = null)
    {
        try {
            $email = $email ?: $receipt->mahasiswa->email;
            
            if (!$email) {
                throw new Exception('Email address not provided');
            }

            // Generate PDF if not exists
            if (!$receipt->pdf_file) {
                $this->generateReceipt($receipt->paymentTransaction);
                $receipt->refresh();
            }

            // Send email (implement your email service here)
            // Mail::to($email)->send(new PaymentReceiptMail($receipt));

            $receipt->markAsSent($email);

            return [
                'success' => true,
                'message' => 'Receipt sent successfully',
                'receipt' => $receipt
            ];

        } catch (Exception $e) {
            Log::error('Receipt Email Error: ' . $e->getMessage(), [
                'receipt_id' => $receipt->id,
                'email' => $email,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send receipt via WhatsApp
     */
    public function sendReceiptByWhatsApp(PaymentReceipt $receipt, $whatsapp = null)
    {
        try {
            $whatsapp = $whatsapp ?: $receipt->mahasiswa->phone;
            
            if (!$whatsapp) {
                throw new Exception('WhatsApp number not provided');
            }

            // Generate PDF if not exists
            if (!$receipt->pdf_file) {
                $this->generateReceipt($receipt->paymentTransaction);
                $receipt->refresh();
            }

            // Send WhatsApp message (implement your WhatsApp service here)
            $message = $this->generateWhatsAppMessage($receipt);
            // WhatsAppService::send($whatsapp, $message, $receipt->pdf_url);

            $receipt->markAsSent(null, $whatsapp);

            return [
                'success' => true,
                'message' => 'Receipt sent via WhatsApp successfully',
                'receipt' => $receipt
            ];

        } catch (Exception $e) {
            Log::error('Receipt WhatsApp Error: ' . $e->getMessage(), [
                'receipt_id' => $receipt->id,
                'whatsapp' => $whatsapp,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Download receipt
     */
    public function downloadReceipt(PaymentReceipt $receipt)
    {
        try {
            // Generate PDF if not exists
            if (!$receipt->pdf_file) {
                $this->generateReceipt($receipt->paymentTransaction);
                $receipt->refresh();
            }

            // Mark as downloaded
            $receipt->markAsDownloaded();

            return [
                'success' => true,
                'file_path' => $receipt->pdf_file,
                'file_url' => $receipt->pdf_url,
                'receipt' => $receipt
            ];

        } catch (Exception $e) {
            Log::error('Receipt Download Error: ' . $e->getMessage(), [
                'receipt_id' => $receipt->id,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Bulk generate receipts
     */
    public function bulkGenerateReceipts($transactionIds, $template = 'default')
    {
        $results = [
            'success' => [],
            'failed' => [],
            'total' => count($transactionIds)
        ];

        foreach ($transactionIds as $transactionId) {
            try {
                $transaction = PaymentTransaction::findOrFail($transactionId);
                $result = $this->generateReceipt($transaction, $template);
                
                if ($result['success']) {
                    $results['success'][] = $transactionId;
                } else {
                    $results['failed'][] = [
                        'id' => $transactionId,
                        'message' => $result['message']
                    ];
                }
            } catch (Exception $e) {
                $results['failed'][] = [
                    'id' => $transactionId,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get available receipt templates
     */
    public function getAvailableTemplates()
    {
        return [
            'default' => 'Template Default',
            'formal' => 'Template Formal',
            'simple' => 'Template Sederhana',
            'modern' => 'Template Modern'
        ];
    }

    /**
     * Preview receipt template
     */
    public function previewTemplate($template, PaymentTransaction $sampleTransaction = null)
    {
        try {
            // Use sample data if no transaction provided
            if (!$sampleTransaction) {
                $sampleTransaction = $this->createSampleTransaction();
            }

            $receipt = PaymentReceipt::createFromTransaction($sampleTransaction);
            
            $data = [
                'receipt' => $receipt,
                'transaction' => $sampleTransaction,
                'mahasiswa' => $sampleTransaction->mahasiswa,
                'tagihan' => $sampleTransaction->tagihan,
                'institution' => $this->getInstitutionData(),
                'generated_at' => now(),
                'is_preview' => true
            ];

            return view("receipts.templates.{$template}", $data)->render();

        } catch (Exception $e) {
            return '<div class="alert alert-danger">Error loading template: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Get institution data for receipt
     */
    private function getInstitutionData()
    {
        return [
            'name' => config('app.institution_name', 'Institusi Pendidikan'),
            'address' => config('app.institution_address', 'Alamat Institusi'),
            'phone' => config('app.institution_phone', '021-12345678'),
            'email' => config('app.institution_email', 'info@institusi.ac.id'),
            'website' => config('app.url'),
            'logo' => asset('images/logo.png')
        ];
    }

    /**
     * Generate WhatsApp message
     */
    private function generateWhatsAppMessage(PaymentReceipt $receipt)
    {
        $mahasiswa = $receipt->mahasiswa;
        $transaction = $receipt->paymentTransaction;

        return "🧾 *KWITANSI PEMBAYARAN*\n\n" .
               "Yth. {$mahasiswa->name}\n" .
               "NIM: {$mahasiswa->numb_nim}\n\n" .
               "Pembayaran Anda telah berhasil diverifikasi:\n" .
               "📋 Kode: {$receipt->receipt_code}\n" .
               "💰 Jumlah: {$receipt->getFormattedAmount()}\n" .
               "📅 Tanggal: {$receipt->created_at->format('d/m/Y H:i')}\n\n" .
               "Kwitansi digital dapat diunduh melalui link berikut:\n" .
               "{$receipt->pdf_url}\n\n" .
               "Terima kasih atas pembayaran Anda.\n\n" .
               "_Pesan ini dikirim otomatis oleh sistem._";
    }

    /**
     * Create sample transaction for preview
     */
    private function createSampleTransaction()
    {
        // This would create a sample transaction for preview purposes
        // Implementation depends on your specific needs
        return new PaymentTransaction([
            'transaction_code' => 'TRX20250131SAMPLE',
            'invoice_number' => 'INV20250131001',
            'amount' => 1000000,
            'fee_amount' => 4000,
            'total_amount' => 1004000,
            'status' => 'verified',
            'created_at' => now(),
        ]);
    }
}
