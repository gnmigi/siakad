<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FixCsrfForProduction
{
    public function handle(Request $request, Closure $next)
    {
        // Enhanced CSRF fix for production environment
        if (app()->environment('production')) {
            // Regenerate CSRF token if missing or invalid
            if (!$request->session()->token() || !$request->session()->has('_token')) {
                $request->session()->regenerateToken();
                Log::info('CSRF token regenerated for production', [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            // For AJAX requests, ensure proper headers
            if ($request->ajax() || $request->wantsJson()) {
                $request->headers->set('X-Requested-With', 'XMLHttpRequest');
                
                // Add CSRF token to request if missing
                if (!$request->header('X-CSRF-TOKEN') && !$request->input('_token')) {
                    $request->merge(['_token' => $request->session()->token()]);
                }
            }
            
            // For payment-related requests, add extra validation
            if (str_contains($request->path(), 'payment') || str_contains($request->path(), 'pembayaran')) {
                // Ensure session is active
                if (!$request->session()->isStarted()) {
                    $request->session()->start();
                }
                
                // Log payment request for debugging
                Log::info('Payment request processed', [
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'has_token' => !empty($request->session()->token()),
                    'session_id' => $request->session()->getId()
                ]);
            }
        }
        
        return $next($request);
    }
}