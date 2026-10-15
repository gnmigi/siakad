<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DosenAbsensiSheetExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $dosen;
    protected $matkul;
    protected $kelompok;
    protected $rekapData;

    public function __construct($dosen, $matkul, $kelompok, $rekapData)
    {
        $this->dosen = $dosen;
        $this->matkul = $matkul;
        $this->kelompok = $kelompok;
        $this->rekapData = $rekapData;
    }

    public function view(): View
    {
        return view('central.backpage.exports.dosen-absensi-excel', [
            'dosen' => $this->dosen,
            'matkul' => $this->matkul,
            'kelompok' => $this->kelompok,
            'rekapData' => $this->rekapData
        ]);
    }

    // Memberi nama Tab/Sheet di Excel sesuai nama kelas
    public function title(): string
    {
        // Maksimal nama sheet Excel adalah 31 karakter dan tidak boleh ada karakter aneh
        $cleanName = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->kelompok);
        return substr($cleanName, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'A3:A5' => ['font' => ['bold' => true]],
            7    => ['font' => ['bold' => true]],
        ];
    }
}