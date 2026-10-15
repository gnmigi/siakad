<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;

class GelombangPendaftaran extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'gelombang_pendaftarans';
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relasi dengan Pendaftar
    public function pendaftars()
    {
        return $this->hasMany(\App\Models\PMB\Pendaftar::class, 'gelombang_id');
    }

    // Relasi dengan TagihanKuliahGroup
    public function tagihanKuliahGroups()
    {
        return $this->hasMany(\App\Models\Keuangan\TagihanKuliahGroup::class, 'gelombang_id');
    }

    // Scope untuk gelombang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk gelombang yang sedang berjalan
    public function scopeRunning($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where('is_active', true);
    }
}
