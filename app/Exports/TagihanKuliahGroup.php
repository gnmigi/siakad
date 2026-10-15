<?php

namespace App\Models\Keuangan;
// USE SYSTEM
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
// USE MODELS
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\PMB\GelombangPendaftaran;
use App\Models\PMB\JalurPendaftaran;
use App\Models\Akademik\TahunAkademik;

class TagihanKuliahGroup extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'tagihan_kuliah_groups';
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'integer',
        'amount_per_student' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_student_count' => 'integer',
    ];

    // Relasi dengan TagihanKuliah
    public function tagihanKuliahs()
    {
        return $this->hasMany(TagihanKuliah::class, 'group_id');
    }

    // Relasi dengan Program Studi
    public function prodi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    // Relasi dengan Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi dengan Gelombang
    public function gelombang()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }

    // Relasi dengan Jalur
    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }

    // Relasi dengan Tahun Akademik
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    // Relasi dengan Billing Template
    public function billingTemplate()
    {
        return $this->belongsTo(BillingTemplate::class, 'template_id');
    }

    // Scope untuk filter berdasarkan status
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'Published');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'Archived');
    }

    // Amount handling methods
    
    /**
     * Get the amount per student for this group
     */
    public function getAmountPerStudent()
    {
        // If we have the new field, use it
        if ($this->amount_per_student > 0) {
            return $this->amount_per_student;
        }
        
        // Fallback: interpret based on amount_type
        if ($this->amount_type === 'total' && $this->expected_student_count > 0) {
            return $this->amount / $this->expected_student_count;
        }
        
        // Default: treat amount as per-student
        return $this->amount;
    }

    /**
     * Get the total amount for all students in this group
     */
    public function getTotalAmount()
    {
        // If we have the new field, use it
        if ($this->total_amount > 0) {
            return $this->total_amount;
        }
        
        // Calculate from actual tagihan if available
        if ($this->tagihanKuliahs()->exists()) {
            return $this->tagihanKuliahs()->sum('amount');
        }
        
        // Fallback: calculate based on amount_type
        if ($this->amount_type === 'per_student' && $this->expected_student_count > 0) {
            return $this->amount * $this->expected_student_count;
        }
        
        // Default: treat amount as total
        return $this->amount;
    }

    /**
     * Update amount fields based on current tagihan
     */
    public function recalculateAmounts()
    {
        $tagihans = $this->tagihanKuliahs;
        $count = $tagihans->count();
        
        if ($count > 0) {
            $total = $tagihans->sum('amount');
            $perStudent = $count > 0 ? $total / $count : 0;
            
            $this->update([
                'amount_per_student' => $perStudent,
                'total_amount' => $total,
                'expected_student_count' => $count,
                'amount_type' => 'per_student' // Set consistent type
            ]);
        }
        
        return $this;
    }

    /**
     * Set amount as per-student amount
     */
    public function setPerStudentAmount($amount, $expectedCount = null)
    {
        $count = $expectedCount ?: $this->expected_student_count ?: 1;
        
        $this->update([
            'amount' => $amount, // Keep for backward compatibility
            'amount_per_student' => $amount,
            'total_amount' => $amount * $count,
            'expected_student_count' => $count,
            'amount_type' => 'per_student'
        ]);
        
        return $this;
    }

    /**
     * Set amount as total amount
     */
    public function setTotalAmount($totalAmount, $expectedCount)
    {
        $perStudent = $expectedCount > 0 ? $totalAmount / $expectedCount : 0;
        
        $this->update([
            'amount' => $perStudent, // Keep for backward compatibility
            'amount_per_student' => $perStudent,
            'total_amount' => $totalAmount,
            'expected_student_count' => $expectedCount,
            'amount_type' => 'total'
        ]);
        
        return $this;
    }

    /**
     * Get formatted amount per student
     */
    public function getFormattedAmountPerStudentAttribute()
    {
        return 'Rp ' . number_format($this->getAmountPerStudent(), 0, ',', '.');
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->getTotalAmount(), 0, ',', '.');
    }

    /**
     * Get actual student count from tagihan
     */
    public function getActualStudentCount()
    {
        return $this->tagihanKuliahs()->count();
    }

    /**
     * Check if amounts are synchronized
     */
    public function isAmountSynchronized()
    {
        $actualCount = $this->getActualStudentCount();
        $actualTotal = $this->tagihanKuliahs()->sum('amount');
        
        if ($actualCount === 0) {
            return true; // No tagihan yet, consider synchronized
        }
        
        $expectedTotal = $this->getTotalAmount();
        $tolerance = 1; // Allow 1 rupiah difference due to rounding
        
        return abs($actualTotal - $expectedTotal) <= $tolerance;
    }

    /**
     * Get synchronization status
     */
    public function getSynchronizationStatus()
    {
        if ($this->isAmountSynchronized()) {
            return [
                'status' => 'synchronized',
                'message' => 'Amounts are synchronized',
                'class' => 'text-success'
            ];
        }
        
        return [
            'status' => 'out_of_sync',
            'message' => 'Amounts need synchronization',
            'class' => 'text-warning'
        ];
    }

    /**
     * Migrate legacy amount data
     */
    public function migrateLegacyAmount()
    {
        // Only migrate if new fields are empty
        if ($this->amount_per_student == 0 && $this->total_amount == 0) {
            $actualCount = $this->getActualStudentCount();
            
            if ($actualCount > 0) {
                // We have actual tagihan, calculate from them
                $this->recalculateAmounts();
            } else {
                // No tagihan yet, assume amount is per-student
                $this->setPerStudentAmount($this->amount, $this->expected_student_count ?: 1);
            }
        }
        
        return $this;
    }
}
