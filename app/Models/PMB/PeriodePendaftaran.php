<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodePendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'periode_pendaftarans';

    protected $fillable = [
        'taka_id',
        'name',
        'code',
        'desc',
        'start_date',
        'ended_date',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['start_date', 'ended_date', 'deleted_at'];

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class, 'periode_id');
    }

    public function jalurs()
    {
        return $this->hasMany(JalurPendaftaran::class, 'periode_id');
    }
}
