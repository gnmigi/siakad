# Fix Attendance Sync Triggering "Already Graded" Status

When "Sinkron Kehadiran ke Nilai Keaktifan" is used, the system updates the `quiz` component and immediately calculates the `nilai_akhir` (Final Grade). This causes the student to be marked as "Sudah Dinilai" (Already Graded) in the UI, even if other mandatory components (like UTS or UAS) haven't been filled yet.

## Proposed Changes

### [Dosen Component]

#### [AbsensiKelasController.php](file:///C:/laragon/www/siakad/app/Http/Controllers/Private/Dosen/AbsensiKelasController.php)

- Modify `syncToNilai` method to only calculate `nilai_akhir` and `huruf_nilai` if **all active grade components** are present.
- If any active component is missing, `nilai_akhir` and `huruf_nilai` will remain `null`.

```php
    private function syncToNilai($mkId, $mhsId, $dsnId, $skorKeaktifan) {
        $nilaiMhs = DB::table('nilai_mahasiswa')->where('mata_kuliah_id', $mkId)->where('mahasiswa_id', $mhsId)->first();
        
        $gradeData = [
            'mata_kuliah_id' => $mkId, 'mahasiswa_id' => $mhsId, 'dosen_id' => $dsnId,
            'quiz' => $skorKeaktifan, 'updated_at' => now()
        ];

        // Get active components to check if all are filled
        $activeComponents = \App\Models\Akademik\BobotNilaiSetting::getActiveComponents();
        $weights = \App\Models\Akademik\BobotNilaiSetting::getWeightsArray();
        
        // Prepare current values (existing or newly synced)
        $currentValues = [
            'tugas' => $nilaiMhs->tugas ?? null,
            'quiz' => $skorKeaktifan, // New value from sync
            'uts' => $nilaiMhs->uts ?? null,
            'uas' => $nilaiMhs->uas ?? null,
        ];

        // Check if all active components have values
        $allFilled = true;
        foreach ($activeComponents as $component) {
            $key = $component->component_key;
            if (!isset($currentValues[$key]) || $currentValues[$key] === null) {
                $allFilled = false;
                break;
            }
        }

        if ($allFilled) {
            $nilaiAkhir = 0;
            foreach ($activeComponents as $component) {
                $key = $component->component_key;
                $weight = ($weights[$key] ?? 0) / 100;
                $nilaiAkhir += ($currentValues[$key] * $weight);
            }

            $gradeData['nilai_akhir'] = $nilaiAkhir;
            $gradeData['huruf_nilai'] = $this->getGradeLetterFallback($nilaiAkhir);
        } else {
            // If not all filled, ensure final grades are null so they stay in "Belum Dinilai"
            $gradeData['nilai_akhir'] = null;
            $gradeData['huruf_nilai'] = null;
        }

        DB::table('nilai_mahasiswa')->updateOrInsert(['mata_kuliah_id' => $mkId, 'mahasiswa_id' => $mhsId], array_merge($gradeData, ['created_at' => now()]));
    }
```

## Verification Plan

### Manual Verification
- Since I cannot run the application, I will:
    1. Verify the logic in `AbsensiKelasController.php`.
    2. Confirm `BobotNilaiSetting::getActiveComponents()` and `getWeightsArray()` are used correctly to match dynamic settings.
    3. Ensure `NilaiController::getMahasiswaDataByMataKuliah` uses `nm.nilai_akhir IS NOT NULL` to determine `has_nilai`, which will now correctly return `0` if only attendance is synced.
