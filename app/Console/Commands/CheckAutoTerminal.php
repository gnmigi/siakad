<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TerminalService;

class CheckAutoTerminal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terminal:check-auto 
                            {--dry-run : Show what would be terminated without actually terminating}
                            {--force : Force check even if not in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and automatically terminate students with unpaid SPP bills';

    protected $terminalService;

    public function __construct(TerminalService $terminalService)
    {
        parent::__construct();
        $this->terminalService = $terminalService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting auto-terminal check...');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No students will actually be terminated');
            return $this->dryRun();
        }

        $terminatedCount = $this->terminalService->checkBulkAutoTerminal();
        
        $this->info("Auto-terminal check completed.");
        $this->info("Students terminated: {$terminatedCount}");
        
        return Command::SUCCESS;
    }

    /**
     * Perform dry run to show what would be terminated
     */
    protected function dryRun()
    {
        $activeStudents = \App\Models\Mahasantri::where('type', 1)
            ->where('is_terminated', false)
            ->with(['prodi'])
            ->get();

        $wouldTerminate = [];

        foreach ($activeStudents as $mahasiswa) {
            // Get unpaid SPP bills
            $unpaidSppBills = $this->getUnpaidSppBills($mahasiswa);
            
            if ($unpaidSppBills->isNotEmpty()) {
                $shouldTerminate = $this->shouldTerminateStudent($mahasiswa, $unpaidSppBills);
                
                if ($shouldTerminate) {
                    $wouldTerminate[] = [
                        'id' => $mahasiswa->id,
                        'name' => $mahasiswa->name,
                        'nim' => $mahasiswa->numb_nim ?? $mahasiswa->id,
                        'prodi' => $mahasiswa->prodi->name ?? 'N/A',
                        'semester' => $mahasiswa->semester,
                        'unpaid_count' => $unpaidSppBills->count(),
                        'unpaid_amount' => $unpaidSppBills->sum('amount'),
                        'bills' => $unpaidSppBills->map(function($bill) {
                            return $bill->desc . ' (Rp ' . number_format($bill->amount, 0, ',', '.') . ')';
                        })->join(', ')
                    ];
                }
            }
        }

        if (empty($wouldTerminate)) {
            $this->info('No students would be terminated.');
            return Command::SUCCESS;
        }

        $this->warn('The following students would be terminated:');
        $this->table(
            ['ID', 'Name', 'NIM', 'Prodi', 'Semester', 'Unpaid Bills', 'Total Amount', 'Bills Detail'],
            array_map(function($student) {
                return [
                    $student['id'],
                    $student['name'],
                    $student['nim'],
                    $student['prodi'],
                    $student['semester'],
                    $student['unpaid_count'],
                    'Rp ' . number_format($student['unpaid_amount'], 0, ',', '.'),
                    substr($student['bills'], 0, 50) . '...'
                ];
            }, $wouldTerminate)
        );

        $this->info('Total students that would be terminated: ' . count($wouldTerminate));
        
        return Command::SUCCESS;
    }

    /**
     * Get unpaid SPP bills (copied from TerminalService for dry run)
     */
    protected function getUnpaidSppBills($mahasiswa)
    {
        $currentSemester = $mahasiswa->semester ?? 1;
        
        return \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->where(function($query) {
                $query->where('desc', 'LIKE', '%SPP%')
                      ->orWhere('desc', 'LIKE', '%spp%')
                      ->orWhereHas('group', function($q) {
                          $q->where('name', 'LIKE', '%SPP%')
                            ->orWhere('name', 'LIKE', '%spp%');
                      });
            })
            ->whereHas('tahunAkademik', function($query) use ($currentSemester) {
                $query->where(function($q) use ($currentSemester) {
                    $q->where('name', '<', now()->year . '/' . (now()->year + 1))
                      ->orWhere(function($subQ) use ($currentSemester) {
                          $subQ->where('name', '>=', (now()->year - 1) . '/' . now()->year);
                      });
                });
            })
            ->with(['tahunAkademik', 'group'])
            ->get();
    }

    /**
     * Check if student should be terminated (copied from TerminalService for dry run)
     */
    protected function shouldTerminateStudent($mahasiswa, $unpaidBills)
    {
        $currentSemester = $mahasiswa->semester ?? 1;
        
        if ($currentSemester <= 1) {
            return false;
        }

        $criticalUnpaidBills = $unpaidBills->filter(function($bill) use ($currentSemester) {
            if ($bill->status === 'Cicilan' || $bill->isPartiallyPaid()) {
                return false;
            }

            $billSemester = $this->extractSemesterFromBill($bill);
            
            if ($billSemester && $billSemester < ($currentSemester - 1)) {
                return true;
            }

            if ($bill->due_date && \Carbon\Carbon::parse($bill->due_date)->addDays(60)->isPast()) {
                return true;
            }

            return false;
        });

        return $criticalUnpaidBills->isNotEmpty();
    }

    /**
     * Extract semester from bill (copied from TerminalService for dry run)
     */
    protected function extractSemesterFromBill($bill)
    {
        $desc = $bill->desc ?? '';
        $groupName = $bill->group->name ?? '';
        
        $text = $desc . ' ' . $groupName;
        
        if (preg_match('/semester\s*(\d+)/i', $text, $matches)) {
            return (int) $matches[1];
        }
        
        if (preg_match('/sem\s*(\d+)/i', $text, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/ganjil/i', $text)) {
            if (preg_match('/(\d{4})/', $text, $yearMatches)) {
                $year = (int) $yearMatches[1];
                $currentYear = now()->year;
                $yearDiff = $currentYear - $year;
                return ($yearDiff * 2) + 1;
            }
        }
        
        if (preg_match('/genap/i', $text)) {
            if (preg_match('/(\d{4})/', $text, $yearMatches)) {
                $year = (int) $yearMatches[1];
                $currentYear = now()->year;
                $yearDiff = $currentYear - $year;
                return ($yearDiff * 2) + 2;
            }
        }

        return null;
    }
}
