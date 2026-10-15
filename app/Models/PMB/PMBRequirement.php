<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMBRequirement extends Model
{
    use HasFactory;

    protected $table = 'pmb_requirements';

    protected $fillable = [
        'name',
        'description',
        'type',
        'allowed_extensions',
        'max_size_mb',
        'is_required',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'max_size_mb' => 'integer',
        'sort_order' => 'integer'
    ];

    public function uploads()
    {
        return $this->hasMany(PMBRequirementUpload::class, 'requirement_id');
    }

    public function getFormattedExtensionsAttribute()
    {
        return explode(',', $this->allowed_extensions);
    }

    public function getMaxSizeBytesAttribute()
    {
        return $this->max_size_mb * 1024 * 1024;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
