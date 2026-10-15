<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiOcrService
{
    protected $apiKeys = [];
    protected $model;

    public function __construct()
    {
        $rawKey = config('services.gemini.api_key');
        // Split by comma to support multiple rotated keys
        $this->apiKeys = array_filter(array_map('trim', explode(',', $rawKey)));
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Extract payment receipt details from image content using Gemini API
     *
     * @param string $imageContent Binary image content of the receipt
     * @param string $mimeType Mime type of the image (e.g. image/jpeg, image/png)
     * @return array
     */
    public function extractReceiptDetails(string $imageContent, string $mimeType = 'image/jpeg'): array
    {
        if (empty($this->apiKeys)) {
            Log::error('❌ Gemini API Key is not set in environment variables (.env)');
            return [
                'success' => false,
                'message' => 'Gemini API Key is not configured'
            ];
        }

        // Define fallback models: if primary is 2.5-flash, fallback is 1.5-flash, and vice-versa
        $primaryModel = $this->model;
        $fallbackModel = ($primaryModel === 'gemini-2.5-flash') ? 'gemini-1.5-flash' : 'gemini-2.5-flash';
        $modelsToTry = [$primaryModel, $fallbackModel];

        $lastError = 'Unknown error';

        foreach ($this->apiKeys as $keyIndex => $apiKey) {
            foreach ($modelsToTry as $modelName) {
                try {
                    $base64Image = base64_encode($imageContent);

                    Log::info("🤖 Sending receipt image to Gemini API...", [
                        'key_index' => $keyIndex,
                        'model' => $modelName,
                        'mime_type' => $mimeType,
                        'size_bytes' => strlen($imageContent)
                    ]);

                    // Construct prompt for structured OCR extraction
                    $prompt = "Analyze this bank transfer receipt image (typically from BCA Mobile, Livin by Mandiri, BRImo, BNI Mobile, QRIS, etc.). " .
                              "Read and extract the transfer transaction details. " .
                              "You must respond ONLY with a raw JSON object containing the following keys (do not include markdown wrapping or ```json blocks): " .
                              "- amount (integer: total amount transferred in IDR, remove any formatting like dots/commas/Rp) " .
                              "- date (string: transfer transaction date formatted as YYYY-MM-DD, or null if not found) " .
                              "- sender_name (string: name of the sender, or null if not found) " .
                              "- sender_bank (string: name of sender's bank, e.g. BCA, MANDIRI, BRI, or null if not found) " .
                              "- reference_number (string: transaction reference/reference ID/journal number, or null if not found) " .
                              "- is_success (boolean: true if the status shown is successful/berhasil/selesai, false otherwise)";

                    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

                    $response = Http::timeout(30)->post($apiUrl, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    [
                                        'inlineData' => [
                                            'mimeType' => $mimeType,
                                            'data' => $base64Image
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json'
                        ]
                    ]);

                    if ($response->failed()) {
                        Log::warning("⚠️ Gemini API request failed with Key index: {$keyIndex}, Model: {$modelName}", [
                            'status' => $response->status(),
                            'error' => $response->body()
                        ]);
                        $lastError = "Model {$modelName} failed with status: " . $response->status();
                        continue; // Try next model or next key
                    }

                    $result = $response->json();
                    $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (empty($textResponse)) {
                        Log::warning("⚠️ Empty response from Gemini API with Key index: {$keyIndex}, Model: {$modelName}");
                        $lastError = "Model {$modelName} returned empty response";
                        continue; // Try next model or next key
                    }

                    // Clean response (in case Gemini wraps it in markdown block even though responseMimeType is set)
                    $cleanedJson = trim($textResponse);
                    if (strpos($cleanedJson, '```json') === 0) {
                        $cleanedJson = substr($cleanedJson, 7);
                        if (substr($cleanedJson, -3) === '```') {
                            $cleanedJson = substr($cleanedJson, 0, -3);
                        }
                        $cleanedJson = trim($cleanedJson);
                    } elseif (strpos($cleanedJson, '```') === 0) {
                        $cleanedJson = substr($cleanedJson, 3);
                        if (substr($cleanedJson, -3) === '```') {
                            $cleanedJson = substr($cleanedJson, 0, -3);
                        }
                        $cleanedJson = trim($cleanedJson);
                    }

                    $extractedData = json_decode($cleanedJson, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning("⚠️ Failed to decode Gemini JSON output with Key index: {$keyIndex}, Model: {$modelName}", [
                            'raw_text' => $textResponse,
                            'error' => json_last_error_msg()
                        ]);
                        $lastError = "Model {$modelName} returned invalid JSON: " . json_last_error_msg();
                        continue; // Try next model or next key
                    }

                    Log::info('✅ Gemini OCR successfully extracted receipt details', [
                        'amount' => $extractedData['amount'] ?? null,
                        'date' => $extractedData['date'] ?? null,
                        'sender_name' => $extractedData['sender_name'] ?? null,
                        'is_success' => $extractedData['is_success'] ?? null
                    ]);

                    return [
                        'success' => true,
                        'data' => $extractedData
                    ];

                } catch (Exception $e) {
                    Log::warning("⚠️ Exception in GeminiOcrService with Key index: {$keyIndex}, Model: {$modelName}: " . $e->getMessage());
                    $lastError = $e->getMessage();
                    continue; // Try next model or next key
                }
            }
        }

        // If we reach here, all configured keys and models failed
        Log::error("❌ All configured Gemini API keys and models failed. Last error: {$lastError}");
        return [
            'success' => false,
            'message' => 'OCR extraction failed: ' . $lastError
        ];
    }
}
