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
        // Simple Indonesian number to words conversion
        // This is a basic implementation, you might want to use a more robust library
        $ones = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
            'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'
        ];

        $tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];

        if ($number == 0) return 'nol rupiah';
        if ($number < 20) return $ones[$number] . ' rupiah';
        if ($number < 100) return $tens[intval($number/10)] . ' ' . $ones[$number%10] . ' rupiah';
        
        // For larger numbers, you'd need more complex logic
        return number_format($number, 0, ',', '.') . ' rupiah';
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
            'description' => 'Pembayaran ' . $transaction->tagihan->name ?? 'Tagihan Akademik',
            'receipt_type' => self::TYPE_DIGITAL,
            'status' => self::STATUS_DRAFT,
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
