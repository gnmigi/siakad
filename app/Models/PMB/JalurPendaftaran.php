<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JalurPendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jalur_pendaftarans';

    protected $fillable = [
        'periode_id',
        'name',
        'code',
        'desc',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class, 'jalur_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }
}
