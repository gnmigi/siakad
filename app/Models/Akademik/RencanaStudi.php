<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\TahunAkademik;

class RencanaStudi extends Model
{
    protected $table = 'rencana_studis';
    
    protected $fillable = [
        'taka_regist',
        'taka_active', 
        'semester',
        'invoice_id',
        'name',
        'code',
        'bsks',
        'start_date',
        'ended_date',
        'desc'
    ];

    protected $casts = [
        'start_date' => 'date',
        'ended_date' => 'date',
    ];

    // Relationship with mata kuliah through pivot table
    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'matkul_rencana_studis', 'renstu_id', 'matkul_id')
                    ->withTimestamps();
    }

    // Relationship with tahun akademik for registration
    public function tahunAkademikRegist()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_regist');
    }

    // Relationship with tahun akademik for active period
    public function tahunAkademikActive()
    {
        return $this->belongsTo(TahunAkademik::class, 'taka_active');
    }
}
