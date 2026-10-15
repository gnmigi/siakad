<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotNilaiSetting extends Model
{
    use HasFactory;

    protected $table = 'bobot_nilai_settings';

    protected $fillable = [
        'component_key',
        'component_name',
        'weight',
        'color_class',
        'order',
        'is_active'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get active components ordered by order field
     */
    public static function getActiveComponents()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get component by key
     */
    public static function getByKey($key)
    {
        return self::where('component_key', $key)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get components as key-value array for calculation
     */
    public static function getWeightsArray()
    {
        return self::where('is_active', true)
            ->pluck('weight', 'component_key')
            ->toArray();
    }

    /**
     * Validate that total weight equals 100%
     */
    public static function validateTotalWeight($components = null)
    {
        if ($components === null) {
            $components = self::where('is_active', true)->get();
        }
        
        $totalWeight = collect($components)->sum('weight');
        return abs($totalWeight - 100) < 0.01; // Allow small floating point differences
    }
}
