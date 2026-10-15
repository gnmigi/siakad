<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Akademik\ProgramStudi;
use Illuminate\Support\Facades\DB;

class TagihanByProdiSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ProgramStudi::select('program_studis.*')
            ->leftJoin('mahasiswas', 'program_studis.id', '=', 'mahasiswas.prodi_id')
            ->leftJoin('tagihan_kuliahs', 'mahasiswas.id', '=', 'tagihan_kuliahs.mahasiswa_id')
            ->selectRaw('
                program_studis.name as prodi_name,
                COUNT(DISTINCT mahasiswas.id) as total_mahasiswa,
                COUNT(DISTINCT CASE WHEN tagihan_kuliahs.id IS NOT NULL THEN mahasiswas.id END) as mahasiswa_with_tagihan,
                COUNT(tagihan_kuliahs.id) as total_tagihan,
                COUNT(CASE WHEN tagihan_kuliahs.status = "Pending" THEN 1 END) as pending_count,
                COUNT(CASE WHEN tagihan_kuliahs.status IN ("Paid", "Sukses") THEN 1 END) as paid_count,
                COALESCE(SUM(CASE WHEN tagihan_kuliahs.status = "Pending" THEN tagihan_kuliahs.amount END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN tagihan_kuliahs.status IN ("Paid", "Sukses") THEN tagihan_kuliahs.amount END), 0) as paid_amount,
                COALESCE(SUM(tagihan_kuliahs.amount), 0) as total_amount
            ')
            ->groupBy('program_studis.id', 'program_studis.name')
            ->orderBy('program_studis.name');

        // Apply filters if any
        if (!empty($this->filters['prodi_filter'])) {
            $query->where('program_studis.id', $this->filters['prodi_filter']);
        }

        if (!empty($this->filters['semester_filter'])) {
            $query->where('mahasiswas.semester', $this->filters['semester_filter']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'PROGRAM STUDI',
            'TOTAL MAHASISWA',
            'MAHASISWA DENGAN TAGIHAN',
            'TOTAL TAGIHAN',
            'TAGIHAN PENDING',
            'TAGIHAN LUNAS',
            'TOTAL PENDING (Rp)',
            'TOTAL LUNAS (Rp)',
            'TOTAL KESELURUHAN (Rp)',
            'PERSENTASE LUNAS (%)',
            'COLLECTION RATE (%)'
        ];
    }

    public function map($prodi): array
    {
        static $no = 1;
        
        // Calculate percentages
        $persentaseLunas = $prodi->total_tagihan > 0 ? 
            round(($prodi->paid_count / $prodi->total_tagihan) * 100, 1) : 0;
        
        $collectionRate = $prodi->total_mahasiswa > 0 ? 
            round(($prodi->mahasiswa_with_tagihan / $prodi->total_mahasiswa) * 100, 1) : 0;

        return [
            $no++,
            $prodi->prodi_name,
            $prodi->total_mahasiswa,
            $prodi->mahasiswa_with_tagihan,
            $prodi->total_tagihan,
            $prodi->pending_count,
            $prodi->paid_count,
            $prodi->pending_amount,
            $prodi->paid_amount,
            $prodi->total_amount,
            $persentaseLunas . '%',
            $collectionRate . '%'
        ];
    }

    public function title(): string
    {
        return 'Tagihan per Program Studi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC3545']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows
            'A:L' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount columns
            'H:J' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            // Percentage columns
            'K:L' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 25,  // PROGRAM STUDI
            'C' => 15,  // TOTAL MAHASISWA
            'D' => 20,  // MAHASISWA DENGAN TAGIHAN
            'E' => 15,  // TOTAL TAGIHAN
            'F' => 15,  // PENDING
            'G' => 15,  // LUNAS
            'H' => 18,  // PENDING AMOUNT
            'I' => 18,  // LUNAS AMOUNT
            'J' => 20,  // TOTAL AMOUNT
            'K' => 18,  // PERSENTASE LUNAS
            'L' => 18   // COLLECTION RATE
        ];
    }
}
