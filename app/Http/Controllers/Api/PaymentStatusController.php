<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function checkStatus(Request $request)
    {
        $transactionCode = $request->input("transaction_code");
        
        if (!$transactionCode) {
            return response()->json(["error" => "Transaction code required"], 400);
        }
        
        $transaction = PaymentTransaction::where("transaction_code", $transactionCode)
            ->with(["mahasiswa", "tagihan", "paymentMethod", "receipts"])
            ->first();
        
        if (!$transaction) {
            return response()->json(["error" => "Transaction not found"], 404);
        }
        
        return response()->json([
            "transaction_code" => $transaction->transaction_code,
            "status" => $transaction->status,
            "amount" => $transaction->total_amount,
            "created_at" => $transaction->created_at,
            "verified_at" => $transaction->verified_at,
            "mahasiswa" => $transaction->mahasiswa->name ?? null,
            "tagihan" => $transaction->tagihan->name ?? null,
            "payment_method" => $transaction->paymentMethod->name ?? null,
            "has_receipt" => $transaction->receipts->count() > 0,
            "receipt_code" => $transaction->receipts->first()->receipt_code ?? null
        ]);
    }
    
    public function systemStatus()
    {
        $stats = [
            "total_transactions" => PaymentTransaction::count(),
            "pending_transactions" => PaymentTransaction::where("status", "pending")->count(),
            "verified_transactions" => PaymentTransaction::where("status", "verified")->count(),
            "failed_transactions" => PaymentTransaction::where("status", "failed")->count(),
            "last_auto_verify" => "Check logs for last run",
            "webhook_url" => config("app.url") . "/api/webhook/midtrans",
            "system_time" => now()->toISOString()
        ];
        
        return response()->json($stats);
    }
}