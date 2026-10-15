<?php

namespace App\Observers;

use App\Models\Keuangan\TagihanKuliah;
use App\Services\TerminalService;
use Illuminate\Support\Facades\Log;

class TagihanKuliahObserver
{
    protected $terminalService;

    public function __construct(TerminalService $terminalService)
    {
        $this->terminalService = $terminalService;
    }

    /**
     * Handle the TagihanKuliah "updated" event.
     */
    public function updated(TagihanKuliah $tagihan)
    {
        // Check if this is an SPP bill
        if (!$this->isSppBill($tagihan)) {
            return;
        }

        // Check if relevant fields were changed
        $relevantFields = ['due_date', 'status', 'amount'];
        $hasRelevantChanges = false;

        foreach ($relevantFields as $field) {
            if ($tagihan->isDirty($field)) {
                $hasRelevantChanges = true;
                break;
            }
        }

        if (!$hasRelevantChanges) {
            return;
        }

        // Get the mahasiswa and trigger auto-terminal check
        $mahasiswa = $tagihan->mahasiswa;
        if ($mahasiswa) {
            try {
                $result = $this->terminalService->checkAutoTerminal($mahasiswa);
                
                if ($result) {
                    $action = $mahasiswa->fresh()->is_terminated ? 'terminated' : 'reactivated';
                    Log::info("Auto-terminal triggered by tagihan update", [
                        'tagihan_id' => $tagihan->id,
                        'mahasiswa_id' => $mahasiswa->id,
                        'mahasiswa_name' => $mahasiswa->name,
                        'action' => $action,
                        'changed_fields' => array_keys($tagihan->getDirty())
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to trigger auto-terminal on tagihan update", [
                    'tagihan_id' => $tagihan->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the TagihanKuliah "created" event.
     */
    public function created(TagihanKuliah $tagihan)
    {
        // Check if this is an SPP bill
        if (!$this->isSppBill($tagihan)) {
            return;
        }

        // Get the mahasiswa and trigger auto-terminal check for new SPP bills
        $mahasiswa = $tagihan->mahasiswa;
        if ($mahasiswa) {
            try {
                $result = $this->terminalService->checkAutoTerminal($mahasiswa);
                
                if ($result) {
                    $action = $mahasiswa->fresh()->is_terminated ? 'terminated' : 'reactivated';
                    Log::info("Auto-terminal triggered by new tagihan", [
                        'tagihan_id' => $tagihan->id,
                        'mahasiswa_id' => $mahasiswa->id,
                        'mahasiswa_name' => $mahasiswa->name,
                        'action' => $action
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to trigger auto-terminal on tagihan creation", [
                    'tagihan_id' => $tagihan->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Check if the tagihan is an SPP bill
     */
    private function isSppBill(TagihanKuliah $tagihan): bool
    {
        $desc = strtolower($tagihan->desc ?? '');
        return strpos($desc, 'spp') !== false;
    }
}
