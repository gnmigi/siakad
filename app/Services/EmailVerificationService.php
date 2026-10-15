<?php

namespace App\Services;

use App\Models\EmailVerification;
use App\Models\Pengaturan\WebSetting;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class EmailVerificationService
{
    protected $notificationService;
    protected $webSettings;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->webSettings = WebSetting::first();
    }

    /**
     * Check if email verification is enabled
     */
    public function isEnabled()
    {
        return $this->webSettings && $this->webSettings->isEmailVerificationEnabled();
    }

    /**
     * Send verification code to email
     */
    public function sendVerificationCode($email, $type = 'registration', $metadata = [])
    {
        try {
            // Check if email verification is enabled
            if (!$this->isEnabled()) {
                return [
                    'success' => false,
                    'message' => 'Email verification is not enabled'
                ];
            }

            // Rate limiting - max 3 requests per 5 minutes per email
            $rateLimitKey = 'email_verification:' . $email;
            if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
                $seconds = RateLimiter::availableIn($rateLimitKey);
                return [
                    'success' => false,
                    'message' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik."
                ];
            }

            // Get verification settings
            $settings = $this->webSettings->getVerificationSettings();
            
            // Generate plain code for sending
            $plainCode = EmailVerification::generateCode($settings['code_length']);
            
            // Create verification record with proper expiry time
            $verification = EmailVerification::create([
                'email' => $email,
                'verification_code' => bcrypt($plainCode),
                'type' => $type,
                'expires_at' => Carbon::now()->addMinutes($settings['expiry_minutes']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $metadata
            ]);

            // Send email
            $emailSent = $this->notificationService->sendVerificationEmail($email, $plainCode, $settings['expiry_minutes'], $type, $metadata);

            if ($emailSent) {
                // Hit rate limiter
                RateLimiter::hit($rateLimitKey, 300); // 5 minutes

                Log::info('Verification code sent', [
                    'email' => $email,
                    'type' => $type,
                    'verification_id' => $verification->id
                ]);

                return [
                    'success' => true,
                    'message' => 'Kode verifikasi telah dikirim ke email Anda',
                    'verification_id' => $verification->id,
                    'expires_in_minutes' => $settings['expiry_minutes']
                ];
            } else {
                // Delete verification record if email failed
                $verification->delete();
                
                return [
                    'success' => false,
                    'message' => 'Gagal mengirim email. Silakan coba lagi.'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Email verification send error: ' . $e->getMessage(), [
                'email' => $email,
                'type' => $type,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Verify the code
     */
    public function verifyCode($email, $code, $type = 'registration')
    {
        try {
            // Find active verification
            $verification = EmailVerification::forEmail($email, $type)
                ->active()
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$verification) {
                return [
                    'success' => false,
                    'message' => 'Kode verifikasi tidak ditemukan atau sudah kadaluarsa'
                ];
            }

            // Verify the code
            $result = $verification->verifyCode($code);

            if ($result['success']) {
                Log::info('Email verification successful', [
                    'email' => $email,
                    'type' => $type,
                    'verification_id' => $verification->id
                ]);
            } else {
                Log::warning('Email verification failed', [
                    'email' => $email,
                    'type' => $type,
                    'verification_id' => $verification->id,
                    'attempts' => $verification->attempts,
                    'reason' => $result['message']
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage(), [
                'email' => $email,
                'type' => $type,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Resend verification code
     */
    public function resendCode($email, $type = 'registration', $metadata = [])
    {
        // Check rate limiting for resend - max 2 resends per 10 minutes
        $rateLimitKey = 'email_verification_resend:' . $email;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 2)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return [
                'success' => false,
                'message' => "Terlalu banyak permintaan kirim ulang. Coba lagi dalam " . ceil($seconds/60) . " menit."
            ];
        }

        // Send new code
        $result = $this->sendVerificationCode($email, $type, $metadata);
        
        if ($result['success']) {
            // Hit resend rate limiter
            RateLimiter::hit($rateLimitKey, 600); // 10 minutes
        }

        return $result;
    }

    /**
     * Check if email is already verified
     */
    public function isEmailVerified($email, $type = 'registration')
    {
        return EmailVerification::forEmail($email, $type)
            ->where('is_used', true)
            ->where('verified_at', '>', Carbon::now()->subHours(24))
            ->exists();
    }

    /**
     * Get verification status
     */
    public function getVerificationStatus($email, $type = 'registration')
    {
        $verification = EmailVerification::forEmail($email, $type)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$verification) {
            return [
                'status' => 'not_found',
                'message' => 'Belum ada verifikasi untuk email ini'
            ];
        }

        if ($verification->is_used) {
            return [
                'status' => 'verified',
                'message' => 'Email sudah diverifikasi',
                'verified_at' => $verification->verified_at
            ];
        }

        if ($verification->expires_at < Carbon::now()) {
            return [
                'status' => 'expired',
                'message' => 'Kode verifikasi sudah kadaluarsa'
            ];
        }

        return [
            'status' => 'pending',
            'message' => 'Menunggu verifikasi',
            'expires_at' => $verification->expires_at,
            'time_remaining' => $verification->time_remaining,
            'attempts' => $verification->attempts,
            'max_attempts' => $this->webSettings->max_verification_attempts ?? 3
        ];
    }

    /**
     * Clean up old verifications
     */
    public function cleanup()
    {
        return EmailVerification::cleanupExpired();
    }
}
