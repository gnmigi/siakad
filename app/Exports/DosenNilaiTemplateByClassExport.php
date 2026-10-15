<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DosenNilaiTemplateByClassExport implements WithMultipleSheets
{
    protected Collection $groupedStudents;
    protected Collection $components;

    public function __construct(Collection $groupedStudents, Collection $components)
    {
        $this->groupedStudents = $groupedStudents;
        $this->components = $components;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->groupedStudents as $className => $students) {
            $sheets[] = new DosenNilaiClassSheetExport((string) $className, collect($students), $this->components);
        }

        return $sheets;
    }
}

class DosenNilaiClassSheetExport implements
    \Maatwebsite\Excel\Concerns\FromArray,
    \Maatwebsite\Excel\Concerns\WithHeadings,
    \Maatwebsite\Excel\Concerns\WithTitle
{
    protected string $className;
    protected Collection $students;
    protected Collection $components;

    public function __construct(string $className, Collection $students, Collection $components)
    {
        $this->className = $className;
        $this->students = $students;
        $this->components = $components;
    }

    public function headings(): array
    {
        $baseHeadings = ['nama_mahasiswa', 'nim', 'kelas', 'semester'];

        $componentHeadings = $this->components->map(function ($component) {
            $label = trim((string) ($component->component_name ?? ''));
            $key = trim((string) ($component->component_key ?? ''));

            if ($label !== '' && $key !== '') {
                return $label . ' (' . $key . ')';
            }

            return $key !== '' ? $key : $label;
        })->toArray();

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
                $key = $component->component_key;
                // Add the actual value if it exists, otherwise empty string
                $row[] = $student->$key ?? '';
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function title(): string
    {
        return substr($this->className ?: 'Tanpa Kelas', 0, 31);
    }
}
