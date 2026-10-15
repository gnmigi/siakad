<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;

class PaymentMethod extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'payment_methods';
    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'auto_verify' => 'boolean',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'integer',
        'sort_order' => 'integer',
    ];

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutoVerify($query)
    {
        return $query->where('auto_verify', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // RELATIONSHIPS
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // METHODS
    public function calculateFee($amount)
    {
        $percentageFee = ($amount * $this->fee_percentage) / 100;
        return $percentageFee + $this->fee_fixed;
    }

    public function getTotalAmount($amount)
    {
        return $amount + $this->calculateFee($amount);
    }

    public function isDigital()
    {
        // Only gateway-based payment methods are considered digital
        // bank_transfer can be either manual or gateway-based, but we need to check the provider
        if ($this->type === 'bank_transfer') {
            // If provider is 'midtrans' or similar, it's digital; otherwise it's manual
            return in_array($this->provider, ['midtrans', 'gateway', 'online']);
        }

        return in_array($this->type, ['e_wallet', 'virtual_account', 'credit_card', 'midtrans_snap']);
    }

    public function isManual()
    {
        return $this->type === 'cash' || $this->provider === 'manual';
    }

    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Default icons based on type
        $defaultIcons = [
            'bank_transfer' => 'fas fa-university',
            'virtual_account' => 'fas fa-credit-card',
            'e_wallet' => 'fas fa-mobile-alt',
            'credit_card' => 'fas fa-credit-card',
            'cash' => 'fas fa-money-bill-wave'
        ];

        return $defaultIcons[$this->type] ?? 'fas fa-payment';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Aktif</span>' 
            : '<span class="badge bg-danger">Nonaktif</span>';
    }

    public function getAutoVerifyBadgeAttribute()
    {
        return $this->auto_verify 
            ? '<span class="badge bg-info">Auto</span>' 
            : '<span class="badge bg-warning">Manual</span>';
    }
}
