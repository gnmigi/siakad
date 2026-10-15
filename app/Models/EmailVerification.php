<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'verification_code',
        'type',
        'expires_at',
        'verified_at',
        'is_used',
        'attempts',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_used' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Generate a new verification code
     */
    public static function generateCode($length = 6)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new verification record
     */
    public static function createVerification($email, $type = 'registration', $expiryMinutes = 10, $metadata = [])
    {
        // Delete any existing unused verifications for this email and type
        static::where('email', $email)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        $code = static::generateCode();
        
        // Fix timezone issue - use explicit timezone
        $now = Carbon::now('Asia/Jakarta');
        $expiresAt = $now->copy()->addMinutes($expiryMinutes);
        
        return static::create([
            'email' => $email,
            'verification_code' => Hash::make($code), // Store hashed code for security
            'type' => $type,
            'expires_at' => $expiresAt,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata
        ]);
    }

    /**
     * Verify a code
     */
    public function verifyCode($inputCode)
    {
        // Check if already used
        if ($this->is_used) {
            return ['success' => false, 'message' => 'Kode verifikasi sudah digunakan'];
        }

        // Check if expired
        if ($this->expires_at < Carbon::now()) {
            return ['success' => false, 'message' => 'Kode verifikasi sudah kadaluarsa'];
        }

        // Increment attempts
        $this->increment('attempts');

        // Check max attempts
        $maxAttempts = config('app.max_verification_attempts', 3);
        if ($this->attempts > $maxAttempts) {
            return ['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan minta kode baru'];
        }

        // Verify code
        if (Hash::check($inputCode, $this->verification_code)) {
            $this->update([
                'verified_at' => Carbon::now(),
                'is_used' => true
            ]);
            
            return ['success' => true, 'message' => 'Email berhasil diverifikasi'];
        }

        return ['success' => false, 'message' => 'Kode verifikasi tidak valid'];
    }

    /**
     * Check if verification is still valid
     */
    public function isValid()
    {
        return !$this->is_used && $this->expires_at > Carbon::now();
    }

    /**
     * Get time remaining in seconds
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->expires_at < Carbon::now()) {
            return 0;
        }
        
        return Carbon::now()->diffInSeconds($this->expires_at);
    }

    /**
     * Scope for active verifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope for specific email and type
     */
    public function scopeForEmail($query, $email, $type = 'registration')
    {
        return $query->where('email', $email)->where('type', $type);
    }

    /**
     * Clean up expired verifications
     */
    public static function cleanupExpired()
    {
        return static::where('expires_at', '<', Carbon::now()->subHours(24))->delete();
    }
}
