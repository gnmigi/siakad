<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Keuangan\TagihanKuliahGroup;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing data to new amount structure
        $groups = TagihanKuliahGroup::all();
        
        foreach ($groups as $group) {
            $actualCount = $group->tagihanKuliahs()->count();
            
            if ($actualCount > 0) {
                // Group has actual tagihan, calculate from them
                $actualTotal = $group->tagihanKuliahs()->sum('amount');
                $perStudentAmount = $actualCount > 0 ? $actualTotal / $actualCount : 0;
                
                $group->update([
                    'amount_per_student' => $perStudentAmount,
                    'total_amount' => $actualTotal,
                    'expected_student_count' => $actualCount,
                    'amount_type' => 'per_student'
                ]);
                
                echo "Migrated group {$group->code}: {$actualCount} students, per-student: " . number_format($perStudentAmount) . ", total: " . number_format($actualTotal) . "\n";
            } else {
                // No tagihan yet, assume current amount is per-student
                $group->update([
                    'amount_per_student' => $group->amount,
                    'total_amount' => 0, // Will be calculated when tagihan are created
                    'expected_student_count' => 0,
                    'amount_type' => 'per_student'
                ]);
                
                echo "Migrated group {$group->code}: No tagihan yet, set per-student amount to " . number_format($group->amount) . "\n";
            }
        }
        
        echo "Migration completed for " . $groups->count() . " groups.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset the new fields
        TagihanKuliahGroup::query()->update([
            'amount_per_student' => 0,
            'total_amount' => 0,
            'expected_student_count' => 0,
            'amount_type' => 'per_student'
        ]);
    }
};
