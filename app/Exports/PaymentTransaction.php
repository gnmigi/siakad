<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\Mahasantri;
use App\Models\User;

class PaymentTransaction extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'payment_transactions';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'fee_amount' => 'integer',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'expired_at' => 'datetime',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    // CONSTANTS
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAID = 'paid';
    const STATUS_VERIFIED = 'verified';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_PAID,
            self::STATUS_VERIFIED
        ]);
    }

    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    // RELATIONSHIPS
    public function tagihan()
    {
        return $this->belongsTo(TagihanKuliah::class, 'tagihan_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class, 'mahasiswa_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function verifications()
    {
        return $this->hasMany(PaymentVerification::class);
    }

    public function receipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function riwayatPembayaran()
    {
        return $this->hasOne(RiwayatPembayaran::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // METHODS
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function isActive()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_PAID,
            self::STATUS_VERIFIED
        ]);
    }

    public function canBePaid()
    {
        return $this->status === self::STATUS_PENDING && 
               (!$this->expired_at || $this->expired_at->isFuture());
    }

    public function canBeVerified()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function getRemainingAmount()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    public function getFormattedAmount($field = 'total_amount')
    {
        return 'Rp ' . number_format($this->$field, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Menunggu</span>',
            self::STATUS_PROCESSING => '<span class="badge bg-info">Diproses</span>',
            self::STATUS_PAID => '<span class="badge bg-primary">Dibayar</span>',
            self::STATUS_VERIFIED => '<span class="badge bg-success">Terverifikasi</span>',
            self::STATUS_FAILED => '<span class="badge bg-danger">Gagal</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">Dibatalkan</span>',
            self::STATUS_EXPIRED => '<span class="badge bg-dark">Kadaluarsa</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getPaymentProofUrlAttribute()
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    // STATIC METHODS
    public static function generateTransactionCode()
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $timestamp = now()->format('His'); // Hours, Minutes, Seconds
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        // Try to generate unique invoice number with retry mechanism
        $maxRetries = 5;
        $attempt = 0;
        
        do {
            $sequence = str_pad($attempt + 1, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = $prefix . $date . $timestamp . $sequence . $random;
            
            // Check if this invoice number already exists
            $exists = static::where('invoice_number', $invoiceNumber)->exists();
            
            if (!$exists) {
                return $invoiceNumber;
            }
            
            $attempt++;
            $random = strtoupper(substr(md5(uniqid()), 0, 4)); // Generate new random
            
        } while ($attempt < $maxRetries);
        
        // Fallback: use microtime for absolute uniqueness
        $microtime = str_replace('.', '', microtime(true));
        return $prefix . $date . substr($microtime, -8);
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transaction_code)) {
                $model->transaction_code = static::generateTransactionCode();
            }
            if (empty($model->invoice_number)) {
                $model->invoice_number = static::generateInvoiceNumber();
            }
        });
    }
}
