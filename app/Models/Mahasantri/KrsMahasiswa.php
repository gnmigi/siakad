<?php

namespace App\Models\Mahasantri;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasantri;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\ProgramStudi;

class KrsMahasiswa extends Model
{
    protected $table = 'krs_mahasiswa';
    
    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'semester',
        'tanggal_pengisian',
        'status',
        'total_sks',
        'ip_semester',
        'keterangan',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'submitted_at',
        'reopened_by',
        'reopened_at'
    ];

    protected $casts = [
        'tanggal_pengisian' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reopened_at' => 'datetime',
        'total_sks' => 'integer',
        'ip_semester' => 'decimal:2'
    ];

    // Relationships
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasantri::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function details()
    {
        return $this->hasMany(KrsDetailMahasiswa::class, 'krs_mahasiswa_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'reopened_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function getTotalSksAttribute()
    {
        return $this->details()->sum('sks');
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function canBePrinted()
    {
        return in_array($this->status, ['approved', 'dicetak']);
    }
}
