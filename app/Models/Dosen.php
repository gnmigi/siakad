<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasLogAktivitas;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\MataKuliah;
use App\Models\Mahasantri;
use App\Models\NilaiMahasiswa;

class Dosen extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes ,HasLogAktivitas;

    protected $guarded=[];

    public function getTypeAttribute($value)
    {
        $types = [
            0 => 'Dosen Non-Aktif',                    // => Prefix dosen-nonaktif  => For Dosen Non-Aktif
            1 => 'Dosen Aktif',                        // => Prefix dosen           => For Dosen Aktif
        ];

        return isset($types[$value]) ? $types[$value] : 'Unknown';
    }

    public function getPhotoAttribute($value)
    {
        return $value == 'default.jpg' ? asset('storage/images/profile/default.jpg') : asset('storage/images/profile/' . $value);
    }


    public function getRawTypeAttribute()
    {
        return $this->attributes['type'];
    }

    public function getWaPhoneAttribute()
    {
        if ($this->phone) {
            return preg_replace('/^0/', '62', $this->phone);
        }

        return null;
    }

    public function getPrefixAttribute()
    {
        $prefixes = [
            0 => 'dosen-nonaktif.',
            1 => 'dosen.',
        ];

        // Jika type valid, kembalikan prefixnya, kalau tidak 'unknown'
        return isset($prefixes[$this->attributes['type']]) ? $prefixes[$this->attributes['type']] : 'unknown';
    }

    // RELASI UNTUK DASHBOARD
    public function jadwalKuliah()
    {
        return $this->hasMany(JadwalKuliah::class, 'dosen_id');
    }

    public function mataKuliahPengampu1()
    {
        return $this->hasMany(MataKuliah::class, 'dosen1_id');
    }

    public function mataKuliahPengampu2()
    {
        return $this->hasMany(MataKuliah::class, 'dosen2_id');
    }

    public function mataKuliahPengampu3()
    {
        return $this->hasMany(MataKuliah::class, 'dosen3_id');
    }

    // METHOD UNTUK STATISTIK DASHBOARD
    public function getTotalMataKuliahAttribute()
    {
        return $this->mataKuliahPengampu1()->count() +
               $this->mataKuliahPengampu2()->count() +
               $this->mataKuliahPengampu3()->count();
    }

    public function getTotalMahasantriAttribute()
    {
        $jadwalIds = $this->jadwalKuliah()->pluck('id');

        // Hitung total mahasantri dari semua kelas yang diajar
        $totalMahasantri = 0;
        foreach ($jadwalIds as $jadwalId) {
            $jadwal = JadwalKuliah::find($jadwalId);
            if ($jadwal && $jadwal->kelas) {
                foreach ($jadwal->kelas as $kelas) {
                    $totalMahasantri += Mahasantri::where('kelas_id', $kelas->id)->count();
                }
            }
        }

        return $totalMahasantri;
    }

    public function getTugasPendingAttribute()
    {
        // Hitung nilai yang belum diinput untuk mata kuliah yang diampu
        $mataKuliahIds = collect();
        $mataKuliahIds = $mataKuliahIds->merge($this->mataKuliahPengampu1()->pluck('id'));
        $mataKuliahIds = $mataKuliahIds->merge($this->mataKuliahPengampu2()->pluck('id'));
        $mataKuliahIds = $mataKuliahIds->merge($this->mataKuliahPengampu3()->pluck('id'));

        return NilaiMahasiswa::whereIn('mata_kuliah_id', $mataKuliahIds)
                            ->whereNull('nilai_akhir')
                            ->count();
    }

    public function getJadwalHariIniAttribute()
    {
        $today = now()->format('l'); // Nama hari dalam bahasa Inggris
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $hariIni = $hariIndonesia[$today] ?? 'Senin';

        return $this->jadwalKuliah()
                   ->with(['mataKuliah', 'kelas', 'waktuKuliah', 'ruang'])
                   ->where('hari', $hariIni)
                   ->orderBy('jam_mulai')
                   ->get();
    }

    public function getAllMataKuliah()
    {
        $mataKuliah = collect();
        $mataKuliah = $mataKuliah->merge($this->mataKuliahPengampu1);
        $mataKuliah = $mataKuliah->merge($this->mataKuliahPengampu2);
        $mataKuliah = $mataKuliah->merge($this->mataKuliahPengampu3);

        return $mataKuliah->unique('id');
    }

    // RELASI UNTUK DOSEN PENASEHAT
    public function mahasiswaBimbingan()
    {
        return $this->hasMany(Mahasantri::class, 'dosen_penasehat_id');
    }

    public function getTotalMahasiswaBimbinganAttribute()
    {
        return $this->mahasiswaBimbingan()->where('type', 1)->count();
    }

    public function getIsPenasehatAttribute()
    {
        return $this->mahasiswaBimbingan()->where('type', 1)->exists();
    }

    // Removed dsn_stat accessors because dsn_stat column does not exist in database
    // public function getDsnStatAttribute($value)
    // {
    //     $dsnstats = [
    //         0 => 'Dosen Tidak Aktif',
    //         1 => 'Dosen Aktif',
    //     ];

    //     return isset($dsnstats[$value]) ? $dsnstats[$value] : 'Unknown';
    // }



    // WILL BE DELETED
    // public function getRawDsnStatAttribute()
    // {
    //     return $this->attributes['dsn_stat'];
    // }
}
