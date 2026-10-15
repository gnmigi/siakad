<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Akademik\ProgramStudi;

class Pendaftar extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftars';
    protected $guard = 'pendaftar';

    protected $fillable = [
        'periode_id',
        'jalur_id',
        'mahasiswa_id',
        'jenis_id',
        'gelombang_id',
        'prodi_1',
        'prodi_2',
        'prodi_3',
        'prodi_id',
        'name',
        'email',
        'phone',
        'password',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'nik',
        'nisn',
        'nama_ayah',
        'nama_ibu',
        'asal_sekolah',
        'tahun_lulus',
        'code',
        'numb_reg',
        'register_date',
        'status',
        'status_validasi',
        'catatan_admin',
        'tanggal_diterima',
        'nim_diberikan',
        'photo',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'register_date' => 'date',
        'tanggal_lahir' => 'date',
        'tanggal_diterima' => 'date',
        'password' => 'hashed',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function periode()
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }

    public function prodi1()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_1');
    }

    public function prodi2()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_2');
    }

    public function prodi3()
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_3');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'warning',
            'Lulus' => 'success',
            'Gagal' => 'danger',
            'Batal' => 'secondary'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getValidasiBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'validated' => 'success',
            'rejected' => 'danger'
        ];

        return $badges[$this->status_validasi] ?? 'secondary';
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && $this->photo !== 'default.jpg') {
            return asset('storage/photos/pendaftar/' . $this->photo);
        }
        return asset('storage/photos/default.jpg');
    }
}
