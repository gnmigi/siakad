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

class TagihanLunasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Mahasantri::select('mahasiswas.*')
            ->with(['prodi', 'kelas.taka', 'tagihanKuliah' => function($q) {
                $q->whereIn('status', ['Paid', 'Sukses']);
            }])
            ->withCount([
                'tagihanKuliah as total_tagihan',
                'tagihanKuliah as paid_tagihan' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ])
            ->withSum([
                'tagihanKuliah as total_paid_amount' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ], 'amount')
            ->whereHas('tagihanKuliah', function($q) {
                $q->whereIn('status', ['Paid', 'Sukses']);
            })
            // Only show students who have NO pending bills (fully paid)
            ->whereDoesntHave('tagihanKuliah', function($q) {
                $q->where('status', 'Pending');
            });

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
            'TOTAL TAGIHAN LUNAS',
            'TOTAL DIBAYAR (Rp)',
            'RATA-RATA PEMBAYARAN (Rp)',
            'STATUS',
            'KETERANGAN'
        ];
    }

    public function map($mahasiswa): array
    {
        static $no = 1;
        
        $rataRata = $mahasiswa->paid_tagihan > 0 ? 
            round($mahasiswa->total_paid_amount / $mahasiswa->paid_tagihan, 0) : 0;

        return [
            $no++,
            $mahasiswa->numb_nim ?? '-',
            $mahasiswa->name,
            $mahasiswa->prodi->name ?? '-',
            $mahasiswa->kelas->name ?? '-',
            $mahasiswa->semester ? 'Semester ' . $mahasiswa->semester : '-',
            $mahasiswa->paid_tagihan ?? 0,
            $mahasiswa->total_paid_amount ?? 0,
            $rataRata,
            'LUNAS SEMUA',
            'Semua tagihan telah dibayar lunas'
        ];
    }

    public function title(): string
    {
        return 'Mahasiswa Lunas';
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
                    'startColor' => ['rgb' => '28A745']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows
            'A:K' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount columns
            'H:I' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            // Status column - green background for paid
            'J:J' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4EDDA']
                ],
                'font' => [
                    'color' => ['rgb' => '155724'],
                    'bold' => true
                ],
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
            'B' => 12,  // NIM
            'C' => 25,  // NAMA
            'D' => 20,  // PRODI
            'E' => 15,  // KELAS
            'F' => 12,  // SEMESTER
            'G' => 18,  // TOTAL LUNAS
            'H' => 18,  // TOTAL DIBAYAR
            'I' => 20,  // RATA-RATA
            'J' => 15,  // STATUS
            'K' => 25   // KETERANGAN
        ];
    }
}
