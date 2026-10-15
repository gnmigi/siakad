<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use App\Models\Akademik\ProgramStudi;

class TakhassusProgramStudi extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'takhasus_program_studis';
    protected $guarded = [];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }
}
