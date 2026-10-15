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
use Carbon\Carbon;

class TagihanDetailSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;
    protected $tagihanType;
    protected $customSheetName;

    public function __construct($filters = [], $tagihanType = null, $customSheetName = null)
    {
        $this->filters = $filters;
        $this->tagihanType = $tagihanType;
        $this->customSheetName = $customSheetName;
    }

    public function collection()
    {
        $query = TagihanKuliah::with(['mahasiswa.prodi', 'mahasiswa.kelas', 'tahunAkademik', 'group']);

        // Filter by specific tagihan type if provided
        if ($this->tagihanType) {
            $query->where('desc', $this->tagihanType);
        }

        // Apply new grouping filters
        if (!empty($this->filters['group_type']) && !empty($this->filters['group_value'])) {
            switch ($this->filters['group_type']) {
                case 'desc':
                    $query->where('desc', $this->filters['group_value']);
                    break;
                case 'prodi':
                    $query->whereHas('mahasiswa.prodi', function($q) {
                        $q->where('name', $this->filters['group_value']);
                    });
                    break;
                case 'kelas_prodi':
                    // Parse "Prodi Name - Kelas Name" format
                    $parts = explode(' - ', $this->filters['group_value']);
                    if (count($parts) >= 2) {
                        $prodiName = $parts[0];
                        $kelasName = $parts[1];
                        $query->whereHas('mahasiswa', function($q) use ($prodiName, $kelasName) {
                            $q->whereHas('prodi', function($subQ) use ($prodiName) {
                                $subQ->where('name', $prodiName);
                            })->whereHas('kelas', function($subQ) use ($kelasName) {
                                $subQ->where('name', $kelasName);
                            });
                        });
                    }
                    break;
                case 'chunk':
                    // For chunk filtering, we'll use offset and limit
                    $chunkNumber = (int)$this->filters['group_value'];
                    $chunkSize = 50;
                    $offset = ($chunkNumber - 1) * $chunkSize;
                    $query->offset($offset)->limit($chunkSize);
                    break;
            }
        }

        // Apply original filters based on mahasiswa
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

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE TAGIHAN',
            'NIM',
            'NAMA MAHASISWA',
            'PROGRAM STUDI',
            'KELAS',
            'SEMESTER',
            'TAHUN AKADEMIK',
            'JENIS TAGIHAN',
            'DESKRIPSI',
            'JUMLAH (Rp)',
            'TANGGAL JATUH TEMPO',
            'STATUS',
            'TANGGAL DIBUAT',
            'TANGGAL DIBAYAR'
        ];
    }

    public function map($tagihan): array
    {
        static $no = 1;
        
        // Determine payment date
        $tanggalBayar = '-';
        if (in_array($tagihan->status, ['Paid', 'Sukses'])) {
            $tanggalBayar = $tagihan->updated_at ? $tagihan->updated_at->format('d/m/Y H:i') : '-';
        }

        // Determine tagihan type
        $jenisTagihan = 'Individual';
        if ($tagihan->group) {
            $jenisTagihan = 'Group (' . $tagihan->group->name . ')';
        }

        return [
            $no++,
            $tagihan->code,
            $tagihan->mahasiswa->numb_nim ?? '-',
            $tagihan->mahasiswa->name ?? '-',
            $tagihan->mahasiswa->prodi->name ?? '-',
            $tagihan->mahasiswa->kelas->name ?? '-',
            $tagihan->mahasiswa->semester ? 'Semester ' . $tagihan->mahasiswa->semester : '-',
            $tagihan->tahunAkademik->name ?? '-',
            $jenisTagihan,
            $tagihan->desc,
            $tagihan->amount,
            $tagihan->due_date ? Carbon::parse($tagihan->due_date)->format('d/m/Y') : '-',
            $this->getStatusLabel($tagihan->status),
            $tagihan->created_at->format('d/m/Y H:i'),
            $tanggalBayar
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
        // Use custom sheet name if provided (already processed for uniqueness)
        if ($this->customSheetName) {
            return $this->customSheetName;
        }
        
        // Fallback to original logic
        if ($this->tagihanType) {
            // Limit sheet name to 31 characters (Excel limit)
            $sheetName = 'Detail - ' . $this->tagihanType;
            return strlen($sheetName) > 31 ? substr($sheetName, 0, 31) : $sheetName;
        }
        return 'Detail Tagihan';
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
            'A:O' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Amount column
            'K:K' => [
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
            'B' => 15,  // KODE
            'C' => 12,  // NIM
            'D' => 25,  // NAMA
            'E' => 20,  // PRODI
            'F' => 15,  // KELAS
            'G' => 12,  // SEMESTER
            'H' => 18,  // TAHUN AKADEMIK
            'I' => 15,  // JENIS
            'J' => 30,  // DESKRIPSI
            'K' => 15,  // JUMLAH
            'L' => 15,  // JATUH TEMPO
            'M' => 12,  // STATUS
            'N' => 18,  // TGL DIBUAT
            'O' => 18   // TGL BAYAR
        ];
    }
}
