<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

// Import all sheet classes
use App\Exports\TagihanSummarySheet;
use App\Exports\TagihanDetailSheet;
use App\Exports\TagihanByProdiSheet;
use App\Exports\TagihanByStatusSheet;
use App\Exports\TagihanLunasSheet;
use App\Exports\TagihanBelumLunasSheet;
use App\Exports\TagihanByJenisSheet;

class TagihanIndividualExport implements WithMultipleSheets
{
    use Exportable;

    protected $filters;
    protected $exportType;

    public function __construct($filters = [], $exportType = 'summary')
    {
        $this->filters = $filters;
        $this->exportType = $exportType;
    }

    public function sheets(): array
    {
        $sheets = [];

        switch ($this->exportType) {
            case 'summary':
                $sheets[] = new TagihanSummarySheet($this->filters);
                break;
            
            case 'detail':
                // Get tagihan data with additional identifiers to create unique sheets
                $query = \App\Models\Keuangan\TagihanKuliah::query();
                
                if (!empty($this->filters['search'])) {
                    $searchTerm = $this->filters['search'];
                    $query->whereHas('mahasiswa', function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('numb_nim', 'LIKE', '%' . $searchTerm . '%');
                    });
                }

                if (!empty($this->filters['prodi_filter'])) {
                    $query->whereHas('mahasiswa', function($q) {
                        $q->where('prodi_id', $this->filters['prodi_filter']);
                    });
                }

                if (!empty($this->filters['kelas_filter'])) {
                    $query->whereHas('mahasiswa', function($q) {
                        $q->where('kelas_id', $this->filters['kelas_filter']);
                    });
                }

                if (!empty($this->filters['semester_filter'])) {
                    $query->whereHas('mahasiswa', function($q) {
                        $q->where('semester', $this->filters['semester_filter']);
                    });
                }

                if (!empty($this->filters['status_filter'])) {
                    if ($this->filters['status_filter'] === 'Pending') {
                        $query->where('status', 'Pending');
                    } elseif ($this->filters['status_filter'] === 'Paid') {
                        $query->whereIn('status', ['Paid', 'Sukses']);
                    }
                }
                
                // Get all tagihan data to analyze grouping options
                $tagihanData = $query->with(['mahasiswa.prodi', 'mahasiswa.kelas'])
                    ->select('desc', 'code', 'id', 'created_at', 'mahasiswa_id')
                    ->orderBy('desc')
                    ->orderBy('created_at')
                    ->get();
                
                if ($tagihanData->isEmpty()) {
                    // No data found, create empty sheet
                    $sheets[] = new TagihanDetailSheet($this->filters);
                } else {
                    // Strategy: Create sheets based on different criteria to avoid single sheet
                    
                    // Option 1: Group by Program Studi if descriptions are same
                    $prodiGroups = $tagihanData->groupBy(function($item) {
                        return $item->mahasiswa->prodi->name ?? 'Unknown';
                    });
                    
                    // Option 2: Group by Kelas if same prodi
                    $kelasGroups = $tagihanData->groupBy(function($item) {
                        return ($item->mahasiswa->prodi->name ?? 'Unknown') . ' - ' . ($item->mahasiswa->kelas->name ?? 'Unknown');
                    });
                    
                    // Option 3: Group by Description (original)
                    $descGroups = $tagihanData->groupBy('desc');
                    
                    // Choose the best grouping strategy
                    $selectedGroups = [];
                    $usedSheetNames = [];
                    
                    if ($descGroups->count() > 1) {
                        // Multiple descriptions - use description grouping
                        foreach ($descGroups as $desc => $items) {
                            $sheetName = 'Detail - ' . substr($desc, 0, 20);
                            $selectedGroups[] = [
                                'filter_type' => 'desc',
                                'filter_value' => $desc,
                                'sheet_name' => $this->ensureUniqueSheetName($sheetName, $usedSheetNames),
                                'count' => $items->count()
                            ];
                        }
                    } elseif ($kelasGroups->count() > 1 && $kelasGroups->count() <= 10) {
                        // Multiple classes - use class grouping (max 10 sheets)
                        foreach ($kelasGroups as $kelasKey => $items) {
                            $sheetName = 'Kelas - ' . substr($kelasKey, 0, 20);
                            $selectedGroups[] = [
                                'filter_type' => 'kelas_prodi',
                                'filter_value' => $kelasKey,
                                'sheet_name' => $this->ensureUniqueSheetName($sheetName, $usedSheetNames),
                                'count' => $items->count()
                            ];
                        }
                    } elseif ($prodiGroups->count() > 1) {
                        // Multiple prodi - use prodi grouping
                        foreach ($prodiGroups as $prodi => $items) {
                            $sheetName = 'Prodi - ' . substr($prodi, 0, 20);
                            $selectedGroups[] = [
                                'filter_type' => 'prodi',
                                'filter_value' => $prodi,
                                'sheet_name' => $this->ensureUniqueSheetName($sheetName, $usedSheetNames),
                                'count' => $items->count()
                            ];
                        }
                    } else {
                        // All same - create sheets by chunks of data (max 50 per sheet)
                        $chunks = $tagihanData->chunk(50);
                        $chunkNumber = 1;
                        
                        foreach ($chunks as $chunk) {
                            $sheetName = 'Detail - Part ' . $chunkNumber;
                            $selectedGroups[] = [
                                'filter_type' => 'chunk',
                                'filter_value' => $chunkNumber,
                                'sheet_name' => $this->ensureUniqueSheetName($sheetName, $usedSheetNames),
                                'count' => $chunk->count()
                            ];
                            $chunkNumber++;
                        }
                    }
                    
                    // Create sheets based on selected grouping
                    foreach ($selectedGroups as $group) {
                        $sheets[] = new TagihanDetailSheet(
                            array_merge($this->filters, [
                                'group_type' => $group['filter_type'],
                                'group_value' => $group['filter_value']
                            ]), 
                            null, 
                            $group['sheet_name']
                        );
                    }
                }
                break;
            
            case 'by_prodi':
                $sheets[] = new TagihanByProdiSheet($this->filters);
                break;
            
            case 'by_status':
                $sheets[] = new TagihanByStatusSheet($this->filters);
                $sheets[] = new TagihanLunasSheet($this->filters);
                $sheets[] = new TagihanBelumLunasSheet($this->filters);
                break;
            
            case 'by_jenis':
                $sheets[] = new TagihanByJenisSheet($this->filters);
                break;
            
            default:
                $sheets[] = new TagihanSummarySheet($this->filters);
                break;
        }

        return $sheets;
    }

    /**
     * Ensure sheet name is unique and within Excel limits
     */
    private function ensureUniqueSheetName($baseName, &$usedNames)
    {
        // Remove invalid Excel sheet name characters
        $invalidChars = ['\\', '/', '?', '*', '[', ']', ':'];
        $baseName = str_replace($invalidChars, '_', $baseName);
        
        $sheetName = $baseName;
        $counter = 1;
        $originalName = $baseName;
        
        // Check if name already exists, add counter if needed
        while (in_array($sheetName, $usedNames)) {
            $counter++;
            // Create unique name with counter
            $maxBaseLength = 31 - strlen(" ($counter)");
            $truncatedBase = strlen($originalName) > $maxBaseLength 
                ? substr($originalName, 0, $maxBaseLength) 
                : $originalName;
            $sheetName = $truncatedBase . " ($counter)";
        }
        
        // Ensure final name doesn't exceed Excel limit
        if (strlen($sheetName) > 31) {
            $sheetName = substr($sheetName, 0, 31);
        }
        
        $usedNames[] = $sheetName;
        return $sheetName;
    }
}
