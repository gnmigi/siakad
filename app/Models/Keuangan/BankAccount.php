<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;

class BankAccount extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'bank_accounts';
    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByBank($query, $bankCode)
    {
        return $query->where('bank_code', $bankCode);
    }

    // RELATIONSHIPS
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // METHODS
    public function getFormattedAccountNumber()
    {
        // Format account number with spaces for better readability
        return chunk_split($this->account_number, 4, ' ');
    }

    public function getMaskedAccountNumber()
    {
        // Mask account number for security (show only last 4 digits)
        $length = strlen($this->account_number);
        if ($length <= 4) {
            return $this->account_number;
        }
        
        return str_repeat('*', $length - 4) . substr($this->account_number, -4);
    }

    public function getFullBankName()
    {
        return $this->bank_name . ($this->branch ? ' - ' . $this->branch : '');
    }

    public function generateVirtualAccount($mahasiswaId)
    {
        if (!$this->virtual_account_prefix) {
            return null;
        }

        // Generate VA number: prefix + padded mahasiswa ID
        $paddedId = str_pad($mahasiswaId, 8, '0', STR_PAD_LEFT);
        return $this->virtual_account_prefix . $paddedId;
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Aktif</span>' 
            : '<span class="badge bg-danger">Nonaktif</span>';
    }

    public function getPrimaryBadgeAttribute()
    {
        return $this->is_primary 
            ? '<span class="badge bg-primary">Utama</span>' 
            : '<span class="badge bg-secondary">Alternatif</span>';
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        // Default bank logos
        $defaultLogos = [
            'BCA' => asset('images/banks/bca.png'),
            'MANDIRI' => asset('images/banks/mandiri.png'),
            'BNI' => asset('images/banks/bni.png'),
            'BRI' => asset('images/banks/bri.png'),
            'CIMB' => asset('images/banks/cimb.png'),
            'DANAMON' => asset('images/banks/danamon.png'),
        ];

        return $defaultLogos[$this->bank_code] ?? asset('images/banks/default.png');
    }

    // STATIC METHODS
    public static function getPrimaryAccount()
    {
        return static::where('is_primary', true)->where('is_active', true)->first();
    }

    public static function getActiveAccounts()
    {
        return static::where('is_active', true)->orderBy('is_primary', 'desc')->get();
    }
}
