<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// Use Models
use App\Models\Mahasantri;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TakhassusProgramStudi;
use App\Models\Pengaturan\WebSetting;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // Assuming dompdf is aliased as PDF in config/app.php
use App\Imports\MahasiswaImport;
use Illuminate\Support\Facades\Validator;
use App\Services\TerminalService;

class DataAkademikMahasiswaController extends Controller
{
    public function renderDataAkademikMahasiswa(Request $request)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Data Akademik Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Build query with filters
        $query = Mahasantri::with(['prodi', 'kelas.taka', 'kelas.pstudi', 'kelas.jenisKelas', 'takhasusOption']);
        
        // Apply filters
        if ($request->filled('prodi_filter')) {
            $query->where('prodi_id', $request->prodi_filter);
        }
        
        if ($request->filled('kelas_filter')) {
            $query->where('kelas_id', $request->kelas_filter);
        }
        
        if ($request->filled('semester_filter')) {
            $query->where('semester', $request->semester_filter);
        }
        
        if ($request->filled('takhasus_filter')) {
            $query->where('takhasus', 'LIKE', '%' . $request->takhasus_filter . '%');
        }
        
        if ($request->filled('status_filter')) {
            $query->where('type', $request->status_filter);
        }
        
        if ($request->filled('terminal_filter')) {
            if ($request->terminal_filter == '1') {
                // Show only terminated students
                $query->where('is_terminated', true);
            } elseif ($request->terminal_filter == '0') {
                // Show only active (non-terminated) students
                $query->where('is_terminated', false);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('numb_nim', 'LIKE', '%' . $search . '%');
            });
        }
        
        $data['mahasiswa'] = $query->get();
        $data['program_studi'] = ProgramStudi::where('status', 'Aktif')->get();
        $data['tahun_akademik'] = TahunAkademik::where('status', 'Aktif')->get();
        $data['kelas'] = Kelas::with(['pstudi', 'taka', 'jenisKelas'])->get();
        
        // Get unique takhasus values for filter dropdown
        $data['takhasus_list'] = Mahasantri::whereNotNull('takhasus')
            ->where('takhasus', '!=', '')
            ->distinct()
            ->pluck('takhasus')
            ->sort();
        
        // Get unique semester values from actual data
        $data['semester_list'] = Mahasantri::whereNotNull('semester')
            ->where('semester', '!=', '')
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();
        
        // Pass current filters to view
        $data['current_filters'] = [
            'prodi_filter' => $request->prodi_filter,
            'kelas_filter' => $request->kelas_filter,
            'semester_filter' => $request->semester_filter,
            'takhasus_filter' => $request->takhasus_filter,
            'status_filter' => $request->status_filter,
            'terminal_filter' => $request->terminal_filter,
            'search' => $request->search,
        ];
        
        return view('master.akademik.data-akademik-mahasiswa-index', $data, compact('user'));
    }

    public function searchMahasiswa(Request $request)
    {
        // Build query with filters and relationships
        $query = Mahasantri::with(['prodi', 'kelas.taka', 'kelas.pstudi', 'kelas.jenisKelas', 'takhasusOption']);
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('numb_nim', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Apply other filters if present
        if ($request->filled('prodi_filter')) {
            $query->where('prodi_id', $request->prodi_filter);
        }

        if ($request->filled('kelas_filter')) {
            $query->where('kelas_id', $request->kelas_filter);
        }

        if ($request->filled('semester_filter')) {
            $query->where('semester', $request->semester_filter);
        }

        if ($request->filled('takhasus_filter')) {
            $query->where('takhasus', 'LIKE', '%' . $request->takhasus_filter . '%');
        }

        if ($request->filled('status_filter')) {
            $query->where('type', $request->status_filter);
        }

        if ($request->filled('terminal_filter')) {
            if ($request->terminal_filter == '1') {
                // Show only terminated students
                $query->where('is_terminated', true);
            } elseif ($request->terminal_filter == '0') {
                // Show only active (non-terminated) students
                $query->where('is_terminated', false);
            }
        }

        $mahasiswa = $query->get();

        // Format data for JSON response
        $formattedData = $mahasiswa->map(function($item, $key) {
            return [
                'no' => $key + 1,
                'code' => $item->code,
                'name' => $item->name,
                'email' => $item->email,
                'photo' => $item->photo,
                'numb_nim' => $item->numb_nim,
                'prodi_name' => $item->prodi->name ?? '-',
                'kelas_name' => $item->kelas ? $item->kelas->name . ' - ' . ($item->kelas->jenisKelas->name ?? 'Reguler') : '-',
                'semester' => $item->semester ?? '-',
                'takhasus' => $item->takhasusOption->name ?? $item->takhasus ?? '-',
                'terminal_status' => $item->getTerminalStatusBadge(),
                'terminal_date' => $item->terminal_date ? $item->terminal_date->format('d/m/Y') : '-',
                'is_terminal' => $item->isTerminal()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'total' => count($formattedData)
        ]);
    }

    public function updateDataAkademikMahasiswa(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'numb_nim' => 'required|string|max:20|unique:mahasiswas,numb_nim,' . $code . ',code',
                'prodi_id' => 'required|exists:program_studis,id',
                'kelas_id' => 'required|exists:kelas,id',
                'semester' => 'required|integer|min:1|max:14',
                'takhasus_id' => 'nullable|exists:takhasus_program_studis,id',
            ]);

            $mahasiswa = Mahasantri::where('code', $code)->firstOrFail();

            // Get takhasus name if takhasus_id is provided
            $takhasusName = '';
            if ($request->takhasus_id) {
                $takhasusOption = TakhassusProgramStudi::find($request->takhasus_id);
                $takhasusName = $takhasusOption ? $takhasusOption->name : '';
            }

            $mahasiswa->update([
                'numb_nim' => $request->numb_nim,
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
                'semester' => $request->semester,
                'takhasus_id' => $request->takhasus_id,
                'takhasus' => $takhasusName, // Keep the old field for backward compatibility
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Akademik Mahasantri berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Data Akademik Mahasantri: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getTakhasusOptions(Request $request)
    {
        $prodiId = $request->get('prodi_id');
        
        if (!$prodiId) {
            return response()->json([]);
        }

        $takhasusOptions = TakhassusProgramStudi::where('program_studi_id', $prodiId)
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        return response()->json($takhasusOptions);
    }

    public function updateProfileMahasiswa(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mahasiswas,email,' . $code . ',code',
                'phone' => 'required|string|unique:mahasiswas,phone,' . $code . ',code',
                'bio_gender' => 'nullable|in:Laki-laki,Perempuan',
                'bio_placebirth' => 'nullable|string|max:255',
                'bio_datebirth' => 'nullable|date',
                'bio_religion' => 'nullable|string|max:255',
                'bio_nationality' => 'nullable|string|max:255',
                'numb_ktp' => 'nullable|string|unique:mahasiswas,numb_ktp,' . $code . ',code',
                'numb_kk' => 'nullable|string|unique:mahasiswas,numb_kk,' . $code . ',code',
                'numb_nisn' => 'nullable|string|unique:mahasiswas,numb_nisn,' . $code . ',code',
                'ktp_addres' => 'nullable|string',
                'ktp_rt' => 'nullable|string|max:10',
                'ktp_rw' => 'nullable|string|max:10',
                'ktp_village' => 'nullable|string|max:255',
                'ktp_subdistrict' => 'nullable|string|max:255',
                'ktp_city' => 'nullable|string|max:255',
                'ktp_province' => 'nullable|string|max:255',
                'ktp_poscode' => 'nullable|string|max:10',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'father_phone' => 'nullable|string|max:20',
                'mother_phone' => 'nullable|string|max:20',
                'father_occupation' => 'nullable|string|max:255',
                'mother_occupation' => 'nullable|string|max:255',
                'edu1_type' => 'nullable|in:SMA/SMK,Pesantren,Diploma,Sarjana,Magister,Doktor',
                'edu1_place' => 'nullable|string|max:255',
                'edu1_major' => 'nullable|string|max:255',
                'edu1_graduate_year' => 'nullable|string|max:4',
            ]);

            // Cari mahasiswa berdasarkan code
            $mahasiswa = Mahasantri::where('code', $code)->firstOrFail();

            // Update data profile mahasiswa
            $mahasiswa->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'bio_gender' => $request->bio_gender,
                'bio_placebirth' => $request->bio_placebirth,
                'bio_datebirth' => $request->bio_datebirth,
                'bio_religion' => $request->bio_religion,
                'bio_nationality' => $request->bio_nationality ?? 'Indonesia',
                'numb_ktp' => $request->numb_ktp,
                'numb_kk' => $request->numb_kk,
                'numb_nisn' => $request->numb_nisn,
                'ktp_addres' => $request->ktp_addres,
                'ktp_rt' => $request->ktp_rt,
                'ktp_rw' => $request->ktp_rw,
                'ktp_village' => $request->ktp_village,
                'ktp_subdistrict' => $request->ktp_subdistrict,
                'ktp_city' => $request->ktp_city,
                'ktp_province' => $request->ktp_province,
                'ktp_poscode' => $request->ktp_poscode,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'father_phone' => $request->father_phone,
                'mother_phone' => $request->mother_phone,
                'father_occupation' => $request->father_occupation,
                'mother_occupation' => $request->mother_occupation,
                'edu1_type' => $request->edu1_type ?? 'SMA/SMK',
                'edu1_place' => $request->edu1_place,
                'edu1_major' => $request->edu1_major,
                'edu1_graduate_year' => $request->edu1_graduate_year,
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Profile mahasiswa berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profile mahasiswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function exportDataAkademikMahasiswa(Request $request)
    {
        $format = $request->get('format', 'excel');

        // Build query with filters
        $query = Mahasantri::with(['prodi', 'kelas.jenisKelas']);

        if ($request->filled('prodi_filter')) {
            $query->where('prodi_id', $request->prodi_filter);
        }

        if ($request->filled('kelas_filter')) {
            $query->where('kelas_id', $request->kelas_filter);
        }

        if ($request->filled('semester_filter')) {
            $query->where('semester', $request->semester_filter);
        }

        if ($request->filled('takhasus_filter')) {
            $query->where('takhasus', 'LIKE', '%' . $request->takhasus_filter . '%');
        }

        if ($request->filled('status_filter')) {
            $query->where('type', $request->status_filter);
        }

        if ($request->filled('terminal_filter')) {
            if ($request->terminal_filter == '1') {
                // Show only terminated students
                $query->where('is_terminated', true);
            } elseif ($request->terminal_filter == '0') {
                // Show only active (non-terminated) students
                $query->where('is_terminated', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('numb_nim', 'LIKE', '%' . $search . '%');
            });
        }

        $mahasiswa = $query->get();

        if ($format === 'excel') {
            // Build grouped export data from all kelas master so empty classes are still exported as sheets
            $kelasMasterQuery = Kelas::with('jenisKelas');

            if ($request->filled('kelas_filter')) {
                $kelasMasterQuery->where('id', $request->kelas_filter);
            }

            $kelasMaster = $kelasMasterQuery->get();
            $grouped = collect();

            foreach ($kelasMaster as $kelas) {
                $jenisKelas = $kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler';
                $sheetName = $kelas->name . ' - ' . $jenisKelas;

                $kelasMahasiswa = $mahasiswa->filter(function ($item) use ($kelas) {
                    return (int) $item->kelas_id === (int) $kelas->id;
                })->values();

                $grouped->put($sheetName, $kelasMahasiswa);
            }

            // Include "Tanpa Kelas" sheet when any students have no kelas assigned
            $tanpaKelas = $mahasiswa->filter(function ($item) {
                return empty($item->kelas_id);
            })->values();

            if ($tanpaKelas->isNotEmpty()) {
                $grouped->put('Tanpa Kelas', $tanpaKelas);
            }

            return Excel::download(new \App\Exports\MahasiswaGroupedExport($grouped), 'data-mahasiswa-' . date('Ymd') . '.xlsx');
        } elseif ($format === 'pdf') {
            // Group by kelas name with jenis kelas
            $grouped = $mahasiswa->groupBy(function($item) {
                if ($item->kelas) {
                    $jenisKelas = $item->kelas->jenisKelas ? $item->kelas->jenisKelas->name : 'Reguler';
                    return $item->kelas->name . ' - ' . $jenisKelas;
                }
                return 'Tanpa Kelas';
            });

            $pdf = PDF::loadView('master.akademik.data-akademik-mahasiswa-pdf', compact('grouped'));
            return $pdf->download('data-mahasiswa-' . date('Ymd') . '.pdf');
        } else {
            return redirect()->back()->with('error', 'Format export tidak didukung');
        }
    }

    public function importDataAkademikMahasiswa(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx',
            'replace_existing' => 'nullable|boolean',
        ], [
            'import_file.required' => 'File import wajib dipilih',
            'import_file.file' => 'File yang dipilih tidak valid',
            'import_file.mimes' => 'File harus berformat Excel (.xlsx)',
        ]);

        $replaceExisting = $request->has('replace_existing') && $request->replace_existing;

        try {
            DB::beginTransaction();
            
            $import = new MahasiswaImport($replaceExisting);
            Excel::import($import, $request->file('import_file'));
            
            DB::commit();
            
            // Get import statistics
            $stats = $import->getImportStats();
            
            // Store detailed results in session for modal display
            $importResults = [
                'success' => true,
                'statistics' => [
                    'imported' => $stats['imported'],
                    'updated' => $stats['updated'],
                    'skipped' => $stats['skipped'],
                    'failed' => $stats['failed'],
                    'total_processed' => $stats['imported'] + $stats['updated'] + $stats['skipped'] + $stats['failed']
                ],
                'details' => [
                    'errors' => $this->formatErrorsForDisplay($stats['errors'] ?? []),
                    'warnings' => $stats['warnings'] ?? [],
                    'success_items' => $this->generateSuccessItems($stats)
                ],
                'filename' => $request->file('import_file')->getClientOriginalName(),
                'timestamp' => now()->format('d/m/Y H:i:s'),
                'replace_mode' => $replaceExisting
            ];

            // Store in session for modal display
            session(['import_results' => $importResults]);

            return redirect()->back()->with('import_success', 'Import berhasil diselesaikan! Klik untuk melihat detail hasil.');
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            
            $failures = $e->failures();
            $validationErrors = [];
            
            foreach ($failures as $failure) {
                $validationErrors[] = [
                    'row' => $failure->row(),
                    'columns' => implode(', ', $failure->attribute()),
                    'errors' => implode(', ', $failure->errors()),
                    'values' => $failure->values(),
                    'type' => 'validation'
                ];
            }
            
            $importResults = [
                'success' => false,
                'error_type' => 'validation',
                'error_title' => 'Validasi Excel Gagal',
                'error_message' => 'File Excel mengandung data yang tidak valid. Silakan periksa format dan isi data.',
                'statistics' => [
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'failed' => count($validationErrors),
                    'total_processed' => count($validationErrors)
                ],
                'details' => [
                    'errors' => $validationErrors,
                    'warnings' => [],
                    'success_items' => []
                ],
                'filename' => $request->file('import_file')->getClientOriginalName(),
                'timestamp' => now()->format('d/m/Y H:i:s'),
                'replace_mode' => $replaceExisting
            ];

            session(['import_results' => $importResults]);
            return redirect()->back()->with('import_error', 'Import gagal! Klik untuk melihat detail error.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Categorize error type
            $errorType = $this->categorizeImportError($e);
            
            $importResults = [
                'success' => false,
                'error_type' => $errorType['type'],
                'error_title' => $errorType['title'],
                'error_message' => $errorType['message'],
                'statistics' => [
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'failed' => 1,
                    'total_processed' => 1
                ],
                'details' => [
                    'errors' => [[
                        'row' => 'System',
                        'columns' => 'General',
                        'errors' => $e->getMessage(),
                        'values' => [],
                        'type' => $errorType['type']
                    ]],
                    'warnings' => [],
                    'success_items' => []
                ],
                'filename' => $request->file('import_file')->getClientOriginalName(),
                'timestamp' => now()->format('d/m/Y H:i:s'),
                'replace_mode' => $replaceExisting,
                'technical_details' => $e->getMessage()
            ];

            session(['import_results' => $importResults]);
            return redirect()->back()->with('import_error', 'Import gagal! Klik untuk melihat detail error.');
        }
    }

    /**
     * Format errors for display in modal
     */
    private function formatErrorsForDisplay($errors)
    {
        $formattedErrors = [];
        
        foreach ($errors as $error) {
            // If error is already in the correct format, use it
            if (is_array($error) && isset($error['row']) && isset($error['columns']) && isset($error['errors'])) {
                $formattedErrors[] = $error;
            } else {
                // If error is a string, try to parse it
                $errorString = is_string($error) ? $error : (string) $error;
                
                // Try to extract row number from error message
                $rowMatch = [];
                if (preg_match('/Baris (\d+):/', $errorString, $rowMatch)) {
                    $row = $rowMatch[1];
                    $message = preg_replace('/Baris \d+:\s*/', '', $errorString);
                } else {
                    $row = 'Unknown';
                    $message = $errorString;
                }
                
                $formattedErrors[] = [
                    'row' => $row,
                    'columns' => 'Data tidak valid',
                    'errors' => $message,
                    'type' => 'validation'
                ];
            }
        }
        
        return $formattedErrors;
    }

    /**
     * Generate success items for display
     */
    private function generateSuccessItems($stats)
    {
        $successItems = [];
        
        // Get actual success items from import stats if available
        if (isset($stats['success_details']) && is_array($stats['success_details'])) {
            return $stats['success_details'];
        }
        
        // Fallback: Generate generic success entries based on statistics
        for ($i = 1; $i <= $stats['imported']; $i++) {
            $successItems[] = [
                'row' => $i,
                'action' => 'imported',
                'message' => 'Data mahasiswa berhasil diimport',
                'type' => 'success',
                'name' => 'Data baris ' . $i
            ];
        }
        
        for ($i = 1; $i <= $stats['updated']; $i++) {
            $successItems[] = [
                'row' => $stats['imported'] + $i,
                'action' => 'updated',
                'message' => 'Data mahasiswa berhasil diperbarui',
                'type' => 'info',
                'name' => 'Data baris ' . ($stats['imported'] + $i)
            ];
        }
        
        for ($i = 1; $i <= $stats['skipped']; $i++) {
            $successItems[] = [
                'row' => $stats['imported'] + $stats['updated'] + $i,
                'action' => 'skipped',
                'message' => 'Data mahasiswa dilewati (duplikat)',
                'type' => 'warning',
                'name' => 'Data baris ' . ($stats['imported'] + $stats['updated'] + $i)
            ];
        }
        
        return $successItems;
    }

    /**
     * Categorize import errors for better user understanding
     */
    private function categorizeImportError($exception)
    {
        $message = $exception->getMessage();
        
        if (strpos($message, 'Integrity constraint violation') !== false) {
            if (strpos($message, 'email_unique') !== false) {
                return [
                    'type' => 'duplicate_email',
                    'title' => 'Duplikasi Email',
                    'message' => 'Email yang sama sudah ada di database. Centang opsi "Replace Data yang Ada" atau gunakan email yang berbeda.'
                ];
            } elseif (strpos($message, 'phone_unique') !== false) {
                return [
                    'type' => 'duplicate_phone',
                    'title' => 'Duplikasi Nomor Telepon',
                    'message' => 'Nomor telepon yang sama sudah ada di database. Centang opsi "Replace Data yang Ada" atau gunakan nomor yang berbeda.'
                ];
            } elseif (strpos($message, 'nim_unique') !== false) {
                return [
                    'type' => 'duplicate_nim',
                    'title' => 'Duplikasi NIM',
                    'message' => 'NIM yang sama sudah ada di database. Centang opsi "Replace Data yang Ada" atau gunakan NIM yang berbeda.'
                ];
            } else {
                return [
                    'type' => 'duplicate_data',
                    'title' => 'Duplikasi Data',
                    'message' => 'Ada data yang sama sudah ada di database. Centang opsi "Replace Data yang Ada".'
                ];
            }
        } elseif (strpos($message, 'Foreign key constraint') !== false) {
            return [
                'type' => 'invalid_reference',
                'title' => 'Referensi Data Tidak Valid',
                'message' => 'Program Studi atau Kelas yang direferensikan tidak ditemukan. Pastikan Program Studi dan Kelas sudah ada di sistem.'
            ];
        } elseif (strpos($message, 'Column') !== false && strpos($message, 'cannot be null') !== false) {
            return [
                'type' => 'missing_required',
                'title' => 'Data Wajib Kosong',
                'message' => 'Ada kolom wajib yang tidak diisi. Pastikan kolom Nama, Email, dan NIM terisi.'
            ];
        } else {
            return [
                'type' => 'system_error',
                'title' => 'Error Sistem',
                'message' => 'Terjadi kesalahan teknis saat memproses data. Silakan coba lagi atau hubungi administrator.'
            ];
        }
    }

    /**
     * Get import results from session (for AJAX requests)
     */
    public function getImportResults(Request $request)
    {
        $results = session('import_results');
        
        if (!$results) {
            return response()->json([
                'success' => false,
                'message' => 'No import results found'
            ], 404);
        }

        // Clear session after retrieving
        session()->forget('import_results');
        
        return response()->json($results);
    }

    public function toggleTerminal(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'terminal_reason' => 'required_if:action,activate|string|max:500',
                'action' => 'required|in:activate,deactivate'
            ], [
                'terminal_reason.required_if' => 'Alasan terminal wajib diisi saat mengaktifkan terminal',
                'terminal_reason.max' => 'Alasan terminal maksimal 500 karakter',
                'action.required' => 'Aksi terminal harus dipilih',
                'action.in' => 'Aksi terminal tidak valid'
            ]);

            $mahasiswa = Mahasantri::where('code', $code)->firstOrFail();

            if ($request->action === 'activate') {
                // Activate terminal
                $mahasiswa->update([
                    'is_terminated' => true,
                    'termination_reason' => $request->terminal_reason,
                    'terminated_at' => now(),
                    'terminated_by' => Auth::id(),
                ]);

                $message = 'Terminal berhasil diaktifkan untuk ' . $mahasiswa->name;
                $logMessage = 'Terminal diaktifkan dengan alasan: ' . $request->terminal_reason;
            } else {
                // Deactivate terminal
                $mahasiswa->update([
                    'is_terminated' => false,
                    'termination_reason' => null,
                    'terminated_at' => null,
                    'terminated_by' => null,
                ]);

                $message = 'Terminal berhasil dinonaktifkan untuk ' . $mahasiswa->name;
                $logMessage = 'Terminal dinonaktifkan';
            }

            DB::commit();
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengubah status terminal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function bulkTerminal(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'selected_students' => 'required|json',
                'bulk_action' => 'required|in:activate,deactivate',
                'bulk_terminal_reason' => 'required_if:bulk_action,activate|nullable|string|max:500',
            ], [
                'selected_students.required' => 'Pilih minimal satu mahasiswa',
                'selected_students.json' => 'Data mahasiswa tidak valid',
                'bulk_action.required' => 'Aksi terminal harus dipilih',
                'bulk_action.in' => 'Aksi terminal tidak valid',
                'bulk_terminal_reason.required_if' => 'Alasan terminal wajib diisi saat mengaktifkan terminal',
                'bulk_terminal_reason.max' => 'Alasan terminal maksimal 500 karakter',
            ]);

            $selectedStudents = json_decode($request->selected_students, true);
            
            if (empty($selectedStudents)) {
                return redirect()->back()->with('error', 'Tidak ada mahasiswa yang dipilih');
            }

            $mahasiswaList = Mahasantri::whereIn('code', $selectedStudents)->get();
            
            if ($mahasiswaList->count() !== count($selectedStudents)) {
                return redirect()->back()->with('error', 'Beberapa mahasiswa tidak ditemukan');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($mahasiswaList as $mahasiswa) {
                try {
                    if ($request->bulk_action === 'activate') {
                        // Activate terminal
                        $mahasiswa->update([
                            'is_terminated' => true,
                            'termination_reason' => $request->bulk_terminal_reason,
                            'terminated_at' => now(),
                            'terminated_by' => Auth::id(),
                        ]);
                    } else {
                        // Deactivate terminal
                        $mahasiswa->update([
                            'is_terminated' => false,
                            'termination_reason' => null,
                            'terminated_at' => null,
                            'terminated_by' => null,
                        ]);
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Gagal memproses {$mahasiswa->name}: " . $e->getMessage();
                }
            }

            DB::commit();

            $action = $request->bulk_action === 'activate' ? 'diaktifkan' : 'dinonaktifkan';
            $message = "Terminal berhasil {$action} untuk {$successCount} mahasiswa";
            
            if ($errorCount > 0) {
                $message .= ". {$errorCount} mahasiswa gagal diproses.";
                if (!empty($errors)) {
                    $message .= "\n\nDetail Error:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... dan " . (count($errors) - 5) . " error lainnya.";
                    }
                }
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal melakukan terminal massal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Auto-terminal check based on unpaid SPP bills
     */
    public function autoTerminalCheck(Request $request)
    {
        try {
            $terminalService = app(TerminalService::class);
            $dryRun = $request->boolean('dry_run', false);
            
            if ($dryRun) {
                // Perform dry run to show what would be terminated
                $activeStudents = Mahasantri::where('type', 1)
                    ->with(['prodi'])
                    ->get();

                $wouldTerminate = [];
                $wouldReactivate = [];
                
                foreach ($activeStudents as $mahasiswa) {
                    // Check current status and what would happen
                    $unpaidSppBills = $this->getUnpaidSppBills($mahasiswa);
                    
                    if ($mahasiswa->isTerminal()) {
                        // Check if would be reactivated
                        if ($this->wasTerminatedForUnpaidSpp($mahasiswa) && $unpaidSppBills->isEmpty()) {
                            $wouldReactivate[] = [
                                'code' => $mahasiswa->code,
                                'name' => $mahasiswa->name,
                                'nim' => $mahasiswa->numb_nim ?? $mahasiswa->id,
                                'prodi' => $mahasiswa->prodi->name ?? 'N/A',
                                'semester' => $mahasiswa->semester,
                                'reason' => 'Tagihan SPP sudah lunas'
                            ];
                        }
                    } else {
                        // Check if would be terminated
                        if ($unpaidSppBills->isNotEmpty() && $this->shouldTerminateStudent($mahasiswa, $unpaidSppBills)) {
                            $wouldTerminate[] = [
                                'code' => $mahasiswa->code,
                                'name' => $mahasiswa->name,
                                'nim' => $mahasiswa->numb_nim ?? $mahasiswa->id,
                                'prodi' => $mahasiswa->prodi->name ?? 'N/A',
                                'semester' => $mahasiswa->semester,
                                'unpaid_count' => $unpaidSppBills->count(),
                                'unpaid_amount' => $unpaidSppBills->sum('amount')
                            ];
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'dry_run' => true,
                    'would_terminate' => $wouldTerminate,
                    'would_reactivate' => $wouldReactivate,
                    'terminate_count' => count($wouldTerminate),
                    'reactivate_count' => count($wouldReactivate)
                ]);
            } else {
                // Perform actual bulk check
                $terminatedCount = 0;
                $reactivatedCount = 0;
                
                $allStudents = Mahasantri::where('type', 1)->get();
                
                foreach ($allStudents as $mahasiswa) {
                    $result = $terminalService->checkAutoTerminal($mahasiswa);
                    if ($result) {
                        if ($mahasiswa->fresh()->isTerminal()) {
                            $terminatedCount++;
                        } else {
                            $reactivatedCount++;
                        }
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'dry_run' => false,
                    'terminated_count' => $terminatedCount,
                    'reactivated_count' => $reactivatedCount,
                    'message' => "Auto-terminal check selesai. {$terminatedCount} mahasiswa di-terminal, {$reactivatedCount} mahasiswa direaktivasi."
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan auto-terminal check: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get terminal status for student (AJAX)
     */
    public function getTerminalStatus($code)
    {
        try {
            $mahasiswa = Mahasantri::where('code', $code)->with(['prodi', 'tagihanKuliah'])->firstOrFail();
            $terminalService = app(TerminalService::class);
            $terminalStatus = $terminalService->getTerminalStatus($mahasiswa);
            
            return response()->json([
                'success' => true,
                'status' => $terminalStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat status terminal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Graduate single student
     */
    public function graduateStudent(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'graduation_date' => 'required|date',
                'graduation_note' => 'nullable|string|max:500',
            ], [
                'graduation_date.required' => 'Tanggal kelulusan wajib diisi',
                'graduation_date.date' => 'Format tanggal kelulusan tidak valid',
                'graduation_note.max' => 'Catatan kelulusan maksimal 500 karakter',
            ]);

            $mahasiswa = Mahasantri::where('code', $code)->firstOrFail();

            // Validate student eligibility
            if ($mahasiswa->getRawTypeAttribute() !== 1) {
                return redirect()->back()->with('error', 'Hanya mahasiswa aktif yang dapat diluluskan');
            }

            if ($mahasiswa->semester < 7) {
                return redirect()->back()->with('error', 'Mahasiswa harus minimal semester 7 untuk dapat diluluskan');
            }

            // Graduate the student
            $mahasiswa->update([
                'type' => 3, // Alumni
                'graduated_at' => $request->graduation_date,
                'graduation_note' => $request->graduation_note,
                'graduated_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Mahasiswa ' . $mahasiswa->name . ' berhasil diluluskan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal meluluskan mahasiswa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Bulk graduate students
     */
    public function bulkGraduate(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'selected_students' => 'required|json',
                'bulk_graduation_date' => 'required|date',
                'bulk_graduation_note' => 'nullable|string|max:500',
            ], [
                'selected_students.required' => 'Pilih minimal satu mahasiswa',
                'selected_students.json' => 'Data mahasiswa tidak valid',
                'bulk_graduation_date.required' => 'Tanggal kelulusan wajib diisi',
                'bulk_graduation_date.date' => 'Format tanggal kelulusan tidak valid',
                'bulk_graduation_note.max' => 'Catatan kelulusan maksimal 500 karakter',
            ]);

            $selectedStudents = json_decode($request->selected_students, true);
            
            if (empty($selectedStudents)) {
                return redirect()->back()->with('error', 'Tidak ada mahasiswa yang dipilih');
            }

            $mahasiswaList = Mahasantri::whereIn('code', $selectedStudents)->get();
            
            if ($mahasiswaList->count() !== count($selectedStudents)) {
                return redirect()->back()->with('error', 'Beberapa mahasiswa tidak ditemukan');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($mahasiswaList as $mahasiswa) {
                try {
                    // Validate eligibility
                    if ($mahasiswa->getRawTypeAttribute() !== 1) {
                        $errors[] = "{$mahasiswa->name}: Hanya mahasiswa aktif yang dapat diluluskan";
                        $errorCount++;
                        continue;
                    }

                    if ($mahasiswa->semester < 7) {
                        $errors[] = "{$mahasiswa->name}: Harus minimal semester 7 untuk dapat diluluskan";
                        $errorCount++;
                        continue;
                    }

                    // Graduate the student
                    $mahasiswa->update([
                        'type' => 3, // Alumni
                        'graduated_at' => $request->bulk_graduation_date,
                        'graduation_note' => $request->bulk_graduation_note,
                        'graduated_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Gagal meluluskan {$mahasiswa->name}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil meluluskan {$successCount} mahasiswa";
            
            if ($errorCount > 0) {
                $message .= ". {$errorCount} mahasiswa gagal diproses.";
                if (!empty($errors)) {
                    $message .= "\n\nDetail Error:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... dan " . (count($errors) - 5) . " error lainnya.";
                    }
                }
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal melakukan kelulusan massal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get graduation candidates (students eligible for graduation)
     */
    public function getGraduationCandidates(Request $request)
    {
        try {
            // Get students who are eligible for graduation (semester >= 7 and active)
            $candidates = Mahasantri::where('type', 1) // Active students only
                ->where('semester', '>=', 7) // Semester 7 or higher
                ->with(['prodi', 'kelas'])
                ->get();

            $formattedCandidates = $candidates->map(function($student) {
                return [
                    'code' => $student->code,
                    'name' => $student->name,
                    'nim' => $student->numb_nim,
                    'prodi' => $student->prodi->name ?? 'N/A',
                    'kelas' => $student->kelas ? $student->kelas->name : 'N/A',
                    'semester' => $student->semester,
                ];
            });

            return response()->json([
                'success' => true,
                'candidates' => $formattedCandidates,
                'total' => $candidates->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat kandidat kelulusan: ' . $e->getMessage()
            ]);
        }
    }

    // Helper methods for dry run (copied from TerminalService)
    private function getUnpaidSppBills($mahasiswa)
    {
        return \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Pending')
            ->where(function($query) {
                $query->where('desc', 'LIKE', '%SPP%')
                      ->orWhere('desc', 'LIKE', '%spp%')
                      ->orWhereHas('group', function($q) {
                          $q->where('name', 'LIKE', '%SPP%')
                            ->orWhere('name', 'LIKE', '%spp%');
                      });
            })
            ->with(['tahunAkademik', 'group'])
            ->get();
    }

    private function wasTerminatedForUnpaidSpp($mahasiswa)
    {
        $reason = $mahasiswa->termination_reason ?? '';
        $autoTerminalKeywords = [
            'Terminal otomatis karena tunggakan SPP',
            'Auto-terminal',
            'Sistem otomatis',
            'Tunggakan SPP'
        ];

        foreach ($autoTerminalKeywords as $keyword) {
            if (stripos($reason, $keyword) !== false) {
                return true;
            }
        }

        return ($mahasiswa->terminated_by == 1 && stripos($reason, 'SPP') !== false);
    }

    private function shouldTerminateStudent($mahasiswa, $unpaidBills)
    {
        $currentSemester = $mahasiswa->semester ?? 1;
        
        if ($currentSemester <= 1) {
            return false;
        }

        $criticalUnpaidBills = $unpaidBills->filter(function($bill) {
            if ($bill->status === 'Cicilan') {
                return false;
            }

            if ($bill->due_date && \Carbon\Carbon::parse($bill->due_date)->addDays(60)->isPast()) {
                return true;
            }

            return false;
        });

        return $criticalUnpaidBills->isNotEmpty();
    }
}
