<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studis_publikasi';

    protected $fillable = [
        'code',
        'name',
        'description',
        'content',
        'photo',
        'kategori_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/images/program-studi/' . $this->photo);
        }
        return asset('storage/images/default-program.jpg');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Boot method to generate code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'PS-' . strtoupper(Str::random(8));
            }
        });
    }
}
