<?php

namespace App\Models\Mahasantri;

use Illuminate\Database\Eloquent\Model;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\RencanaStudi;

class KrsDetailMahasiswa extends Model
{
    protected $table = 'krs_detail_mahasiswa';
    
    protected $fillable = [
        'krs_mahasiswa_id',
        'mata_kuliah_id',
        'rencana_studi_id',
        'sks',
        'nilai_huruf',
        'nilai_angka',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'sks' => 'integer',
        'nilai_angka' => 'decimal:2'
    ];

    // Relationships
    public function krsMahasiswa()
    {
        return $this->belongsTo(KrsMahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function rencanaStudi()
    {
        return $this->belongsTo(RencanaStudi::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // Methods
    public function isCompleted()
    {
        return !empty($this->nilai_huruf);
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'aktif':
                return '<span class="badge bg-success">Aktif</span>';
            case 'batal':
                return '<span class="badge bg-danger">Batal</span>';
            case 'selesai':
                return '<span class="badge bg-primary">Selesai</span>';
            default:
                return '<span class="badge bg-secondary">-</span>';
        }
    }
}
