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

class TagihanByStatusSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TagihanKuliah::select('status')
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount,
                COUNT(DISTINCT mahasiswa_id) as unique_students
            ')
            ->with(['mahasiswa.prodi'])
            ->groupBy('status')
            ->orderBy('status');

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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'STATUS TAGIHAN',
            'JUMLAH TAGIHAN',
            'MAHASISWA UNIK',
            'TOTAL NOMINAL (Rp)',
            'RATA-RATA NOMINAL (Rp)',
            'NOMINAL TERENDAH (Rp)',
            'NOMINAL TERTINGGI (Rp)',
            'PERSENTASE (%)'
        ];
    }

    public function map($status): array
    {
        static $no = 1;
        static $totalAll = null;
        
        // Calculate total for percentage (only once)
        if ($totalAll === null) {
            $totalAll = TagihanKuliah::count();
        }
        
        $percentage = $totalAll > 0 ? round(($status->total_count / $totalAll) * 100, 1) : 0;

        return [
            $no++,
            $this->getStatusLabel($status->status),
            $status->total_count,
            $status->unique_students,
            $status->total_amount,
            round($status->avg_amount, 0),
            $status->min_amount,
            $status->max_amount,
            $percentage . '%'
        ];
    }

    private function getStatusLabel($status)
    {
        switch ($status) {
            case 'Pending':
                return 'BELUM BAYAR';
            case 'Paid':
            case 'Sukses':
                return 'LUNAS';
            case 'Cancelled':
                return 'DIBATALKAN';
            default:
                return strtoupper($status);
        }
    }

    public function title(): string
    {
        return 'Ringkasan Status';
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
                    'startColor' => ['rgb' => 'FFC107']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows
            'A:I' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount columns
            'E:H' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            // Percentage column
            'I:I' => [
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
            'B' => 18,  // STATUS
            'C' => 15,  // JUMLAH TAGIHAN
            'D' => 15,  // MAHASISWA UNIK
            'E' => 20,  // TOTAL NOMINAL
            'F' => 20,  // RATA-RATA
            'G' => 18,  // TERENDAH
            'H' => 18,  // TERTINGGI
            'I' => 15   // PERSENTASE
        ];
    }
}
