<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\Mahasantri;
use App\Models\User;

class RiwayatPembayaran extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'riwayat_pembayarans';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // CONSTANTS
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_VERIFIED = 'verified';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
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

    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // RELATIONSHIPS
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class, 'mahasiswa_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanKuliah::class, 'tagihan_id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
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

    public function canBeVerified()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function markAsPaid($userId = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'updated_by' => $userId,
        ]);

        return $this;
    }

    public function markAsVerified($userId = null)
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $userId,
            'updated_by' => $userId,
        ]);

        return $this;
    }

    public function markAsFailed($userId = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'updated_by' => $userId,
        ]);

        return $this;
    }

    public function getFormattedAmount()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Menunggu</span>',
            self::STATUS_PAID => '<span class="badge bg-primary">Dibayar</span>',
            self::STATUS_VERIFIED => '<span class="badge bg-success">Terverifikasi</span>',
            self::STATUS_FAILED => '<span class="badge bg-danger">Gagal</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">Dibatalkan</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getPaymentMethodBadgeAttribute()
    {
        $badges = [
            'bank_transfer' => '<span class="badge bg-info">Transfer Bank</span>',
            'virtual_account' => '<span class="badge bg-primary">Virtual Account</span>',
            'e_wallet' => '<span class="badge bg-success">E-Wallet</span>',
            'credit_card' => '<span class="badge bg-warning">Kartu Kredit</span>',
            'cash' => '<span class="badge bg-secondary">Tunai</span>',
        ];

        return $badges[$this->payment_method] ?? '<span class="badge bg-light">' . ucfirst($this->payment_method) . '</span>';
    }

    // STATIC METHODS
    public static function generateCode()
    {
        $prefix = 'RWY';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        $paddedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $paddedSequence;
    }

    public static function createFromTransaction(PaymentTransaction $transaction)
    {
        return static::create([
            'code' => static::generateCode(),
            'mahasiswa_id' => $transaction->mahasiswa_id,
            'tagihan_id' => $transaction->tagihan_id,
            'payment_transaction_id' => $transaction->id,
            'amount' => $transaction->total_amount,
            'payment_method' => $transaction->paymentMethod->type ?? 'unknown',
            'status' => self::STATUS_PENDING,
            'reference_number' => $transaction->transaction_code,
            'notes' => 'Riwayat pembayaran dari transaksi ' . $transaction->transaction_code,
        ]);
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::generateCode();
            }
        });
    }
}
