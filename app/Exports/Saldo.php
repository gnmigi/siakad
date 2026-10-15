<?php

namespace App\Models\Keuangan;

// USE SYSTEM
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
// USE MODELS
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Mahasantri;

class Saldo extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'saldos';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with TagihanKuliah
     */
    public function tagihan()
    {
        return $this->belongsTo(TagihanKuliah::class, 'tagihan_id');
    }

    /**
     * Relationship with User (created by)
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relationship with User (updated by)
     */
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope for pemasukan
     */
    public function scopePemasukan($query)
    {
        return $query->where('type', 'Pemasukan');
    }

    /**
     * Scope for pengeluaran
     */
    public function scopePengeluaran($query)
    {
        return $query->where('type', 'Pengeluaran');
    }

    /**
     * Scope for sukses status
     */
    public function scopeSukses($query)
    {
        return $query->where('status', 'Sukses');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get saldo bersih (total pemasukan - total pengeluaran)
     */
    public static function getSaldoBersih()
    {
        $pemasukan = self::where('type', 'Pemasukan')
            ->where('status', 'Sukses')
            ->sum('amount');
            
        $pengeluaran = self::where('type', 'Pengeluaran')
            ->where('status', 'Sukses')
            ->sum('amount');
            
        return $pemasukan - $pengeluaran;
    }
}
