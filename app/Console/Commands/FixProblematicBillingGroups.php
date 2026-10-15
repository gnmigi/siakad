<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keuangan\TagihanKuliahGroup;

class FixProblematicBillingGroups extends Command
{
    protected $signature = 'billing:fix-problematic-groups';
    protected $description = 'Fix billing groups with incorrect amount interpretation';

    public function handle()
    {
        $this->info('=== FIXING PROBLEMATIC BILLING GROUPS ===');
        $this->newLine();

        // Find groups that have suspiciously high per-student amounts
        $problematicGroups = TagihanKuliahGroup::where('amount_per_student', '>', 10000000) // More than 10 million per student
            ->where('status', 'Draft') // Only draft groups
            ->get();

        $this->info("Found {$problematicGroups->count()} problematic groups:");
        $this->newLine();

        foreach ($problematicGroups as $group) {
            $this->line("Group: {$group->code} - {$group->name}");
            $this->line("  Current per-student amount: Rp " . number_format($group->amount_per_student, 0, ',', '.'));
            $this->line("  Current total amount: Rp " . number_format($group->total_amount, 0, ',', '.'));
            
            $suspectedTotal = $group->amount_per_student;
            $possibleStudentCounts = [88, 87, 45, 50, 100]; // Common student counts
            $fixApplied = false;
            
            foreach ($possibleStudentCounts as $count) {
                $perStudentAmount = $suspectedTotal / $count;
                
                // Check if this results in a reasonable per-student amount (between 500k - 2M)
                if ($perStudentAmount >= 500000 && $perStudentAmount <= 2000000) {
                    $this->line("  💡 Detected pattern: Rp " . number_format($suspectedTotal, 0, ',', '.') . " ÷ {$count} students = Rp " . number_format($perStudentAmount, 0, ',', '.') . " per student");
                    
                    if ($this->confirm("Apply this fix?", true)) {
                        $group->update([
                            'amount' => $perStudentAmount,
                            'amount_per_student' => $perStudentAmount,
                            'total_amount' => $suspectedTotal,
                            'expected_student_count' => $count,
                            'amount_type' => 'per_student'
                        ]);
                        
                        $this->info("  ✅ Fixed! Per-student: Rp " . number_format($perStudentAmount, 0, ',', '.') . ", Total: Rp " . number_format($suspectedTotal, 0, ',', '.') . " ({$count} students)");
                        $fixApplied = true;
                        break;
                    }
                }
            }
            
            if (!$fixApplied) {
                $this->warn("  ⚠️  Could not auto-fix this group. Manual review needed.");
                $this->line("  💡 Suggestion: Check if " . number_format($suspectedTotal, 0, ',', '.') . " should be divided by student count");
            }
            
            $this->newLine();
        }

        $this->info('=== FIX COMPLETED ===');
        $this->info('Please refresh your browser to see the updated amounts.');
        
        return 0;
    }
}
