<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\User;

class PaymentVerification extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'payment_verifications';
    protected $guarded = [];

    protected $casts = [
        'verified_amount' => 'integer',
        'payment_date' => 'datetime',
        'verification_data' => 'array',
        'verified_at' => 'datetime',
        'auto_verified_at' => 'datetime',
    ];

    // CONSTANTS
    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_MANUAL = 'manual';
    const TYPE_GATEWAY_CALLBACK = 'gateway_callback';
    const TYPE_BANK_STATEMENT = 'bank_statement';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NEED_REVIEW = 'need_review';

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeNeedReview($query)
    {
        return $query->where('status', self::STATUS_NEED_REVIEW);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('verification_type', self::TYPE_AUTOMATIC);
    }

    public function scopeManual($query)
    {
        return $query->where('verification_type', self::TYPE_MANUAL);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('verification_type', $type);
    }

    // RELATIONSHIPS
    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
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

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function needsReview()
    {
        return $this->status === self::STATUS_NEED_REVIEW;
    }

    public function isAutomatic()
    {
        return $this->verification_type === self::TYPE_AUTOMATIC;
    }

    public function isManual()
    {
        return $this->verification_type === self::TYPE_MANUAL;
    }

    public function canBeApproved()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_NEED_REVIEW]);
    }

    public function canBeRejected()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_NEED_REVIEW]);
    }

    public function approve($userId = null, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'verified_by' => $userId,
            'verified_at' => now(),
            'notes' => $notes ?: $this->notes,
        ]);

        $transaction = $this->paymentTransaction;

        // Update payment transaction status
        $transaction->update([
            'status' => PaymentTransaction::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $userId,
            'paid_at' => $transaction->paid_at ?? now(),
        ]);

        // Sync linked bill (tagihan) and ensure saldo entry for verified payment
        if ($transaction && $transaction->tagihan) {
            $tagihan = $transaction->tagihan;
            $totalAmount = (float) ($tagihan->amount ?? 0);

            // IMPORTANT:
            // paid_amount can be 0 on manual flow even when full payment exists.
            // Use first POSITIVE value from paid_amount/total_amount/amount.
            $candidates = [
                (float) ($transaction->paid_amount ?? 0),
                (float) ($transaction->total_amount ?? 0),
                (float) ($transaction->amount ?? 0),
            ];

            $paymentAmount = 0;
            foreach ($candidates as $candidate) {
                if ($candidate > 0) {
                    $paymentAmount = $candidate;
                    break;
                }
            }

            // FULL if paid amount covers bill total.
            $isFullyPaid = $paymentAmount >= $totalAmount;

            $tagihan->update([
                'paid_amount' => $isFullyPaid ? $totalAmount : $paymentAmount,
                'remaining_amount' => $isFullyPaid ? 0 : max($totalAmount - $paymentAmount, 0),
                'status' => $isFullyPaid ? \App\Models\Keuangan\TagihanKuliah::PAYMENT_SUKSES : 'Cicilan',
                'paid_at' => $transaction->paid_at ?? now(),
                'is_installment' => !$isFullyPaid,
            ]);

            // Ensure saldo transaction exists for this verified manual payment
            // NOTE: table `saldos` in this project does not have `reference` column.
            $existingSaldo = \App\Models\Keuangan\Saldo::where('type', 'Pemasukan')
                ->where('status', 'Sukses')
                ->where('amount', $paymentAmount)
                ->where('desc', 'like', '%'.$transaction->transaction_code.'%')
                ->first();

            if (!$existingSaldo) {
                \App\Models\Keuangan\Saldo::create([
                    'code' => 'SAL-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transaction_code' => $transaction->transaction_code,
                    'type' => 'Pemasukan',
                    'source' => 'Pembayaran Manual',
                    'desc' => 'Pembayaran Mahasiswa - ' . ($transaction->mahasiswa->name ?? '-') . ' (' . ($transaction->mahasiswa->numb_nim ?? '-') . ') - ' . $transaction->transaction_code,
                    'amount' => max(0, $paymentAmount),
                    'status' => 'Sukses',
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }
        }

        return $this;
    }

    public function reject($reason, $userId = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verified_by' => $userId,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Update payment transaction status
        $this->paymentTransaction->update([
            'status' => PaymentTransaction::STATUS_FAILED,
        ]);

        return $this;
    }

    public function markForReview($notes = null)
    {
        $this->update([
            'status' => self::STATUS_NEED_REVIEW,
            'notes' => $notes ?: $this->notes,
        ]);

        return $this;
    }

    public function getFormattedAmount()
    {
        return 'Rp ' . number_format($this->verified_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Menunggu</span>',
            self::STATUS_APPROVED => '<span class="badge bg-success">Disetujui</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">Ditolak</span>',
            self::STATUS_NEED_REVIEW => '<span class="badge bg-info">Perlu Review</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_AUTOMATIC => '<span class="badge bg-success">Otomatis</span>',
            self::TYPE_MANUAL => '<span class="badge bg-primary">Manual</span>',
            self::TYPE_GATEWAY_CALLBACK => '<span class="badge bg-info">Gateway</span>',
            self::TYPE_BANK_STATEMENT => '<span class="badge bg-warning">Mutasi Bank</span>',
        ];

        return $badges[$this->verification_type] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getProofFileUrlAttribute()
    {
        return $this->proof_file ? asset('storage/' . $this->proof_file) : null;
    }

    // STATIC METHODS
    public static function generateVerificationCode()
    {
        $prefix = 'VER';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    public static function createAutoVerification($paymentTransactionId, $verificationData = [])
    {
        return static::create([
            'verification_code' => static::generateVerificationCode(),
            'payment_transaction_id' => $paymentTransactionId,
            'verification_type' => self::TYPE_AUTOMATIC,
            'status' => self::STATUS_APPROVED,
            'verification_data' => $verificationData,
            'verified_at' => now(),
            'auto_verified_at' => now(),
        ]);
    }

    public static function createManualVerification($paymentTransactionId, $data = [])
    {
        return static::create([
            'verification_code' => static::generateVerificationCode(),
            'payment_transaction_id' => $paymentTransactionId,
            'verification_type' => self::TYPE_MANUAL,
            'status' => self::STATUS_PENDING,
            'verified_amount' => $data['amount'] ?? null,
            'payment_date' => $data['payment_date'] ?? null,
            'sender_name' => $data['sender_name'] ?? null,
            'sender_account' => $data['sender_account'] ?? null,
            'bank_reference' => $data['bank_reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'proof_file' => $data['proof_file'] ?? null,
        ]);
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->verification_code)) {
                $model->verification_code = static::generateVerificationCode();
            }
        });
    }
}
