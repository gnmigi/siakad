<?php

namespace App\Models\Pengaturan;
// USE SYSTEM
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLogAktivitas;
// USE MODELS

class WebSetting extends Model
{
    use SoftDeletes, HasLogAktivitas;
    
    protected $table = 'web_settings';
    protected $guarded = [];

    protected $casts = [
        'email_verification_enabled' => 'boolean',
        'require_email_verification_pmb' => 'boolean',
        'verification_code_length' => 'integer',
        'verification_code_expiry_minutes' => 'integer',
        'max_verification_attempts' => 'integer',
        'wa_admin_payment_notification_enabled' => 'boolean',
        'wa_admin_krs_notification_enabled' => 'boolean',
    ];

    public function getSchoolLogoHoriAttribute($value)
    {
        if (!$value) {
            return asset('logo.png');
        }
        
        // Use url() helper instead of asset() to ensure correct domain/port
        $storagePath = 'storage/images/logo/' . ($value == 'logo-hori.png' ? 'logo-hori.png' : $value);
        return url($storagePath);
    }
    
    public function getSchoolLogoVertAttribute($value)
    {
        return $value == 'logo-vert.png' ? asset('storage/images/logo/logo-vert.png') : asset('storage/images/logo/' . $value);
    }
    
    public function getSigninBackgroundImageAttribute($value)
    {
        if (!$value) {
            // Default fallback image
            return asset('storage/images/website/DSCF6566.jpg');
        }
        return asset('storage/images/signin/' . $value);
    }

    /**
     * Check if email verification is enabled for PMB
     */
    public function isEmailVerificationEnabled()
    {
        return $this->email_verification_enabled && $this->require_email_verification_pmb;
    }

    /**
     * Get verification code settings
     */
    public function getVerificationSettings()
    {
        return [
            'enabled' => $this->email_verification_enabled,
            'pmb_required' => $this->require_email_verification_pmb,
            'code_length' => $this->verification_code_length ?? 6,
            'expiry_minutes' => $this->verification_code_expiry_minutes ?? 10,
            'max_attempts' => $this->max_verification_attempts ?? 3,
        ];
    }
}
