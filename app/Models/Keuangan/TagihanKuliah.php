<?php

namespace App\Models\Keuangan;
// USE SYSTEM
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use Carbon\Carbon;
// USE MODELS
use App\Models\Mahasantri;
use App\Models\Akademik\TahunAkademik;
use App\Models\Keuangan\Biaya;
use App\Models\PMB\BiayaPendaftaran;

class TagihanKuliah extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'tagihan_kuliahs';
    protected $guarded = [];

    // Constants for overdue status
    const STATUS_ON_TIME = 'on_time';
    const STATUS_GRACE_PERIOD = 'grace_period';
    const STATUS_OVERDUE = 'overdue';

    // Constants for payment priority
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_URGENT = 'urgent';
    const PRIORITY_CRITICAL = 'critical';

    // Constants for payment status
    const PAYMENT_PENDING = 'Pending';
    const PAYMENT_SUKSES = 'Sukses';
    const PAYMENT_GAGAL = 'Gagal';

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'grace_period_days' => 'integer',
        'reminder_sent_at' => 'datetime',
        'last_reminder_sent' => 'datetime',
        'first_reminder_sent' => 'datetime',
        'academic_hold_date' => 'datetime',
        'academic_hold_applied' => 'boolean',
        'installment_allowed' => 'boolean',
        'installment_count' => 'integer',
        'is_installment' => 'boolean',
        'installment_payments_count' => 'integer',
        'installment_history' => 'array',
        'reminder_count' => 'integer',
        'reminder_history' => 'array',
    ];

    protected $attributes = [
        'grace_period_days' => 7,
        'overdue_status' => self::STATUS_ON_TIME,
        'payment_priority' => self::PRIORITY_NORMAL,
        'academic_hold_applied' => false,
        'installment_allowed' => false,
        'reminder_count' => 0,
    ];

    protected static function booted()
    {
        static::created(function ($tagihan) {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($tagihan) {
                    $mahasiswa = $tagihan->mahasiswa;
                    if ($mahasiswa && (float)$mahasiswa->deposit_balance > 0) {
                        $balance = (float)$mahasiswa->deposit_balance;
                        $billAmount = (float)$tagihan->remaining_amount;

                        if ($billAmount <= 0) {
                            return;
                        }

                        $allocated = min($balance, $billAmount);
                        if ($allocated <= 0) {
                            return;
                        }

                        // Deduct from deposit balance
                        $mahasiswa->decrement('deposit_balance', $allocated);

                        // Update tagihan using updateQuietly to avoid recursive loops
                        $newPaidAmount = (float)$tagihan->paid_amount + $allocated;
                        $newRemaining = max((float)$tagihan->amount - $newPaidAmount, 0);
                        $isFullyPaid = $newPaidAmount >= (float)$tagihan->amount;

                        $tagihan->updateQuietly([
                            'paid_amount' => $newPaidAmount,
                            'remaining_amount' => $newRemaining,
                            'status' => $isFullyPaid ? self::PAYMENT_SUKSES : 'Cicilan',
                            'paid_at' => now(),
                            'is_installment' => !$isFullyPaid,
                        ]);

                        // Get or create the DEPOSIT payment method ID
                        $paymentMethod = \App\Models\Keuangan\PaymentMethod::where('code', 'DEPOSIT')->first();
                        $paymentMethodId = $paymentMethod ? $paymentMethod->id : null;
                        if (!$paymentMethodId) {
                            $paymentMethodId = \Illuminate\Support\Facades\DB::table('payment_methods')->insertGetId([
                                'code' => 'DEPOSIT',
                                'name' => 'Deposit Saldo',
                                'type' => 'deposit',
                                'provider' => 'manual',
                                'description' => 'Pembayaran menggunakan saldo deposit mahasiswa',
                                'is_active' => true,
                                'auto_verify' => true,
                                'fee_percentage' => 0.00,
                                'fee_fixed' => 0,
                                'sort_order' => 9,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                        // Create PaymentTransaction
                        $txCode = 'TX-DEP-' . time() . '-' . rand(1000, 9999);
                        $invoiceNum = 'INV-DEP-' . time() . '-' . rand(1000, 9999);

                        $transaction = \App\Models\Keuangan\PaymentTransaction::create([
                            'transaction_code' => $txCode,
                            'invoice_number' => $invoiceNum,
                            'tagihan_id' => $tagihan->id,
                            'mahasiswa_id' => $mahasiswa->id,
                            'payment_method_id' => $paymentMethodId,
                            'amount' => $tagihan->amount,
                            'fee_amount' => 0,
                            'total_amount' => $tagihan->amount,
                            'paid_amount' => $allocated,
                            'status' => 'verified',
                            'due_date' => now(),
                            'paid_at' => now(),
                            'verified_at' => now(),
                            'notes' => 'Pembayaran otomatis menggunakan Saldo Deposit Mahasiswa.',
                            'created_by' => 1,
                            'verified_by' => 1
                        ]);

                        // Create RiwayatPembayaran entry
                        \App\Models\Keuangan\RiwayatPembayaran::create([
                            'code' => 'RWY-DEP-' . time() . '-' . rand(100, 999),
                            'payment_transaction_id' => $transaction->id,
                            'mahasiswa_id' => $mahasiswa->id,
                            'tagihan_id' => $tagihan->id,
                            'amount' => $allocated,
                            'payment_method' => 'Deposit Saldo',
                            'paid_at' => now(),
                            'status' => 'verified',
                            'reference_number' => $txCode,
                            'notes' => 'Pembayaran otomatis dari Deposit Saldo Mahasiswa',
                            'created_by' => 1
                        ]);

                        // Create Saldo record (Pengeluaran)
                        \App\Models\Keuangan\Saldo::create([
                            'code' => 'SAL-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                            'tagihan_id' => $tagihan->id,
                            'transaction_code' => $txCode,
                            'type' => 'Pengeluaran',
                            'source' => 'Deposit Mahasiswa (Auto-deduct)',
                            'desc' => 'Dipotong dari deposit saldo mahasiswa - ' . ($mahasiswa->name ?? '-') . ' (' . ($mahasiswa->numb_nim ?? '-') . ') untuk pembayaran tagihan: ' . ($tagihan->group->name ?? 'Tagihan'),
                            'amount' => $allocated,
                            'status' => 'Sukses',
                            'created_by' => 1,
                            'updated_by' => 1,
                        ]);

                        // Generate receipt for this fully paid bill
                        if ($isFullyPaid) {
                            try {
                                \App\Models\Keuangan\PaymentReceipt::createOrUpdateAccumulatedInstallmentReceipt($tagihan, $transaction);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Failed to generate receipt during auto-deduct: ' . $e->getMessage());
                            }
                        }
                    }
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in TagihanKuliah auto-deduct event: ' . $e->getMessage());
            }
        });
    }

    // Relasi dengan TagihanKuliahGroup
    public function group()
    {
        return $this->belongsTo(TagihanKuliahGroup::class, 'group_id');
    }

    // Relasi dengan Mahasantri
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class, 'mahasiswa_id');
    }

    // Relasi dengan Tahun Akademik
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    // Relasi dengan Biaya
    public function biaya()
    {
        return $this->belongsTo(Biaya::class, 'biaya_id');
    }

    // Relasi dengan Biaya PMB
    public function biayaPmb()
    {
        return $this->belongsTo(BiayaPendaftaran::class, 'biaya_pmb');
    }

    // Scope untuk filter berdasarkan status pembayaran
    public function scopePending($query)
    {
        return $query->where('status', self::PAYMENT_PENDING);
    }

    public function scopeSukses($query)
    {
        return $query->where('status', self::PAYMENT_SUKSES);
    }

    public function scopeGagal($query)
    {
        return $query->where('status', self::PAYMENT_GAGAL);
    }

    // Scope untuk filter berdasarkan overdue status
    public function scopeOnTime($query)
    {
        return $query->where('overdue_status', self::STATUS_ON_TIME);
    }

    public function scopeGracePeriod($query)
    {
        return $query->where('overdue_status', self::STATUS_GRACE_PERIOD);
    }

    public function scopeOverdue($query)
    {
        return $query->where('overdue_status', self::STATUS_OVERDUE);
    }

    // Scope untuk filter berdasarkan prioritas
    public function scopeNormalPriority($query)
    {
        return $query->where('payment_priority', self::PRIORITY_NORMAL);
    }

    public function scopeUrgentPriority($query)
    {
        return $query->where('payment_priority', self::PRIORITY_URGENT);
    }

    public function scopeCriticalPriority($query)
    {
        return $query->where('payment_priority', self::PRIORITY_CRITICAL);
    }

    // Scope untuk tagihan yang perlu reminder
    public function scopeNeedsReminder($query)
    {
        return $query->where('status', self::PAYMENT_PENDING)
            ->whereNotNull('due_date') // Skip tagihan tanpa due_date
            ->where(function ($q) {
                $q->whereNull('reminder_sent_at')
                  ->orWhere('reminder_sent_at', '<', now()->subDays(1));
            });
    }

    // Scope untuk tagihan yang sudah overdue
    public function scopeOverduePayments($query)
    {
        return $query->where('status', self::PAYMENT_PENDING)
            ->where('due_date', '<', now())
            ->whereNotNull('due_date'); // Exclude tagihan tanpa due_date
    }

    // Scope untuk academic hold
    public function scopeAcademicHoldApplied($query)
    {
        return $query->where('academic_hold_applied', true);
    }

    // Method untuk mengecek apakah tagihan sudah overdue
    public function isOverdue()
    {
        // Tagihan tanpa due_date tidak pernah overdue
        if ($this->due_date === null) {
            return false;
        }
        
        return $this->status === self::PAYMENT_PENDING && 
               $this->due_date < now();
    }

    // Method untuk mengecek apakah masih dalam grace period
    public function isInGracePeriod()
    {
        if ($this->status !== self::PAYMENT_PENDING) {
            return false;
        }

        // Tagihan tanpa due_date tidak memiliki grace period
        if ($this->due_date === null) {
            return false;
        }

        $gracePeriodEnd = $this->due_date->addDays($this->grace_period_days);
        return now() > $this->due_date && now() <= $gracePeriodEnd;
    }

    // Method untuk mengecek apakah sudah melewati grace period
    public function isPastGracePeriod()
    {
        if ($this->status !== self::PAYMENT_PENDING) {
            return false;
        }

        // Tagihan tanpa due_date tidak pernah melewati grace period
        if ($this->due_date === null) {
            return false;
        }

        $gracePeriodEnd = $this->due_date->addDays($this->grace_period_days);
        return now() > $gracePeriodEnd;
    }

    // Method untuk update status overdue
    public function updateOverdueStatus()
    {
        if ($this->status !== self::PAYMENT_PENDING) {
            return;
        }

        $oldStatus = $this->overdue_status;

        // Tagihan tanpa due_date selalu on_time dengan priority normal
        if ($this->due_date === null) {
            $this->overdue_status = self::STATUS_ON_TIME;
            $this->payment_priority = self::PRIORITY_NORMAL;
        } elseif (!$this->isOverdue()) {
            $this->overdue_status = self::STATUS_ON_TIME;
            $this->payment_priority = self::PRIORITY_NORMAL;
        } elseif ($this->isInGracePeriod()) {
            $this->overdue_status = self::STATUS_GRACE_PERIOD;
            $this->payment_priority = self::PRIORITY_URGENT;
        } else {
            $this->overdue_status = self::STATUS_OVERDUE;
            $this->payment_priority = self::PRIORITY_CRITICAL;
        }

        // Save only if status changed
        if ($oldStatus !== $this->overdue_status) {
            $this->save();
        }
    }

    // Method untuk menghitung hari tersisa sampai due date
    public function getDaysUntilDueDate()
    {
        // Tagihan tanpa due_date tidak memiliki countdown
        if ($this->due_date === null) {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    // Method untuk menghitung hari overdue
    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        // Tagihan tanpa due_date tidak pernah overdue
        if ($this->due_date === null) {
            return 0;
        }

        return $this->due_date->diffInDays(now());
    }

    // Method untuk menambah reminder history
    public function addReminderHistory($type, $method = 'email', $notes = null)
    {
        $history = $this->reminder_history ?? [];
        
        $history[] = [
            'type' => $type, // 'before_due', 'grace_period', 'overdue'
            'method' => $method, // 'email', 'whatsapp', 'sms'
            'sent_at' => now()->toISOString(),
            'notes' => $notes
        ];

        $this->reminder_history = $history;
        $this->reminder_count = count($history);
        $this->last_reminder_sent = now();
        
        if (!$this->first_reminder_sent) {
            $this->first_reminder_sent = now();
        }

        $this->save();
    }

    // Method untuk apply academic hold
    public function applyAcademicHold($reason = null)
    {
        if (!$this->academic_hold_applied) {
            $this->academic_hold_applied = true;
            $this->academic_hold_date = now();
            $this->academic_hold_reason = $reason ?? 'Tunggakan pembayaran melewati batas waktu';
            $this->save();

            // Log activity
            $this->logActivity('academic_hold_applied', [
                'reason' => $this->academic_hold_reason,
                'days_overdue' => $this->getDaysOverdue()
            ]);
        }
    }

    // Method untuk remove academic hold
    public function removeAcademicHold()
    {
        if ($this->academic_hold_applied) {
            $this->academic_hold_applied = false;
            $this->academic_hold_date = null;
            $this->academic_hold_reason = null;
            $this->save();

            // Log activity
            $this->logActivity('academic_hold_removed');
        }
    }

    // Method untuk mendapatkan status display yang user-friendly
    public function getStatusDisplayAttribute()
    {
        if ($this->status === self::PAYMENT_SUKSES) {
            return 'Lunas';
        }

        if ($this->status === self::PAYMENT_GAGAL) {
            return 'Gagal';
        }

        // Tagihan tanpa due_date selalu "Belum Bayar" jika pending
        if ($this->due_date === null && $this->status === self::PAYMENT_PENDING) {
            return 'Belum Bayar';
        }

        // For pending payments with due_date, show overdue status
        switch ($this->overdue_status) {
            case self::STATUS_ON_TIME:
                return 'Belum Bayar';
            case self::STATUS_GRACE_PERIOD:
                return 'Masa Tenggang';
            case self::STATUS_OVERDUE:
                return 'Terlambat';
            default:
                return 'Pending';
        }
    }

    // Method untuk mendapatkan priority display
    public function getPriorityDisplayAttribute()
    {
        switch ($this->payment_priority) {
            case self::PRIORITY_NORMAL:
                return 'Normal';
            case self::PRIORITY_URGENT:
                return 'Mendesak';
            case self::PRIORITY_CRITICAL:
                return 'Kritis';
            default:
                return 'Normal';
        }
    }

    // Method untuk mendapatkan CSS class berdasarkan priority
    public function getPriorityCssClassAttribute()
    {
        switch ($this->payment_priority) {
            case self::PRIORITY_NORMAL:
                return 'badge-success';
            case self::PRIORITY_URGENT:
                return 'badge-warning';
            case self::PRIORITY_CRITICAL:
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    // Method untuk menangani pembayaran cicilan
    public function addInstallmentPayment($amount, $paymentDate = null, $notes = null)
    {
        $paymentDate = $paymentDate ?? now();
        
        // Initialize paid_amount if null
        if ($this->paid_amount === null) {
            $this->paid_amount = 0;
        }
        
        // Initialize installment_payments_count if null
        if ($this->installment_payments_count === null) {
            $this->installment_payments_count = 0;
        }
        
        // Add payment amount
        $this->paid_amount += $amount;
        
        // Calculate remaining amount
        $this->remaining_amount = $this->amount - $this->paid_amount;
        
        // Mark as installment payment
        $this->is_installment = true;
        $this->installment_payments_count += 1;
        
        // Add to installment history with proper JSON formatting
        $history = $this->installment_history ?? [];
        $history[] = [
            'payment_number' => $this->installment_payments_count,
            'amount' => (float) $amount,
            'paid_at' => $paymentDate->toDateTimeString(),
            'notes' => $notes,
            'remaining_amount' => (float) $this->remaining_amount
        ];
        $this->installment_history = $history;
        
        // Update status based on remaining amount
        if ($this->remaining_amount <= 0) {
            $this->status = self::PAYMENT_SUKSES;
            $this->paid_at = $paymentDate;
            $this->remaining_amount = 0;
        } else {
            // Keep as pending but mark as partially paid
            $this->status = 'Cicilan'; // New status for installment payments
        }
        
        $this->save();
        
        return $this;
    }
    
    // Method untuk mengecek apakah tagihan sudah dibayar penuh
    public function isFullyPaid()
    {
        return $this->status === self::PAYMENT_SUKSES || 
               ($this->paid_amount >= $this->amount);
    }
    
    // Method untuk mengecek apakah tagihan dibayar cicilan
    public function isPartiallyPaid()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->amount;
    }
    
    // Method untuk mendapatkan persentase pembayaran
    public function getPaymentPercentage()
    {
        if ($this->amount <= 0) return 0;
        return min(100, ($this->paid_amount / $this->amount) * 100);
    }
    
    // Method untuk mendapatkan sisa pembayaran yang diformat
    public function getFormattedRemainingAmount()
    {
        return 'Rp ' . number_format($this->remaining_amount ?? $this->amount, 0, ',', '.');
    }
    
    // Method untuk mendapatkan jumlah yang sudah dibayar yang diformat
    public function getFormattedPaidAmount()
    {
        return 'Rp ' . number_format($this->paid_amount ?? 0, 0, ',', '.');
    }

    // Method untuk mendapatkan due date yang diformat atau "Tidak ada tempo"
    public function getFormattedDueDate()
    {
        if ($this->due_date === null) {
            return 'Tidak ada tempo';
        }
        
        return $this->due_date->format('d M Y');
    }

    // Method untuk mendapatkan due date display dengan status
    public function getDueDateDisplayAttribute()
    {
        if ($this->due_date === null) {
            return 'Tidak ada tempo';
        }
        
        return $this->due_date->format('d M Y');
    }

    // Method untuk mengecek apakah tagihan memiliki due date
    public function hasDueDate()
    {
        return $this->due_date !== null;
    }

    // Boot method untuk auto-update overdue status
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tagihan) {
            // Auto-update overdue status when saving (skip if due_date is null)
            if ($tagihan->isDirty('due_date') || $tagihan->isDirty('status')) {
                // Only update overdue status if we have a due_date or it's being set to null
                $tagihan->updateOverdueStatus();
            }
            
            // Auto-calculate remaining amount if not set
            if ($tagihan->isDirty('paid_amount') || $tagihan->isDirty('amount')) {
                if ($tagihan->paid_amount !== null) {
                    $tagihan->remaining_amount = $tagihan->amount - $tagihan->paid_amount;
                }
            }
        });
        
        static::creating(function ($tagihan) {
            // Set initial values for new records
            if ($tagihan->paid_amount === null) {
                $tagihan->paid_amount = 0;
            }
            if ($tagihan->remaining_amount === null) {
                $tagihan->remaining_amount = $tagihan->amount;
            }
        });
    }
}
