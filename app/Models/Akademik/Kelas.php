<?php

namespace App\Models\Akademik;
// USE SYSTEM
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
// USE MODELS
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\JenisKelas;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Mahasantri;

class Kelas extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'kelas';
    protected $guarded = [];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    public function taka()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    public function pstudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    public function jenisKelas()
    {
        return $this->belongsTo(JenisKelas::class);
    }

    public function ketua()
    {
        return $this->belongsTo(Mahasantri::class, 'ketua_id');
    }
    public function anggota()
    {
        return $this->hasMany(Mahasantri::class, 'kelas_id');
    }

    public function jadwalKuliah()
    {
        return $this->belongsToMany(JadwalKuliah::class, 'jadwal_kelas', 'kelas_id', 'jadwal_id');
    }
}
