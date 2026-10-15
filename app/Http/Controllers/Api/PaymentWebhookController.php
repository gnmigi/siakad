<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $gatewayService;

    public function __construct(PaymentGatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function midtrans(Request $request)
    {
        try {
            Log::info("🔔 Midtrans Webhook Received", [
                "ip" => $request->ip(),
                "user_agent" => $request->userAgent(),
                "content_type" => $request->header('Content-Type'),
                "body_size" => strlen($request->getContent()),
                "has_signature" => $request->has('signature_key')
            ]);

            $notification = $request->all();
            
            // Validate required fields with better error messages
            $requiredFields = ["order_id", "transaction_status", "status_code", "gross_amount"];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($notification[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                Log::warning("❌ Invalid webhook notification - missing required fields", [
                    "missing_fields" => $missingFields,
                    "received_fields" => array_keys($notification)
                ]);
                return response()->json([
                    "status" => "error",
                    "message" => "Missing required fields: " . implode(", ", $missingFields),
                    "error_code" => "INVALID_WEBHOOK_DATA"
                ], 400);
            }

            // Enhanced logging for debugging
            Log::info("📋 Webhook data validated", [
                "order_id" => $notification["order_id"],
                "transaction_status" => $notification["transaction_status"],
                "status_code" => $notification["status_code"],
                "gross_amount" => $notification["gross_amount"],
                "payment_type" => $notification["payment_type"] ?? "unknown",
                "fraud_status" => $notification["fraud_status"] ?? null
            ]);
            
            // Process webhook through gateway service
            $result = $this->gatewayService->handleWebhook($notification);

            if ($result["success"]) {
                Log::info("✅ Midtrans Webhook Processed Successfully", [
                    "order_id" => $notification["order_id"],
                    "transaction_status" => $notification["transaction_status"],
                    "new_status" => $result["transaction"]->status ?? null,
                    "payment_method" => $result["transaction"]->paymentMethod->name ?? 'Unknown',
                    "verification_created" => isset($result["verification"])
                ]);

                return response()->json([
                    "status" => "success",
                    "message" => "Webhook processed successfully",
                    "order_id" => $notification["order_id"],
                    "transaction_status" => $result["transaction"]->status ?? null
                ], 200);
            }

            // Categorize error for better handling
            $errorType = $this->categorizeWebhookError($result["message"]);
            $httpStatus = $this->getHttpStatusForWebhookError($errorType);

            Log::warning("⚠️ Midtrans Webhook Processing Failed", [
                "order_id" => $notification["order_id"],
                "error" => $result["message"],
                "error_type" => $errorType,
                "http_status" => $httpStatus
            ]);

            return response()->json([
                "status" => "error",
                "message" => $result["message"],
                "error_type" => $errorType,
                "order_id" => $notification["order_id"]
            ], $httpStatus);

        } catch (\Throwable $e) {
            Log::error("❌ Midtrans Webhook Critical Error", [
                "error" => $e->getMessage(),
                "error_class" => get_class($e),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
                "trace" => $e->getTraceAsString(),
                "request_data" => $request->all()
            ]);

            return response()->json([
                "status" => "error",
                "message" => "Internal server error",
                "error_code" => "WEBHOOK_PROCESSING_ERROR"
            ], 500);
        }
    }

    /**
     * Categorize webhook error for better handling
     */
    private function categorizeWebhookError($message)
    {
        if (strpos($message, 'signature') !== false) {
            return 'signature_validation_failed';
        }
        if (strpos($message, 'not found') !== false) {
            return 'transaction_not_found';
        }
        if (strpos($message, 'already') !== false) {
            return 'already_processed';
        }
        if (strpos($message, 'invalid') !== false) {
            return 'invalid_data';
        }
        return 'unknown_error';
    }

    /**
     * Get appropriate HTTP status for webhook error type
     */
    private function getHttpStatusForWebhookError($errorType)
    {
        switch ($errorType) {
            case 'signature_validation_failed':
                return 401; // Unauthorized
            case 'transaction_not_found':
                return 404; // Not Found
            case 'already_processed':
                return 200; // OK - not really an error
            case 'invalid_data':
                return 400; // Bad Request
            default:
                return 400; // Bad Request
        }
    }

    /**
     * Handle payment callback (redirect after payment)
     */
    public function callback(Request $request)
    {
        try {
            $orderId = $request->get("order_id");
            $statusCode = $request->get("status_code");
            $transactionStatus = $request->get("transaction_status");

            Log::info("Payment Callback Received", [
                "order_id" => $orderId,
                "status_code" => $statusCode,
                "transaction_status" => $transactionStatus
            ]);

            // Redirect to payment result page
            if ($transactionStatus === "settlement" || $transactionStatus === "capture") {
                return redirect()->route("mahasiswa.payment.success")
                    ->with("success", "Pembayaran berhasil! Terima kasih.");
            } elseif ($transactionStatus === "pending") {
                return redirect()->route("mahasiswa.payment.pending")
                    ->with("info", "Pembayaran sedang diproses. Silakan tunggu konfirmasi.");
            } else {
                return redirect()->route("mahasiswa.payment.failed")
                    ->with("error", "Pembayaran gagal. Silakan coba lagi.");
            }

        } catch (\Exception $e) {
            Log::error("Payment Callback Error", [
                "error" => $e->getMessage(),
                "request" => $request->all()
            ]);

            return redirect()->route("mahasiswa.payment.index")
                ->with("error", "Terjadi kesalahan. Silakan periksa status pembayaran Anda.");
        }
    }
    
    /**
     * Manual sync endpoint for testing and force verification
     */
    public function manualSync(Request $request)
    {
        try {
            $transactionCode = $request->input("transaction_code");
            
            if (!$transactionCode) {
                Log::warning("❌ Manual sync called without transaction code");
                return response()->json(["error" => "Transaction code required"], 400);
            }
            
            Log::info("🔄 Manual sync initiated", [
                "transaction_code" => $transactionCode,
                "ip" => $request->ip()
            ]);
            
            // Get payment status from Midtrans
            $result = $this->gatewayService->checkPaymentStatus($transactionCode);
            
            if ($result["success"]) {
                $midtransData = $result["data"];
                
                Log::info("📊 Midtrans status retrieved", [
                    "transaction_code" => $transactionCode,
                    "midtrans_status" => $midtransData["transaction_status"],
                    "is_simulated" => $midtransData["simulated"] ?? false
                ]);
                
                // For ANY status that indicates payment completion, trigger webhook processing
                if (in_array($midtransData["transaction_status"], ["settlement", "capture"]) || 
                    isset($midtransData["auto_verified"])) {
                    
                    $webhookData = array_merge($midtransData, [
                        "signature_key" => "manual_sync_" . time()
                    ]);
                    
                    $webhookResult = $this->gatewayService->handleWebhook($webhookData);
                    
                    Log::info("🚀 Manual sync webhook processed", [
                        "transaction_code" => $transactionCode,
                        "webhook_success" => $webhookResult["success"],
                        "final_status" => $webhookResult["transaction"]->status ?? null
                    ]);
                    
                    return response()->json([
                        "success" => $webhookResult["success"],
                        "message" => $webhookResult["message"],
                        "midtrans_status" => $midtransData["transaction_status"],
                        "system_status" => $webhookResult["transaction"]->status ?? null,
                        "verified" => $webhookResult["transaction"]->status === 'verified'
                    ]);
                }
                
                return response()->json([
                    "success" => true,
                    "message" => "Transaction status checked - no action needed",
                    "midtrans_status" => $midtransData["transaction_status"],
                    "note" => "Payment not yet settled"
                ]);
            }
            
            Log::warning("⚠️ Manual sync failed to get status", [
                "transaction_code" => $transactionCode,
                "error" => $result["message"]
            ]);
            
            return response()->json([
                "success" => false,
                "message" => $result["message"]
            ], 400);
            
        } catch (\Exception $e) {
            Log::error("❌ Manual sync error", [
                "transaction_code" => $request->input("transaction_code"),
                "error" => $e->getMessage()
            ]);
            
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
