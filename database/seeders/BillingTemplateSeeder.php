<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan\BillingTemplate;
use Illuminate\Support\Str;

class BillingTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: SPP Semester Regular
        BillingTemplate::create([
            'name' => 'SPP Semester Regular',
            'code' => 'SPP-REG',
            'slug' => 'spp-semester-regular',
            'type' => BillingTemplate::TYPE_SEMESTER,
            'amount_formula' => [
                'base_amount' => 650000,
                'modifiers' => [
                    [
                        'condition' => 'semester >= 5',
                        'adjustment' => '+100000'
                    ],
                    [
                        'condition' => 'program_id == 2',
                        'adjustment' => '*1.2'
                    ]
                ]
            ],
            'auto_generate_rules' => [
                'trigger_on' => 'semester_start',
                'target_students' => [
                    'active_only' => true,
                    'semester_range' => [1, 8]
                ]
            ],
            'applicable_semesters' => [1, 2, 3, 4, 5, 6, 7, 8],
            'applicable_programs' => null, // All programs
            'applicable_classes' => null, // All classes
            'due_date_formula' => 'semester_start + 30 days',
            'grace_period_days' => 7,
            'auto_generate' => true,
            'generation_trigger' => BillingTemplate::TRIGGER_SEMESTER_START,
            'status' => BillingTemplate::STATUS_ACTIVE,
            'description' => 'Template untuk SPP semester regular yang berlaku untuk semua mahasiswa aktif',
            'notes' => 'Otomatis generate setiap awal semester dengan penyesuaian untuk semester tinggi dan program tertentu',
            'created_by' => 1,
        ]);

        // Template 2: Biaya Praktikum
        BillingTemplate::create([
            'name' => 'Biaya Praktikum',
            'code' => 'PRAKTIKUM',
            'slug' => 'biaya-praktikum',
            'type' => BillingTemplate::TYPE_SEMESTER,
            'amount_formula' => [
                'base_amount' => 200000,
                'modifiers' => [
                    [
                        'condition' => 'semester >= 3',
                        'adjustment' => '+50000'
                    ]
                ]
            ],
            'auto_generate_rules' => [
                'trigger_on' => 'semester_start',
                'target_students' => [
                    'active_only' => true,
                    'semester_range' => [3, 4, 5, 6, 7, 8]
                ]
            ],
            'applicable_semesters' => [3, 4, 5, 6, 7, 8],
            'applicable_programs' => null,
            'applicable_classes' => null,
            'due_date_formula' => 'semester_start + 45 days',
            'grace_period_days' => 10,
            'auto_generate' => true,
            'generation_trigger' => BillingTemplate::TRIGGER_SEMESTER_START,
            'status' => BillingTemplate::STATUS_ACTIVE,
            'description' => 'Template untuk biaya praktikum mulai semester 3',
            'notes' => 'Berlaku untuk mahasiswa semester 3 ke atas dengan tambahan biaya untuk semester tinggi',
            'created_by' => 1,
        ]);

        // Template 3: Biaya Wisuda
        BillingTemplate::create([
            'name' => 'Biaya Wisuda',
            'code' => 'WISUDA',
            'slug' => 'biaya-wisuda',
            'type' => BillingTemplate::TYPE_ONE_TIME,
            'amount_formula' => [
                'base_amount' => 500000,
                'modifiers' => []
            ],
            'auto_generate_rules' => [
                'trigger_on' => 'manual',
                'target_students' => [
                    'active_only' => true,
                    'semester_range' => [8]
                ]
            ],
            'applicable_semesters' => [8],
            'applicable_programs' => null,
            'applicable_classes' => null,
            'due_date_formula' => 'now + 60 days',
            'grace_period_days' => 14,
            'auto_generate' => false,
            'generation_trigger' => BillingTemplate::TRIGGER_MANUAL,
            'status' => BillingTemplate::STATUS_ACTIVE,
            'description' => 'Template untuk biaya wisuda mahasiswa semester akhir',
            'notes' => 'Generate manual untuk mahasiswa yang akan wisuda',
            'created_by' => 1,
        ]);

        // Template 4: SPP Kelas Khusus
        BillingTemplate::create([
            'name' => 'SPP Kelas Khusus',
            'code' => 'SPP-KHUSUS',
            'slug' => 'spp-kelas-khusus',
            'type' => BillingTemplate::TYPE_SEMESTER,
            'amount_formula' => [
                'base_amount' => 800000,
                'modifiers' => [
                    [
                        'condition' => 'semester >= 5',
                        'adjustment' => '+150000'
                    ]
                ]
            ],
            'auto_generate_rules' => [
                'trigger_on' => 'semester_start',
                'target_students' => [
                    'active_only' => true,
                    'class_specific' => true
                ]
            ],
            'applicable_semesters' => [1, 2, 3, 4, 5, 6, 7, 8],
            'applicable_programs' => null,
            'applicable_classes' => [2, 3], // Assuming class IDs 2 and 3 are special classes
            'due_date_formula' => 'semester_start + 25 days',
            'grace_period_days' => 5,
            'auto_generate' => true,
            'generation_trigger' => BillingTemplate::TRIGGER_SEMESTER_START,
            'status' => BillingTemplate::STATUS_ACTIVE,
            'description' => 'Template untuk SPP kelas khusus dengan biaya lebih tinggi',
            'notes' => 'Berlaku untuk kelas khusus dengan due date lebih cepat',
            'created_by' => 1,
        ]);

        // Template 5: Biaya Remedial (Draft)
        BillingTemplate::create([
            'name' => 'Biaya Remedial',
            'code' => 'REMEDIAL',
            'slug' => 'biaya-remedial',
            'type' => BillingTemplate::TYPE_ONE_TIME,
            'amount_formula' => [
                'base_amount' => 100000,
                'modifiers' => []
            ],
            'auto_generate_rules' => [
                'trigger_on' => 'manual',
                'target_students' => [
                    'active_only' => true,
                    'specific_criteria' => true
                ]
            ],
            'applicable_semesters' => null,
            'applicable_programs' => null,
            'applicable_classes' => null,
            'due_date_formula' => 'now + 14 days',
            'grace_period_days' => 3,
            'auto_generate' => false,
            'generation_trigger' => BillingTemplate::TRIGGER_MANUAL,
            'status' => BillingTemplate::STATUS_DRAFT,
            'description' => 'Template untuk biaya remedial mahasiswa',
            'notes' => 'Template dalam tahap draft, belum aktif',
            'created_by' => 1,
        ]);
    }
}
