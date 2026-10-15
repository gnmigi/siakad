<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertemuanKuliah extends Model
{
    use HasFactory;

    protected $guarded = []; // Mengizinkan insert data masal

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiMahasiswa::class, 'pertemuan_id');
    }
}