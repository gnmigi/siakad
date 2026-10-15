<?php

namespace App\Models\Institusi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionPage extends Model
{
    use HasFactory;

    protected $table = 'institution_pages';
    
    protected $fillable = [
        'page_type',
        'title',
        'content',
        'image',
        'additional_data',
        'is_active'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'is_active' => 'boolean'
    ];

    // Page types constants
    const TYPE_PROFILE = 'profile';
    const TYPE_VISION_MISSION = 'vision_mission';
    const TYPE_STRUCTURE = 'structure';
    const TYPE_FACILITIES = 'facilities';

    public static function getPageTypes()
    {
        return [
            self::TYPE_PROFILE => 'Profil Institusi',
            self::TYPE_VISION_MISSION => 'Visi & Misi',
            self::TYPE_STRUCTURE => 'Struktur Organisasi',
            self::TYPE_FACILITIES => 'Fasilitas'
        ];
    }

    public function getPageTypeNameAttribute()
    {
        $types = self::getPageTypes();
        return $types[$this->page_type] ?? $this->page_type;
    }

    // Scope for active pages
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get page by type
    public static function getByType($type)
    {
        return self::where('page_type', $type)->first();
    }
}
