<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DosenNilaiTemplateExport implements FromArray, WithHeadings, WithTitle
{
    protected Collection $students;
    protected Collection $components;

    public function __construct(Collection $students, Collection $components)
    {
        $this->students = $students;
        $this->components = $components;
    }

    public function headings(): array
    {
        $baseHeadings = ['nama_mahasiswa', 'nim', 'kelas', 'semester'];
        $componentHeadings = $this->components->pluck('component_key')->toArray();

        return array_merge($baseHeadings, $componentHeadings);
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->students as $student) {
            $row = [
                $student->name ?? '',
                $student->numb_nim ?? '',
                $student->kelas_full_name ?? '',
                $student->semester ?? '',
            ];

            foreach ($this->components as $component) {
                $row[] = '';
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Template Nilai';
    }
}
