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
use App\Models\Keuangan\TagihanKuliah;
use Illuminate\Support\Facades\DB;

class TagihanByJenisSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TagihanKuliah::select('desc')
            ->selectRaw('
                COUNT(*) as total_count,
                COUNT(CASE WHEN status = "Pending" THEN 1 END) as pending_count,
                COUNT(CASE WHEN status IN ("Paid", "Sukses") THEN 1 END) as paid_count,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN status IN ("Paid", "Sukses") THEN amount ELSE 0 END) as paid_amount,
                AVG(amount) as avg_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount,
                COUNT(DISTINCT mahasiswa_id) as unique_students
            ')
            ->with(['mahasiswa.prodi']);

        // Apply filters based on mahasiswa
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

        return $query->groupBy('desc')
                    ->orderByDesc('total_count')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'JENIS TAGIHAN',
            'TOTAL TAGIHAN',
            'MAHASISWA TERKENA',
            'TAGIHAN PENDING',
            'TAGIHAN LUNAS',
            'TOTAL NOMINAL (Rp)',
            'PENDING AMOUNT (Rp)',
            'LUNAS AMOUNT (Rp)',
            'RATA-RATA (Rp)',
            'TERENDAH (Rp)',
            'TERTINGGI (Rp)',
            'TINGKAT PELUNASAN (%)'
        ];
    }

    public function map($jenis): array
    {
        static $no = 1;
        
        // Calculate completion rate
        $tingkatPelunasan = $jenis->total_count > 0 ? 
            round(($jenis->paid_count / $jenis->total_count) * 100, 1) : 0;

        return [
            $no++,
            $jenis->desc,
            $jenis->total_count,
            $jenis->unique_students,
            $jenis->pending_count,
            $jenis->paid_count,
            $jenis->total_amount,
            $jenis->pending_amount,
            $jenis->paid_amount,
            round($jenis->avg_amount, 0),
            $jenis->min_amount,
            $jenis->max_amount,
            $tingkatPelunasan . '%'
        ];
    }

    public function title(): string
    {
        return 'Tagihan per Jenis';
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
                    'startColor' => ['rgb' => '6F42C1']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows
            'A:M' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount columns
            'G:L' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            // Percentage column
            'M:M' => [
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
            'B' => 30,  // JENIS TAGIHAN
            'C' => 15,  // TOTAL TAGIHAN
            'D' => 18,  // MAHASISWA TERKENA
            'E' => 15,  // PENDING
            'F' => 15,  // LUNAS
            'G' => 18,  // TOTAL NOMINAL
            'H' => 18,  // PENDING AMOUNT
            'I' => 18,  // LUNAS AMOUNT
            'J' => 15,  // RATA-RATA
            'K' => 15,  // TERENDAH
            'L' => 15,  // TERTINGGI
            'M' => 18   // TINGKAT PELUNASAN
        ];
    }
}
