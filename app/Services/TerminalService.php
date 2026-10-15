<?php

namespace App\Services;

use App\Models\Mahasantri;
use App\Models\Keuangan\TagihanKuliah;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TerminalService
{
    /**
     * Check if student should be automatically terminated or reactivated based on SPP status
     */
    public function checkAutoTerminal(Mahasantri $mahasiswa)
    {
        // Skip if not active student - handle both integer and string types
        if (!$this->isActiveStudent($mahasiswa)) {
            return false;
        }

        // Get unpaid SPP bills from previous semesters
        $unpaidSppBills = $this->getUnpaidSppBills($mahasiswa);
        
        if ($mahasiswa->isTerminal()) {
            // Check if terminated student should be reactivated (only for auto-terminal)
            return $this->checkAutoReactivation($mahasiswa, $unpaidSppBills);
        } else {
            // Check if active student should be terminated
            if ($unpaidSppBills->isEmpty()) {
                return false;
            }

            $shouldTerminate = $this->shouldTerminateStudent($mahasiswa, $unpaidSppBills);
            
            if ($shouldTerminate) {
                $this->terminateStudent($mahasiswa, $unpaidSppBills);
                return true;
            }
        }

        return false;
    }

    /**
     * Check if terminated student should be automatically reactivated
     * Only reactivate if terminal was automatic (not manual by admin)
     */
    protected function checkAutoReactivation(Mahasantri $mahasiswa, $unpaidBills)
    {
        // Only auto-reactivate if terminal was due to unpaid SPP (not manual terminal)
        if (!$this->wasTerminatedForUnpaidSpp($mahasiswa)) {
            return false;
        }

        // If no unpaid SPP bills, reactivate the student
        if ($unpaidBills->isEmpty()) {
            $this->autoReactivateStudent($mahasiswa);
            return true;
        }

        // FIXED: Check if unpaid bills are no longer critical (not overdue by >60 days)
        $criticalBills = $unpaidBills->filter(function($bill) {
            // Skip installment payments (cicilan)
            if ($bill->status === 'Cicilan' || $bill->isPartiallyPaid()) {
                return false;
            }

            // Check by due date - if overdue by more than 60 days
            if ($bill->due_date) {
                $dueDate = Carbon::parse($bill->due_date);
                $daysPastDue = $dueDate->diffInDays(Carbon::now(), false);
                
                // Only consider critical if due date is in the past AND more than 60 days overdue
                if ($daysPastDue > 60) {
                    return true;
                }
            }

            return false;
        });

        // If no critical bills remain, reactivate the student
        if ($criticalBills->isEmpty()) {
            $this->autoReactivateStudent($mahasiswa);
            return true;
        }

        return false;
    }

    /**
     * Check if student was terminated automatically for unpaid SPP
     */
    protected function wasTerminatedForUnpaidSpp(Mahasantri $mahasiswa)
    {
        $reason = $mahasiswa->termination_reason ?? '';
        
        // Check if termination reason contains auto-terminal keywords
        $autoTerminalKeywords = [
            'Terminal otomatis karena tunggakan SPP',
            'Auto-terminal',
            'Sistem otomatis',
            'Tunggakan SPP'
        ];

        foreach ($autoTerminalKeywords as $keyword) {
            if (stripos($reason, $keyword) !== false) {
                return true;
            }
        }

        // Also check if terminated_by is system user (ID 1) and reason contains SPP
        if ($mahasiswa->terminated_by == 1 && stripos($reason, 'SPP') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Auto-reactivate student (only for auto-terminated students)
     */
    protected function autoReactivateStudent(Mahasantri $mahasiswa)
    {
        $previousReason = $mahasiswa->termination_reason;
        
        $mahasiswa->update([
            'is_terminated' => false,
            'terminated_at' => null,
            'termination_reason' => null,
            'terminated_by' => null,
        ]);

        Log::info('Student auto-reactivated due to paid SPP', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'previous_reason' => $previousReason,
            'reactivated_by' => 'system_auto'
        ]);
    }

    /**
     * Get unpaid SPP bills from previous semesters
     */
    protected function getUnpaidSppBills(Mahasantri $mahasiswa)
    {
        // Get all unpaid bills that are SPP-related (simplified logic to match Console Command)
        return TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->where(function($query) {
                // Check for SPP in description or group name
                $query->where('desc', 'LIKE', '%SPP%')
                      ->orWhere('desc', 'LIKE', '%spp%')
                      ->orWhereHas('group', function($q) {
                          $q->where('name', 'LIKE', '%SPP%')
                            ->orWhere('name', 'LIKE', '%spp%');
                      });
            })
            ->with(['tahunAkademik', 'group'])
            ->get();
    }

    /**
     * Determine if student should be terminated
     */
    protected function shouldTerminateStudent(Mahasantri $mahasiswa, $unpaidBills)
    {
        $currentSemester = $mahasiswa->semester ?? 1;
        
        // Don't terminate students in semester 1 (they just started)
        if ($currentSemester <= 1) {
            return false;
        }

        // Check if there are unpaid SPP bills that are overdue by more than 60 days
        $criticalUnpaidBills = $unpaidBills->filter(function($bill) use ($currentSemester) {
            // Skip installment payments (cicilan)
            if ($bill->status === 'Cicilan' || $bill->isPartiallyPaid()) {
                return false;
            }

            // Check if bill is from previous semesters
            $billSemester = $this->extractSemesterFromBill($bill);
            
            if ($billSemester && $billSemester < ($currentSemester - 1)) {
                return true;
            }

            // FIXED: Check by due date - if overdue by more than 60 days
            // Only consider bills that have due_date and are actually overdue
            if ($bill->due_date) {
                $dueDate = Carbon::parse($bill->due_date);
                $daysPastDue = $dueDate->diffInDays(Carbon::now(), false); // false = past dates are negative
                
                // Only terminate if due date is in the past AND more than 60 days overdue
                if ($daysPastDue > 60) {
                    return true;
                }
            }

            return false;
        });

        return $criticalUnpaidBills->isNotEmpty();
    }

    /**
     * Extract semester information from bill
     */
    protected function extractSemesterFromBill($bill)
    {
        // Try to extract semester from description
        $desc = $bill->desc ?? '';
        $groupName = $bill->group->name ?? '';
        
        $text = $desc . ' ' . $groupName;
        
        // Look for semester patterns
        if (preg_match('/semester\s*(\d+)/i', $text, $matches)) {
            return (int) $matches[1];
        }
        
        if (preg_match('/sem\s*(\d+)/i', $text, $matches)) {
            return (int) $matches[1];
        }

        // Look for "Ganjil" or "Genap" patterns
        if (preg_match('/ganjil/i', $text)) {
            // Extract year and determine if it's semester 1, 3, 5, 7
            if (preg_match('/(\d{4})/', $text, $yearMatches)) {
                $year = (int) $yearMatches[1];
                $currentYear = now()->year;
                $yearDiff = $currentYear - $year;
                return ($yearDiff * 2) + 1; // Approximate semester for ganjil
            }
        }
        
        if (preg_match('/genap/i', $text)) {
            // Extract year and determine if it's semester 2, 4, 6, 8
            if (preg_match('/(\d{4})/', $text, $yearMatches)) {
                $year = (int) $yearMatches[1];
                $currentYear = now()->year;
                $yearDiff = $currentYear - $year;
                return ($yearDiff * 2) + 2; // Approximate semester for genap
            }
        }

        return null;
    }

    /**
     * Terminate student due to unpaid SPP
     */
    protected function terminateStudent(Mahasantri $mahasiswa, $unpaidBills)
    {
        $billDetails = $unpaidBills->map(function($bill) {
            return $bill->desc . ' (Rp ' . number_format($bill->amount, 0, ',', '.') . ')';
        })->join(', ');

        $reason = "Terminal otomatis karena tunggakan SPP: " . $billDetails;

        $mahasiswa->update([
            'is_terminated' => true,
            'terminated_at' => now(),
            'termination_reason' => $reason,
            'terminated_by' => 1, // System user ID
        ]);

        Log::info('Student auto-terminated due to unpaid SPP', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'unpaid_bills_count' => $unpaidBills->count(),
            'total_unpaid_amount' => $unpaidBills->sum('amount'),
            'reason' => $reason
        ]);
    }

    /**
     * Check if student is active (handle both integer and string types)
     */
    protected function isActiveStudent(Mahasantri $mahasiswa)
    {
        $type = $mahasiswa->type;
        
        // Handle integer type (legacy)
        if (is_numeric($type) && (int)$type === 1) {
            return true;
        }
        
        // Handle string type (current system)
        if (is_string($type)) {
            $activeTypes = [
                'Mahasantri Aktif',
                'mahasantri aktif',
                'MAHASANTRI AKTIF',
                'Aktif',
                'aktif',
                'AKTIF',
                '1'
            ];
            
            return in_array($type, $activeTypes, true);
        }
        
        return false;
    }

    /**
     * Check and auto-terminate students in bulk
     */
    public function checkBulkAutoTerminal()
    {
        // Get active students using the same logic as isActiveStudent
        $activeStudents = Mahasantri::where(function($query) {
                // Handle both integer and string types
                $query->where('type', 1)
                      ->orWhere('type', 'Mahasantri Aktif')
                      ->orWhere('type', 'mahasantri aktif')
                      ->orWhere('type', 'MAHASANTRI AKTIF')
                      ->orWhere('type', 'Aktif')
                      ->orWhere('type', 'aktif')
                      ->orWhere('type', 'AKTIF');
            })
            ->where('is_terminated', false)
            ->get();

        $terminatedCount = 0;

        foreach ($activeStudents as $mahasiswa) {
            if ($this->checkAutoTerminal($mahasiswa)) {
                $terminatedCount++;
            }
        }

        Log::info('Bulk auto-terminal check completed', [
            'total_students_checked' => $activeStudents->count(),
            'students_terminated' => $terminatedCount
        ]);

        return $terminatedCount;
    }

    /**
     * Get terminal status for student with detailed info
     */
    public function getTerminalStatus(Mahasantri $mahasiswa)
    {
        if (!$mahasiswa->isTerminal()) {
            // Check if student has unpaid SPP that might lead to terminal
            $unpaidSppBills = $this->getUnpaidSppBills($mahasiswa);
            
            if ($unpaidSppBills->isNotEmpty()) {
                $totalUnpaid = $unpaidSppBills->sum('amount');
                $oldestBill = $unpaidSppBills->sortBy('due_date')->first();
                
                return [
                    'status' => 'warning',
                    'message' => 'Peringatan: Anda memiliki tunggakan SPP sebesar Rp ' . number_format($totalUnpaid, 0, ',', '.'),
                    'unpaid_bills' => $unpaidSppBills,
                    'risk_level' => $this->calculateRiskLevel($mahasiswa, $unpaidSppBills)
                ];
            }
            
            return [
                'status' => 'active',
                'message' => 'Status aktif - tidak ada tunggakan SPP'
            ];
        }

        return [
            'status' => 'terminal',
            'message' => 'Akses dibatasi karena status terminal',
            'terminal_info' => $mahasiswa->getTerminalInfo()
        ];
    }

    /**
     * Calculate risk level for terminal
     */
    protected function calculateRiskLevel(Mahasantri $mahasiswa, $unpaidBills)
    {
        $currentSemester = $mahasiswa->semester ?? 1;
        $overdueBills = $unpaidBills->filter(function($bill) {
            return $bill->due_date && Carbon::parse($bill->due_date)->isPast();
        });

        $criticalBills = $unpaidBills->filter(function($bill) use ($currentSemester) {
            $billSemester = $this->extractSemesterFromBill($bill);
            return $billSemester && $billSemester < ($currentSemester - 1);
        });

        if ($criticalBills->isNotEmpty()) {
            return 'high'; // High risk - has bills from 2+ semesters ago
        }

        if ($overdueBills->count() >= 2) {
            return 'medium'; // Medium risk - multiple overdue bills
        }

        if ($overdueBills->isNotEmpty()) {
            return 'low'; // Low risk - some overdue bills
        }

        return 'none'; // No immediate risk
    }

    /**
     * Manually terminate student
     */
    public function manualTerminate(Mahasantri $mahasiswa, $reason, $userId)
    {
        $mahasiswa->update([
            'is_terminated' => true,
            'terminated_at' => now(),
            'termination_reason' => $reason,
            'terminated_by' => $userId,
        ]);

        Log::info('Student manually terminated', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'reason' => $reason,
            'terminated_by' => $userId
        ]);
    }

    /**
     * Reactivate terminated student
     */
    public function reactivateStudent(Mahasantri $mahasiswa, $userId)
    {
        $mahasiswa->update([
            'is_terminated' => false,
            'terminated_at' => null,
            'termination_reason' => null,
            'terminated_by' => null,
        ]);

        Log::info('Student reactivated', [
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_name' => $mahasiswa->name,
            'reactivated_by' => $userId
        ]);
    }
}
