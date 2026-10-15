<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentWebhookController;

// Simulator webhook endpoint
Route::post("/api/webhook/simulator", function(Illuminate\Http\Request $request) {
    try {
        $transactionCode = $request->input("transaction_code");
        
        if (!$transactionCode) {
            return response()->json(["error" => "Transaction code required"], 400);
        }
        
        $transaction = App\Models\Keuangan\PaymentTransaction::where("transaction_code", $transactionCode)->first();
        
        if (!$transaction) {
            return response()->json(["error" => "Transaction not found"], 404);
        }
        
        // Simulate successful payment
        $simulatedData = [
            "order_id" => $transaction->transaction_code,
            "transaction_status" => "settlement",
            "status_code" => "200",
            "gross_amount" => $transaction->total_amount,
            "payment_type" => "bank_transfer",
            "transaction_time" => now()->toISOString(),
            "signature_key" => "simulated_signature",
            "fraud_status" => "accept"
        ];
        
        // Process webhook
        $gatewayService = app(App\Services\PaymentGatewayService::class);
        $result = $gatewayService->handleWebhook($simulatedData);
        
        return response()->json([
            "success" => $result["success"],
            "message" => $result["message"],
            "transaction_code" => $transactionCode,
            "new_status" => $transaction->fresh()->status
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            "error" => $e->getMessage()
        ], 500);
    }
});
