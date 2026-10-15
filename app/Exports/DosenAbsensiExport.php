<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DosenAbsensiExport implements WithMultipleSheets
{
    use Exportable;
    protected $dataExport;

    public function __construct($dataExport)
    {
        $this->dataExport = $dataExport;
    }

    public function sheets(): array
    {
        $sheets = [];
        // Looping data kelas, jadikan masing-masing sebagai Sheet terpisah
        foreach ($this->dataExport['rekapDataByKelas'] as $kelompok => $rekapData) {
            $sheets[] = new DosenAbsensiSheetExport(
                $this->dataExport['dosen'],
                $this->dataExport['matkul'],
                $kelompok,
                $rekapData
            );
        }
        return $sheets;
    }
}