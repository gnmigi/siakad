<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLogAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Mahasantri;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;

class BillingTemplate extends Model
{
    use SoftDeletes, HasLogAktivitas;

    protected $table = 'billing_templates';
    protected $guarded = [];

    // Constants for template types
    const TYPE_SEMESTER = 'semester';
    const TYPE_ANNUAL = 'annual';
    const TYPE_ONE_TIME = 'one_time';
    const TYPE_MONTHLY = 'monthly';

    // Constants for generation triggers
    const TRIGGER_SEMESTER_START = 'semester_start';
    const TRIGGER_MANUAL = 'manual';
    const TRIGGER_SCHEDULED = 'scheduled';

    // Constants for status
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $casts = [
        'amount_formula' => 'array',
        'auto_generate_rules' => 'array',
        'applicable_semesters' => 'array',
        'applicable_programs' => 'array',
        'applicable_classes' => 'array',
        'auto_generate' => 'boolean',
        'use_dynamic_naming' => 'boolean',
        'grace_period_days' => 'integer',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'type' => self::TYPE_SEMESTER,
        'generation_trigger' => self::TRIGGER_MANUAL,
        'auto_generate' => false,
        'use_dynamic_naming' => false,
        'grace_period_days' => 7,
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function tagihanKuliahGroups()
    {
        return $this->hasMany(TagihanKuliahGroup::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeAutoGenerate($query)
    {
        return $query->where('auto_generate', true);
    }

    public function scopeBySemester($query)
    {
        return $query->where('type', self::TYPE_SEMESTER);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods for amount calculation
    public function calculateAmount($mahasiswa, $additionalData = [])
    {
        $formula = $this->amount_formula;
        $baseAmount = $formula['base_amount'] ?? 0;
        
        if (!isset($formula['modifiers']) || !is_array($formula['modifiers'])) {
            return $baseAmount;
        }

        $finalAmount = $baseAmount;

        foreach ($formula['modifiers'] as $modifier) {
            if ($this->evaluateCondition($modifier['condition'], $mahasiswa, $additionalData)) {
                $adjustment = $modifier['adjustment'];
                
                if (strpos($adjustment, '+') === 0) {
                    // Addition: +100000
                    $finalAmount += (int) substr($adjustment, 1);
                } elseif (strpos($adjustment, '-') === 0) {
                    // Subtraction: -50000
                    $finalAmount -= (int) substr($adjustment, 1);
                } elseif (strpos($adjustment, '*') === 0) {
                    // Multiplication: *1.2
                    $finalAmount *= (float) substr($adjustment, 1);
                } elseif (strpos($adjustment, '/') === 0) {
                    // Division: /2
                    $finalAmount /= (float) substr($adjustment, 1);
                }
            }
        }

        return max(0, $finalAmount); // Ensure non-negative amount
    }

    // Method to evaluate conditions
    protected function evaluateCondition($condition, $mahasiswa, $additionalData = [])
    {
        // Simple condition evaluation
        // Examples: "semester >= 5", "program_id == 2", "kelas_id == 1"
        
        // Replace variables with actual values
        $condition = str_replace('semester', $mahasiswa->semester ?? 0, $condition);
        $condition = str_replace('program_id', $mahasiswa->prodi_id ?? 0, $condition);
        $condition = str_replace('kelas_id', $mahasiswa->kelas_id ?? 0, $condition);
        
        // Add additional data variables
        foreach ($additionalData as $key => $value) {
            $condition = str_replace($key, $value, $condition);
        }

        // Evaluate the condition safely
        try {
            return eval("return $condition;");
        } catch (\Exception $e) {
            // Log error and return false for safety
            Log::error("Error evaluating billing condition: $condition", ['error' => $e->getMessage()]);
            return false;
        }
    }

    // Method to calculate due date
    public function calculateDueDate($startDate = null)
    {
        $formula = $this->due_date_formula;

        // Handle "no_due_date" option
        if ($formula === 'no_due_date' || $formula === 'tidak_ada_tempo') {
            return null;
        }

        $startDate = $startDate ? Carbon::parse($startDate) : now();

        // Parse formula like "semester_start + 30 days", "now + 1 month", etc.
        if (strpos($formula, '+') !== false) {
            [$base, $addition] = explode(' + ', $formula, 2);
            
            $baseDate = $this->parseBaseDate($base, $startDate);
            return $this->addTimeToDate($baseDate, $addition);
        }

        return $this->parseBaseDate($formula, $startDate);
    }

    protected function parseBaseDate($base, $fallback)
    {
        switch (trim($base)) {
            case 'now':
                return now();
            case 'semester_start':
                // You might want to get this from TahunAkademik or configuration
                return $fallback;
            case 'month_start':
                return now()->startOfMonth();
            case 'year_start':
                return now()->startOfYear();
            default:
                return Carbon::parse($base);
        }
    }

    protected function addTimeToDate($date, $addition)
    {
        $addition = trim($addition);
        
        if (strpos($addition, 'day') !== false) {
            $days = (int) str_replace(['days', 'day'], '', $addition);
            return $date->addDays($days);
        } elseif (strpos($addition, 'month') !== false) {
            $months = (int) str_replace(['months', 'month'], '', $addition);
            return $date->addMonths($months);
        } elseif (strpos($addition, 'week') !== false) {
            $weeks = (int) str_replace(['weeks', 'week'], '', $addition);
            return $date->addWeeks($weeks);
        }

        return $date;
    }

    // Method to check if template applies to a student
    public function appliesTo($mahasiswa)
    {
        // Check semester applicability
        if ($this->applicable_semesters && !in_array($mahasiswa->semester, $this->applicable_semesters)) {
            return false;
        }

        // Check program applicability
        if ($this->applicable_programs && !in_array($mahasiswa->prodi_id, $this->applicable_programs)) {
            return false;
        }

        // Check class applicability
        if ($this->applicable_classes && !in_array($mahasiswa->kelas_id, $this->applicable_classes)) {
            return false;
        }

        return true;
    }

    // Method to get eligible students for this template
    public function getEligibleStudents()
    {
        $query = Mahasantri::where('type', 1); // Active students only

        // Apply semester filter
        if ($this->applicable_semesters) {
            $query->whereIn('semester', $this->applicable_semesters);
        }

        // Apply program filter
        if ($this->applicable_programs) {
            $query->whereIn('prodi_id', $this->applicable_programs);
        }

        // Apply class filter
        if ($this->applicable_classes) {
            $query->whereIn('kelas_id', $this->applicable_classes);
        }

        return $query->get();
    }

    // Method to generate tagihan group from template
    public function generateTagihanGroup($tahunAkademikId, $additionalData = [])
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            throw new \Exception('Template must be active to generate tagihan group');
        }

        // Generate unique slug
        $baseSlug = Str::slug($this->name . ' ' . now()->format('Y-m-d-H-i-s'));
        $slug = $baseSlug;
        $counter = 1;
        
        // Check if slug exists and make it unique
        while (TagihanKuliahGroup::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Create TagihanKuliahGroup
        $group = TagihanKuliahGroup::create([
            'name' => $this->name . ' - ' . now()->format('Y-m-d H:i:s'),
            'code' => 'TKG-' . strtoupper(Str::random(8)),
            'slug' => $slug,
            'template_id' => $this->id,
            'taka_id' => $tahunAkademikId,
            'amount' => 0, // Will be calculated per student
            'due_date' => $this->calculateDueDate(),
            'desc' => $this->description,
            'status' => 'Draft',
            'created_by' => auth()->id(),
        ]);

        return $group;
    }

    // Static method to get available template types
    public static function getTemplateTypes()
    {
        return [
            self::TYPE_SEMESTER => 'Per Semester',
            self::TYPE_ANNUAL => 'Tahunan',
            self::TYPE_ONE_TIME => 'Sekali Bayar',
            self::TYPE_MONTHLY => 'Bulanan',
        ];
    }

    // Static method to get generation triggers
    public static function getGenerationTriggers()
    {
        return [
            self::TRIGGER_MANUAL => 'Manual',
            self::TRIGGER_SEMESTER_START => 'Awal Semester',
            self::TRIGGER_SCHEDULED => 'Terjadwal',
        ];
    }

    // Static method to get available due date formulas
    public static function getDueDateFormulas()
    {
        return [
            'semester_start + 30 days' => '30 hari setelah awal semester',
            'semester_start + 45 days' => '45 hari setelah awal semester',
            'now + 30 days' => '30 hari dari sekarang',
            'now + 60 days' => '60 hari dari sekarang',
            'no_due_date' => 'Tidak ada tempo',
        ];
    }

    // Method to get due date formula display
    public function getDueDateFormulaDisplayAttribute()
    {
        $formulas = self::getDueDateFormulas();
        return $formulas[$this->due_date_formula] ?? $this->due_date_formula;
    }

    // Method to check if template has no due date
    public function hasNoDueDate()
    {
        return $this->due_date_formula === 'no_due_date' || $this->due_date_formula === 'tidak_ada_tempo';
    }

    // Method to get status display
    public function getStatusDisplayAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'Aktif';
            case self::STATUS_INACTIVE:
                return 'Tidak Aktif';
            case self::STATUS_DRAFT:
            default:
                return 'Draft';
        }
    }

    // Method to get type display
    public function getTypeDisplayAttribute()
    {
        $types = self::getTemplateTypes();
        return $types[$this->type] ?? $this->type;
    }

    // Method to get CSS class for status
    public function getStatusCssClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'badge-success';
            case self::STATUS_INACTIVE:
                return 'badge-secondary';
            case self::STATUS_DRAFT:
            default:
                return 'badge-warning';
        }
    }

    // Method to generate dynamic name based on academic year and semester
    public function generateDynamicName($tahunAkademik = null, $studentSemester = null)
    {
        if (!$this->use_dynamic_naming) {
            return $this->name;
        }

        // Get current academic year if not provided
        if (!$tahunAkademik) {
            $tahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
        }

        if (!$tahunAkademik) {
            return $this->name;
        }

        // Use custom pattern if provided, otherwise use default
        $pattern = $this->dynamic_naming_pattern ?: '{template_name} - {academic_year} {semester_type}';

        // Replace placeholders
        $dynamicName = str_replace([
            '{template_name}',
            '{academic_year}',
            '{semester_type}',
            '{year}',
            '{type}',
            '{student_semester}'
        ], [
            $this->name,
            $tahunAkademik->name, // Use 'name' field which contains "2024/2025" format
            $tahunAkademik->type,
            $tahunAkademik->name, // Use 'name' field for year as well
            $tahunAkademik->type,
            $studentSemester ?: '' // Student semester if provided
        ], $pattern);

        return $dynamicName;
    }

    // Method to get available dynamic naming patterns
    public static function getDynamicNamingPatterns()
    {
        return [
            '{template_name} - {academic_year} {semester_type}' => 'SPP Semester - 2024/2025 Ganjil',
            '{template_name} {academic_year} ({semester_type})' => 'SPP Semester 2024/2025 (Ganjil)',
            '{template_name} - {semester_type} {academic_year}' => 'SPP Semester - Ganjil 2024/2025',
            '{academic_year} {semester_type} - {template_name}' => '2024/2025 Ganjil - SPP Semester',
            '{template_name} - Semester {semester_type} {year}' => 'SPP Semester - Semester Ganjil 2024/2025',
            '{template_name} {student_semester} - {academic_year} {semester_type}' => 'SPP Semester 6 - 2024/2025 Ganjil',
            '{template_name} {student_semester}' => 'SPP Semester 5'
        ];
    }

    // Method to preview dynamic name
    public function previewDynamicName($pattern = null, $tahunAkademik = null)
    {
        $originalPattern = $this->dynamic_naming_pattern;
        $originalUseDynamic = $this->use_dynamic_naming;

        // Temporarily set pattern for preview
        $this->dynamic_naming_pattern = $pattern ?: $this->dynamic_naming_pattern;
        $this->use_dynamic_naming = true;

        $preview = $this->generateDynamicName($tahunAkademik);

        // Restore original values
        $this->dynamic_naming_pattern = $originalPattern;
        $this->use_dynamic_naming = $originalUseDynamic;

        return $preview;
    }
}
