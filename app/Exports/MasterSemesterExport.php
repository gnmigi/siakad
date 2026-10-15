<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // Kita pakai ini sebagai ganti AutoSize

// Class Utama (Untuk memecah Sheet berdasarkan Matkul)
class MasterSemesterExport implements WithMultipleSheets
{
    use Exportable;

    protected $allRekapByMatkul;
    protected $kelasName;
    protected $semester;

    public function __construct($allRekapByMatkul, $kelasName, $semester)
    {
        $this->allRekapByMatkul = $allRekapByMatkul;
        $this->kelasName = $kelasName;
        $this->semester = $semester;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->allRekapByMatkul as $matkulName => $data) {
            $sheets[] = new MasterSemesterSheetExport($data['dosen'], $data['matkul'], $this->kelasName, $this->semester, $data['rekap']);
        }
        return $sheets;
    }
}

// Class Anak: Hapus ShouldAutoSize, Ganti dengan WithColumnWidths
class MasterSemesterSheetExport implements FromView, WithTitle, WithColumnWidths
{
    protected $dosen;
    protected $matkul;
    protected $kelasName;
    protected $semester;
    protected $rekap;

    public function __construct($dosen, $matkul, $kelasName, $semester, $rekap) {
        $this->dosen = $dosen;
        $this->matkul = $matkul;
        $this->kelasName = $kelasName;
        $this->semester = $semester;
        $this->rekap = $rekap;
    }

    public function view(): View {
        return view('master.akademik.excel-semester', [
            'dosen' => $this->dosen,
            'matkul' => $this->matkul,
            'kelasName' => $this->kelasName,
            'semester' => $this->semester,
            'rekap' => $this->rekap
        ]);
    }

    public function title(): string {
        return substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->matkul->name), 0, 31);
    }

    // KUNCI LEBAR KOLOM SECARA MUTLAK (DIJAMIN GAK PENYOK)
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 35,  // Nama Mahasantri (Lebar)
            'C' => 18,  // NIM (Cukup untuk angka panjang)
            'D' => 4,   // P1
            'E' => 4,   // P2
            'F' => 4,   // P3
            'G' => 4,   // P4
            'H' => 4,   // P5
            'I' => 4,   // P6
            'J' => 4,   // P7
            'K' => 4,   // P8
            'L' => 4,   // P9
            'M' => 4,   // P10
            'N' => 4,   // P11
            'O' => 4,   // P12
            'P' => 4,   // P13
            'Q' => 4,   // P14
            'R' => 10,  // % Hadir
        ];
    }
}   