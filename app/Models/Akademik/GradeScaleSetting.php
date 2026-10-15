<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScaleSetting extends Model
{
    use HasFactory;

    protected $table = 'grade_scale_settings';

    protected $fillable = [
        'grade_letter',
        'min_score',
        'max_score',
        'grade_point',
        'color_class',
        'order',
        'is_active'
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get active grade scales ordered by order field
     */
    public static function getActiveGradeScales()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get grade scale by letter
     */
    public static function getByLetter($letter)
    {
        return self::where('grade_letter', $letter)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Convert numerical score to grade letter using configurable scales
     */
    public static function convertToLetterGrade($score)
    {
        $gradeScales = self::getActiveGradeScales();
        
        // First, try exact match within ranges
        foreach ($gradeScales as $scale) {
            if ($score >= $scale->min_score && $score <= $scale->max_score) {
                return $scale->grade_letter;
            }
        }
        
        // If no exact match (due to gaps from disabled grades), 
        // find the highest grade that the score qualifies for
        $sortedScales = $gradeScales->sortByDesc('min_score');
        
        foreach ($sortedScales as $scale) {
            if ($score >= $scale->min_score) {
                return $scale->grade_letter;
            }
        }
        
        // If score is below all active grades, return the lowest grade
        $lowestGrade = $gradeScales->sortBy('grade_point')->first();
        return $lowestGrade ? $lowestGrade->grade_letter : 'E';
    }

    /**
     * Convert numerical score to grade point using configurable scales
     */
    public static function convertToGradePoint($score)
    {
        $gradeScales = self::getActiveGradeScales();
        
        // First, try exact match within ranges
        foreach ($gradeScales as $scale) {
            if ($score >= $scale->min_score && $score <= $scale->max_score) {
                return $scale->grade_point;
            }
        }
        
        // If no exact match (due to gaps from disabled grades), 
        // find the highest grade that the score qualifies for
        $sortedScales = $gradeScales->sortByDesc('min_score');
        
        foreach ($sortedScales as $scale) {
            if ($score >= $scale->min_score) {
                return $scale->grade_point;
            }
        }
        
        // If score is below all active grades, return the lowest grade point
        $lowestGrade = $gradeScales->sortBy('grade_point')->first();
        return $lowestGrade ? $lowestGrade->grade_point : 0.00;
    }

    /**
     * Get grade scales as array for quick lookup
     */
    public static function getGradeScalesArray()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(function ($scale) {
                return [$scale->grade_letter => [
                    'min_score' => $scale->min_score,
                    'max_score' => $scale->max_score,
                    'grade_point' => $scale->grade_point,
                    'color_class' => $scale->color_class
                ]];
            })
            ->toArray();
    }

    /**
     * Validate that grade scale ranges don't overlap
     */
    public static function validateGradeRanges($gradeScales = null)
    {
        if ($gradeScales === null) {
            $gradeScales = self::where('is_active', true)->orderBy('min_score', 'desc')->get();
        }
        
        $ranges = collect($gradeScales)->sortByDesc('min_score');
        
        for ($i = 0; $i < $ranges->count() - 1; $i++) {
            $current = $ranges->values()[$i];
            $next = $ranges->values()[$i + 1];
            
            // Check if current min_score is less than or equal to next max_score
            if ($current->min_score <= $next->max_score) {
                return false; // Overlap detected
            }
        }
        
        return true; // No overlaps
    }

    /**
     * Get default grade scale configuration
     */
    public static function getDefaultGradeScales()
    {
        return [
            [
                'grade_letter' => 'A',
                'min_score' => 85.00,
                'max_score' => 100.00,
                'grade_point' => 4.00,
                'color_class' => 'text-success',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'A-',
                'min_score' => 80.00,
                'max_score' => 84.99,
                'grade_point' => 3.70,
                'color_class' => 'text-success',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'B+',
                'min_score' => 75.00,
                'max_score' => 79.99,
                'grade_point' => 3.30,
                'color_class' => 'text-info',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'B',
                'min_score' => 70.00,
                'max_score' => 74.99,
                'grade_point' => 3.00,
                'color_class' => 'text-info',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'B-',
                'min_score' => 65.00,
                'max_score' => 69.99,
                'grade_point' => 2.70,
                'color_class' => 'text-primary',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'C+',
                'min_score' => 60.00,
                'max_score' => 64.99,
                'grade_point' => 2.30,
                'color_class' => 'text-warning',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'C',
                'min_score' => 55.00,
                'max_score' => 59.99,
                'grade_point' => 2.00,
                'color_class' => 'text-warning',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'C-',
                'min_score' => 50.00,
                'max_score' => 54.99,
                'grade_point' => 1.70,
                'color_class' => 'text-orange',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'D+',
                'min_score' => 45.00,
                'max_score' => 49.99,
                'grade_point' => 1.30,
                'color_class' => 'text-danger',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'D',
                'min_score' => 40.00,
                'max_score' => 44.99,
                'grade_point' => 1.00,
                'color_class' => 'text-danger',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'grade_letter' => 'E',
                'min_score' => 0.00,
                'max_score' => 39.99,
                'grade_point' => 0.00,
                'color_class' => 'text-dark',
                'order' => 11,
                'is_active' => true,
            ],
        ];
    }
}
