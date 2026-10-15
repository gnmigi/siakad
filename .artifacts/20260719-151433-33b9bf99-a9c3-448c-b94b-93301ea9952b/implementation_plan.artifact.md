# Fix Dosen Nilai Export to Include Existing Grades

The current Excel export for lecturer's "Rekap Nilai" (which uses the same template as "Upload Nilai") intentionally leaves the grade columns empty. This prevents lecturers from using the exported file as a rekap or as a base to update existing grades.

## Proposed Changes

### [Exports]

#### [DosenNilaiTemplateByClassExport.php](file:///C:/laragon/www/siakad/app/Exports/DosenNilaiTemplateByClassExport.php)

- Modify `DosenNilaiClassSheetExport::array()` to include values from the `$student` object for each component instead of an empty string.

```php
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
```

## Verification Plan

### Manual Verification
- Since I cannot run the full Laravel application, I will:
    1. Verify the changes in `DosenNilaiTemplateByClassExport.php`.
    2. Check `NilaiController::downloadUploadTemplate` to ensure it passes the necessary grade data into the `$students` collection. (I already checked this and it uses `getMahasiswaDataByMataKuliah` which fetches grades).
    3. Confirm that the mapping in `NilaiController` correctly populates the `$studentData` with component keys.

### Code Review of `NilaiController`
- The `downloadUploadTemplate` method already contains this logic:
```php
        $students = collect($this->getMahasiswaDataByMataKuliah($mataKuliahId, $user->id))
            ->map(function ($item) use ($components) {
                // ...
                foreach ($components as $component) {
                    $key = $component->component_key;
                    $studentData[$key] = $item[$key] ?? '';
                }
                return (object) $studentData;
            });
```
- This confirms that if we update the Export class, it will correctly receive and display the values.
