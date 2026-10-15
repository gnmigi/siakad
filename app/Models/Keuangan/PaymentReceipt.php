<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\Mahasantri;
use App\Models\Keuangan\TagihanKuliah;

class PaymentReceipt extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'payment_receipts';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'receipt_data' => 'array',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'download_count' => 'integer',
    ];

    // CONSTANTS
    const TYPE_DIGITAL = 'digital';
    const TYPE_PRINTED = 'printed';

    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_SENT = 'sent';
    const STATUS_DOWNLOADED = 'downloaded';

    // SCOPES
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', self::STATUS_GENERATED);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeDownloaded($query)
    {
        return $query->where('status', self::STATUS_DOWNLOADED);
    }

    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    public function scopeDigital($query)
    {
        return $query->where('receipt_type', self::TYPE_DIGITAL);
    }

    public function scopePrinted($query)
    {
        return $query->where('receipt_type', self::TYPE_PRINTED);
    }

    // RELATIONSHIPS
    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class, 'mahasiswa_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanKuliah::class, 'tagihan_id');
    }

    // METHODS
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isGenerated()
    {
        return $this->status === self::STATUS_GENERATED;
    }

    public function isSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isDownloaded()
    {
        return $this->status === self::STATUS_DOWNLOADED;
    }

    public function isDigital()
    {
        return $this->receipt_type === self::TYPE_DIGITAL;
    }

    public function isPrinted()
    {
        return $this->receipt_type === self::TYPE_PRINTED;
    }

    public function canBeGenerated()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeSent()
    {
        return in_array($this->status, [self::STATUS_GENERATED, self::STATUS_SENT]);
    }

    public function canBeDownloaded()
    {
        return in_array($this->status, [self::STATUS_GENERATED, self::STATUS_SENT, self::STATUS_DOWNLOADED]);
    }

    public function markAsGenerated($pdfFile = null)
    {
        $this->update([
            'status' => self::STATUS_GENERATED,
            'pdf_file' => $pdfFile ?: $this->pdf_file,
            'generated_at' => now(),
        ]);

        return $this;
    }

    public function markAsSent($email = null, $whatsapp = null)
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'sent_to_email' => $email ?: $this->sent_to_email,
            'sent_to_whatsapp' => $whatsapp ?: $this->sent_to_whatsapp,
        ]);

        return $this;
    }

    public function markAsDownloaded()
    {
        $this->update([
            'status' => self::STATUS_DOWNLOADED,
            'downloaded_at' => now(),
            'download_count' => $this->download_count + 1,
        ]);

        return $this;
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        
        if (!$this->downloaded_at) {
            $this->update(['downloaded_at' => now()]);
        }

        return $this;
    }

    public function getFormattedAmount()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getAmountInWords()
    {
        return $this->amount_words ?: $this->convertToWords($this->amount);
    }

    public function getPdfUrlAttribute()
    {
        return $this->pdf_file ? asset('storage/' . $this->pdf_file) : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_DRAFT => '<span class="badge bg-secondary">Draft</span>',
            self::STATUS_GENERATED => '<span class="badge bg-primary">Dibuat</span>',
            self::STATUS_SENT => '<span class="badge bg-success">Dikirim</span>',
            self::STATUS_DOWNLOADED => '<span class="badge bg-info">Diunduh</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_DIGITAL => '<span class="badge bg-info">Digital</span>',
            self::TYPE_PRINTED => '<span class="badge bg-warning">Cetak</span>',
        ];

        return $badges[$this->receipt_type] ?? '<span class="badge bg-light">Unknown</span>';
    }

    // HELPER METHODS
    private function convertToWords($number)
    {
        $number = (int) $number;

        if ($number === 0) {
            return 'Nol rupiah';
        }

        if ($number < 0) {
            return ucwords('minus ' . trim($this->spellNumber(abs($number))) . ' rupiah');
        }

        return ucwords(trim($this->spellNumber($number)) . ' rupiah');
    }

    private function spellNumber($number)
    {
        $number = (int) $number;
        $words = [
            0 => '',
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan',
            10 => 'sepuluh',
            11 => 'sebelas',
        ];

        if ($number < 12) {
            return ' ' . $words[$number];
        } elseif ($number < 20) {
            return $this->spellNumber($number - 10) . ' belas';
        } elseif ($number < 100) {
            return $this->spellNumber(intdiv($number, 10)) . ' puluh' . $this->spellNumber($number % 10);
        } elseif ($number < 200) {
            return ' seratus' . $this->spellNumber($number - 100);
        } elseif ($number < 1000) {
            return $this->spellNumber(intdiv($number, 100)) . ' ratus' . $this->spellNumber($number % 100);
        } elseif ($number < 2000) {
            return ' seribu' . $this->spellNumber($number - 1000);
        } elseif ($number < 1000000) {
            return $this->spellNumber(intdiv($number, 1000)) . ' ribu' . $this->spellNumber($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->spellNumber(intdiv($number, 1000000)) . ' juta' . $this->spellNumber($number % 1000000);
        } elseif ($number < 1000000000000) {
            return $this->spellNumber(intdiv($number, 1000000000)) . ' miliar' . $this->spellNumber($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            return $this->spellNumber(intdiv($number, 1000000000000)) . ' triliun' . $this->spellNumber($number % 1000000000000);
        }

        // fallback for very large numbers
        return number_format($number, 0, ',', '.');
    }

    // STATIC METHODS
    public static function generateReceiptNumber()
    {
        $prefix = 'RCP';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        $paddedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $paddedSequence;
    }

    public static function generateReceiptCode()
    {
        $prefix = 'KWT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . $random;
    }

    public static function createFromTransaction(PaymentTransaction $transaction)
    {
        return static::create([
            'receipt_number' => static::generateReceiptNumber(),
            'receipt_code' => static::generateReceiptCode(),
            'payment_transaction_id' => $transaction->id,
            'mahasiswa_id' => $transaction->mahasiswa_id,
            'tagihan_id' => $transaction->tagihan_id,
            'amount' => $transaction->total_amount,
            'amount_words' => '', // Will be generated when needed
            'description' => 'Pembayaran ' . ($transaction->tagihan->name ?? 'Tagihan Akademik'),
            'receipt_type' => self::TYPE_DIGITAL,
            'status' => self::STATUS_DRAFT,
        ]);
    }

    /**
     * Create/update accumulated installment receipt for a fully paid tagihan.
     * Idempotent by (tagihan_id + is_accumulated_installment marker in receipt_data).
     */
    public static function createOrUpdateAccumulatedInstallmentReceipt(TagihanKuliah $tagihan, $transaction = null)
    {
        $verifiedTransactions = PaymentTransaction::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('status', PaymentTransaction::STATUS_VERIFIED)
            ->orderBy('created_at')
            ->get();

        if ($verifiedTransactions->isEmpty()) {
            return null;
        }

        $latestTransaction = $transaction ?: $verifiedTransactions->last();

        $receiptData = [
            'is_accumulated_installment' => true,
            'installment_transaction_ids' => $verifiedTransactions->pluck('id')->values()->toArray(),
            'installment_transaction_codes' => $verifiedTransactions->pluck('transaction_code')->values()->toArray(),
            'installment_count' => $verifiedTransactions->count(),
            'installment_total_verified_amount' => (int) $verifiedTransactions->sum(function ($tx) {
                return (int) ($tx->paid_amount ?: $tx->total_amount ?: $tx->amount ?: 0);
            }),
            'tagihan_total_amount' => (int) ($tagihan->amount ?? 0),
            'generated_at' => now()->toDateTimeString(),
        ];

        $receipt = static::query()
            ->where('tagihan_id', $tagihan->id)
            ->whereNotNull('receipt_data')
            ->get()
            ->first(function ($item) {
                return (bool) data_get($item->receipt_data, 'is_accumulated_installment', false);
            });

        if ($receipt) {
            $receipt->update([
                'payment_transaction_id' => $latestTransaction?->id,
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'amount' => (int) ($tagihan->amount ?? 0),
                'description' => 'Kwitansi Akumulasi Cicilan - ' . ($tagihan->name ?? 'Tagihan Akademik'),
                'receipt_type' => self::TYPE_DIGITAL,
                'status' => self::STATUS_DRAFT,
                'receipt_data' => $receiptData,
            ]);

            return $receipt->fresh();
        }

        return static::create([
            'receipt_number' => static::generateReceiptNumber(),
            'receipt_code' => static::generateReceiptCode(),
            'payment_transaction_id' => $latestTransaction?->id,
            'mahasiswa_id' => $tagihan->mahasiswa_id,
            'tagihan_id' => $tagihan->id,
            'amount' => (int) ($tagihan->amount ?? 0),
            'amount_words' => '',
            'description' => 'Kwitansi Akumulasi Cicilan - ' . ($tagihan->name ?? 'Tagihan Akademik'),
            'receipt_type' => self::TYPE_DIGITAL,
            'status' => self::STATUS_DRAFT,
            'receipt_data' => $receiptData,
        ]);
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->receipt_number)) {
                $model->receipt_number = static::generateReceiptNumber();
            }
            if (empty($model->receipt_code)) {
                $model->receipt_code = static::generateReceiptCode();
            }
        });
    }
}
