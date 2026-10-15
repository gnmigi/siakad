<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    public function testPaymentEndpoint(Request $request)
    {
        Log::info('Debug payment endpoint called', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'data' => $request->all(),
            'session_token' => $request->session()->token(),
            'csrf_token' => csrf_token()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Debug endpoint working',
            'csrf_token' => csrf_token(),
            'session_id' => $request->session()->getId(),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    public function getSessionInfo(Request $request)
    {
        return response()->json([
            'session_id' => $request->session()->getId(),
            'csrf_token' => csrf_token(),
            'session_token' => $request->session()->token(),
            'session_started' => $request->session()->isStarted(),
            'environment' => app()->environment(),
            'timestamp' => now()->toISOString()
        ]);
    }
}