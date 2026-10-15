<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Get Turnstile secret key from config
        $secretKey = config('turnstile.secret_key') ?? env('TURNSTILE_SECRET_KEY');
        
        // If no secret key configured, skip validation
        if (empty($secretKey)) {
            Log::warning('Turnstile validation skipped: No secret key configured');
            return;
        }
        
        // If no response token provided, fail validation
        if (empty($value)) {
            $fail('Please complete the CAPTCHA verification.');
            return;
        }
        
        try {
            // Verify with Cloudflare Turnstile API
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
            
            $result = $response->json();
            
            // Check if verification was successful
            if (!isset($result['success']) || !$result['success']) {
                Log::warning('Turnstile verification failed', [
                    'response' => $result,
                    'ip' => request()->ip()
                ]);
                
                $fail('CAPTCHA verification failed. Please try again.');
                return;
            }
            
            Log::info('Turnstile verification successful', [
                'ip' => request()->ip()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Turnstile verification error: ' . $e->getMessage(), [
                'ip' => request()->ip(),
                'error' => $e->getTraceAsString()
            ]);
            
            // In case of API error, we can choose to pass or fail
            // For security, we'll fail the validation
            $fail('CAPTCHA verification service is temporarily unavailable. Please try again.');
        }
    }
}
