<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiMahasiswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pertemuan()
    {
        return $this->belongsTo(PertemuanKuliah::class, 'pertemuan_id');
    }

    public function mahasiswa()
    {
        // Mengarahkan ke model Mahasantri yang sudah ada di sistemmu
        return $this->belongsTo(\App\Models\Mahasantri::class, 'mahasiswa_id'); 
    }
}