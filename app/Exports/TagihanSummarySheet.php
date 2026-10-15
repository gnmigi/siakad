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
use App\Models\Mahasantri;
use Illuminate\Support\Facades\DB;

class TagihanSummarySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Mahasantri::select('mahasiswas.*')
            ->with(['prodi', 'kelas.taka', 'tagihanKuliah'])
            ->withCount([
                'tagihanKuliah as total_tagihan',
                'tagihanKuliah as pending_tagihan' => function($query) {
                    $query->where('status', 'Pending');
                },
                'tagihanKuliah as paid_tagihan' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ])
            ->withSum([
                'tagihanKuliah as total_pending_amount' => function($query) {
                    $query->where('status', 'Pending');
                },
                'tagihanKuliah as total_paid_amount' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ], 'amount')
            ->whereHas('tagihanKuliah');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('numb_nim', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        if (!empty($this->filters['prodi_filter'])) {
            $query->where('prodi_id', $this->filters['prodi_filter']);
        }

        if (!empty($this->filters['kelas_filter'])) {
            $query->where('kelas_id', $this->filters['kelas_filter']);
        }

        if (!empty($this->filters['semester_filter'])) {
            $query->where('semester', $this->filters['semester_filter']);
        }

        if (!empty($this->filters['status_filter'])) {
            if ($this->filters['status_filter'] === 'Pending') {
                $query->whereHas('tagihanKuliah', function($q) {
                    $q->where('status', 'Pending');
                });
            } elseif ($this->filters['status_filter'] === 'Paid') {
                $query->whereHas('tagihanKuliah', function($q) {
                    $q->whereIn('status', ['Paid', 'Sukses']);
                })->whereDoesntHave('tagihanKuliah', function($q) {
                    $q->where('status', 'Pending');
                });
            }
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIM',
            'NAMA MAHASISWA',
            'PROGRAM STUDI',
            'KELAS',
            'SEMESTER',
            'TOTAL TAGIHAN',
            'TAGIHAN PENDING',
            'TAGIHAN LUNAS',
            'TOTAL PENDING (Rp)',
            'TOTAL LUNAS (Rp)',
            'STATUS PEMBAYARAN'
        ];
    }

    public function map($mahasiswa): array
    {
        static $no = 1;
        
        $statusPembayaran = 'LUNAS';
        if ($mahasiswa->pending_tagihan > 0) {
            $statusPembayaran = 'BELUM LUNAS';
        }

        return [
            $no++,
            $mahasiswa->numb_nim ?? '-',
            $mahasiswa->name,
            $mahasiswa->prodi->name ?? '-',
            $mahasiswa->kelas->name ?? '-',
            $mahasiswa->semester ? 'Semester ' . $mahasiswa->semester : '-',
            $mahasiswa->total_tagihan ?? 0,
            $mahasiswa->pending_tagihan ?? 0,
            $mahasiswa->paid_tagihan ?? 0,
            $mahasiswa->total_pending_amount ?? 0,
            $mahasiswa->total_paid_amount ?? 0,
            $statusPembayaran
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Tagihan';
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
                    'startColor' => ['rgb' => '4472C4']
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
            'J:K' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 12,  // NIM
            'C' => 25,  // NAMA
            'D' => 20,  // PRODI
            'E' => 15,  // KELAS
            'F' => 12,  // SEMESTER
            'G' => 12,  // TOTAL TAGIHAN
            'H' => 15,  // PENDING
            'I' => 12,  // LUNAS
            'J' => 18,  // TOTAL PENDING
            'K' => 18,  // TOTAL LUNAS
            'L' => 18   // STATUS
        ];
    }
}
