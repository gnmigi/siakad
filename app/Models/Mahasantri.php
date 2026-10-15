<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasLogAktivitas;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\TakhassusProgramStudi;
use App\Models\Keuangan\TagihanKuliah;

class Mahasantri extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, HasLogAktivitas;

    protected $table = 'mahasiswas';
    protected $guarded=[];

    protected $casts = [
        'is_terminated' => 'boolean',
        'terminated_at' => 'datetime',
        'graduated_at' => 'datetime',
    ];

    public function getTypeAttribute($value)
    {
        $types = [
            0 => 'Calon Mahasantri Baru',               // => Prefix camaba      => For Calon Mahasantri Baru
            1 => 'Mahasantri Aktif',                    // => Prefix mahasiswa   => For Mahasantri Aktif
            2 => 'Mahasantri Non-Aktif',                // => Prefix nonaktif    => For Mahasantri Non-Aktif
            3 => 'Mahasantri Alumni',                   // => Prefix alumni      => For Mahasantri Alumni
        ];

        return isset($types[$value]) ? $types[$value] : 'Unknown';
    }

    public function getPhotoAttribute($value)
    {
        // Handle cases where photo is null, empty, placeholder.jpg, or default.jpg
        if (empty($value) || $value == 'placeholder.jpg' || $value == 'default.jpg') {
            return asset('storage/images/profile/default.jpg');
        }

        return asset('storage/images/profile/' . $value);
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
            0 => 'camaba.',
            1 => 'mahasiswa.',
            2 => 'mahasiswa-nonaktif.',
            3 => 'alumni.',
        ];

        // Jika type valid, kembalikan prefixnya, kalau tidak 'unknown'
        return isset($prefixes[$this->attributes['type']]) ? $prefixes[$this->attributes['type']] : 'unknown';
    }

    // WILL BE DELETED
    public function getMhsStatAttribute($value)
    {
        $mhsstats = [
            0 => 'Calon Mahasantri',
            1 => 'Mahasantri Aktif',
            2 => 'Mahasantri Non-Aktif',
            3 => 'Mahasantri Alumni',
        ];

        return isset($mhsstats[$value]) ? $mhsstats[$value] : 'Unknown';
    }
    public function getRawMhsStatAttribute()
    {
        return $this->attributes['mhs_stat'];
    }
    public function getAgamaAttribute($value)
    {
        $mhsrelis = [
            0 => 'Belum Memilih',
            1 => 'Agama Islam',
            2 => 'Agama Kristen Katholik',
            3 => 'Agama Kristen Protestan',
            4 => 'Agama Hindu',
            5 => 'Agama Buddha',
            6 => 'Agama Konghuchu',
            7 => 'Kepercayaan Lainnya',
        ];

        return isset($mhsrelis[$value]) ? $mhsrelis[$value] : 'Unknown';
    }


    public function getRawMhsReliAttribute()
    {
        return $this->attributes['mhs_reli'];
    }

    public function getMhsPhoneAttribute($value)
    {
        // Periksa apakah nomor telepon dimulai dengan "0"
        if (strpos($value, '0') === 0) {
            // Jika ya, ubah menjadi "+62" dan hapus angka "0" di awal
            return '62' . substr($value, 1);
        }

        // Jika tidak dimulai dengan "0", biarkan seperti itu
        return $value;
    }

    public function prodi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    // Alias untuk programStudi (untuk kompatibilitas dengan controller)
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function taka()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_active');
    }

    public function takaRegist()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_regist');
    }

    public function takhasusOption()
    {
        return $this->belongsTo(TakhassusProgramStudi::class, 'takhasus_id');
    }

    public function dosenPenasehat()
    {
        return $this->belongsTo(Dosen::class, 'dosen_penasehat_id');
    }

    public function tagihanKuliah()
    {
        return $this->hasMany(TagihanKuliah::class, 'mahasiswa_id');
    }

    // Terminal related relationships and methods
    public function terminalBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'terminated_by');
    }

    /**
     * Check if student is currently in terminal status
     */
    public function isTerminal()
    {
        return $this->is_terminated === true;
    }

    /**
     * Check if student can access KRS
     */
    public function canAccessKrs()
    {
        return !$this->isTerminal();
    }

    /**
     * Check if student can access KHS
     */
    public function canAccessKhs()
    {
        return !$this->isTerminal();
    }

    /**
     * Get terminal status badge for display
     */
    public function getTerminalStatusBadge()
    {
        if ($this->isTerminal()) {
            return '<span class="badge bg-danger text-white"><i class="fas fa-ban me-1"></i>Terminal</span>';
        }
        return '<span class="badge bg-success text-white"><i class="fas fa-check me-1"></i>Aktif</span>';
    }

    /**
     * Get terminal info for display
     */
    public function getTerminalInfo()
    {
        if ($this->isTerminal()) {
            return [
                'status' => 'Terminal',
                'reason' => $this->termination_reason,
                'date' => $this->terminated_at ? $this->terminated_at->format('d/m/Y H:i') : null,
                'by' => $this->terminalBy ? $this->terminalBy->name : null,
            ];
        }
        return null;
    }

    /**
     * Accessor for nim attribute to map to numb_nim column
     */
    public function getNimAttribute()
    {
        return $this->numb_nim;
    }
}
