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
use Carbon\Carbon;

class TagihanBelumLunasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
                $q->where('status', 'Pending')->orderBy('due_date', 'asc');
            }])
            ->withCount([
                'tagihanKuliah as total_tagihan',
                'tagihanKuliah as pending_tagihan' => function($query) {
                    $query->where('status', 'Pending');
                },
                'tagihanKuliah as overdue_tagihan' => function($query) {
                    $query->where('status', 'Pending')
                          ->where('due_date', '<', Carbon::now());
                }
            ])
            ->withSum([
                'tagihanKuliah as total_pending_amount' => function($query) {
                    $query->where('status', 'Pending');
                },
                'tagihanKuliah as overdue_amount' => function($query) {
                    $query->where('status', 'Pending')
                          ->where('due_date', '<', Carbon::now());
                }
            ], 'amount')
            ->whereHas('tagihanKuliah', function($q) {
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
            'TOTAL TAGIHAN PENDING',
            'TAGIHAN OVERDUE',
            'TOTAL PENDING (Rp)',
            'TOTAL OVERDUE (Rp)',
            'TAGIHAN TERDEKAT',
            'JATUH TEMPO TERDEKAT',
            'STATUS PRIORITAS',
            'KETERANGAN'
        ];
    }

    public function map($mahasiswa): array
    {
        static $no = 1;
        
        // Get nearest due date
        $nearestTagihan = $mahasiswa->tagihanKuliah->first();
        $nearestDueDate = $nearestTagihan ? Carbon::parse($nearestTagihan->due_date)->format('d/m/Y') : '-';
        $nearestDesc = $nearestTagihan ? $nearestTagihan->desc : '-';
        
        // Determine priority status
        $priorityStatus = 'NORMAL';
        $keterangan = 'Masih dalam batas waktu';
        
        if ($mahasiswa->overdue_tagihan > 0) {
            $priorityStatus = 'URGENT';
            $keterangan = 'Ada tagihan yang sudah lewat jatuh tempo';
        } elseif ($nearestTagihan && Carbon::parse($nearestTagihan->due_date)->diffInDays(Carbon::now()) <= 7) {
            $priorityStatus = 'PERHATIAN';
            $keterangan = 'Ada tagihan yang akan jatuh tempo dalam 7 hari';
        }

        return [
            $no++,
            $mahasiswa->numb_nim ?? '-',
            $mahasiswa->name,
            $mahasiswa->prodi->name ?? '-',
            $mahasiswa->kelas->name ?? '-',
            $mahasiswa->semester ? 'Semester ' . $mahasiswa->semester : '-',
            $mahasiswa->pending_tagihan ?? 0,
            $mahasiswa->overdue_tagihan ?? 0,
            $mahasiswa->total_pending_amount ?? 0,
            $mahasiswa->overdue_amount ?? 0,
            $nearestDesc,
            $nearestDueDate,
            $priorityStatus,
            $keterangan
        ];
    }

    public function title(): string
    {
        return 'Mahasiswa Belum Lunas';
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
            'A:N' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount columns
            'I:J' => [
                'numberFormat' => [
                    'formatCode' => '#,##0'
                ]
            ],
            // Priority status column
            'M:M' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'font' => [
                    'bold' => true
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
            'G' => 18,  // TOTAL PENDING
            'H' => 15,  // OVERDUE
            'I' => 18,  // TOTAL PENDING AMOUNT
            'J' => 18,  // OVERDUE AMOUNT
            'K' => 25,  // TAGIHAN TERDEKAT
            'L' => 18,  // JATUH TEMPO
            'M' => 15,  // PRIORITAS
            'N' => 30   // KETERANGAN
        ];
    }
}
