<?php

namespace App\Models\Akademik;
// USE SYSTEM
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use Carbon\Carbon;
// USE MODELS
class TahunAkademik extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'tahun_akademiks';
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'ended_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'ended_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Mutator for start_date to ensure proper format
     *
     * @param  string  $value
     * @return void
     */
    public function setStartDateAttribute($value)
    {
        if ($value) {
            // Handle different date formats
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                // Convert DD-MM-YYYY to YYYY-MM-DD
                $this->attributes['start_date'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                // Already in YYYY-MM-DD format
                $this->attributes['start_date'] = $value;
            } else {
                // Try to parse with Carbon
                $this->attributes['start_date'] = Carbon::parse($value)->format('Y-m-d');
            }
        }
    }

    /**
     * Mutator for ended_date to ensure proper format
     *
     * @param  string  $value
     * @return void
     */
    public function setEndedDateAttribute($value)
    {
        if ($value) {
            // Handle different date formats
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                // Convert DD-MM-YYYY to YYYY-MM-DD
                $this->attributes['ended_date'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                // Already in YYYY-MM-DD format
                $this->attributes['ended_date'] = $value;
            } else {
                // Try to parse with Carbon
                $this->attributes['ended_date'] = Carbon::parse($value)->format('Y-m-d');
            }
        }
    }

    /**
     * Get formatted start date for HTML date input
     *
     * @return string
     */
    public function getFormattedStartDateAttribute()
    {
        // Get raw value from database to avoid casting issues
        $rawValue = $this->getOriginal('start_date');
        return $rawValue ? Carbon::parse($rawValue)->format('Y-m-d') : null;
    }

    /**
     * Get formatted ended date for HTML date input
     *
     * @return string
     */
    public function getFormattedEndedDateAttribute()
    {
        // Get raw value from database to avoid casting issues
        $rawValue = $this->getOriginal('ended_date');
        return $rawValue ? Carbon::parse($rawValue)->format('Y-m-d') : null;
    }

    /**
     * Scope to get only active academic years
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Get the currently active academic year
     *
     * @return \App\Models\Akademik\TahunAkademik|null
     */
    public static function getActive()
    {
        return static::where('status', 'Aktif')->first();
    }

    /**
     * Get the ID of the currently active academic year
     *
     * @return int|null
     */
    public static function getActiveId()
    {
        $active = static::getActive();
        return $active ? $active->id : null;
    }
}
