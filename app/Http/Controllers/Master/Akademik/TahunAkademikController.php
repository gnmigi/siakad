<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
// Use Models
use App\Models\Akademik\TahunAkademik;
use App\Models\Pengaturan\WebSetting;
use App\Services\AcademicYearAdvancementService;
// Use Plugins


class TahunAkademikController extends Controller
{
    /**
     * Parse date from various formats
     *
     * @param string $dateString
     * @return \Carbon\Carbon
     * @throws \Exception
     */
    private function parseDate($dateString)
    {
        // Try different date formats
        $formats = [
            'd-m-Y',    // 31-07-2025
            'Y-m-d',    // 2025-07-31
            'd/m/Y',    // 31/07/2025
            'Y/m/d',    // 2025/07/31
        ];
        
        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $dateString);
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // If all formats fail, try Carbon's parse method
        try {
            return \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            throw new \Exception('Invalid date format: ' . $dateString);
        }
    }

    public function renderTaka()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Tahun Akademik";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['taka'] = TahunAkademik::all();
        
        return view('master.akademik.taka-index', $data, compact('user'));
    }

    public function handleTaka(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate input - allow flexible date formats
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:Ganjil,Genap',
                'start_date' => 'required|string',
                'ended_date' => 'required|string',
                'desc' => 'nullable|string',
                'status' => 'required|in:Aktif,Tidak Aktif',
            ], [
                'name.required' => 'Nama tahun akademik wajib diisi',
                'name.max' => 'Nama tahun akademik maksimal 255 karakter',
                'type.required' => 'Tipe semester wajib dipilih',
                'type.in' => 'Tipe semester harus Ganjil atau Genap',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'ended_date.required' => 'Tanggal berakhir wajib diisi',
                'status.required' => 'Status wajib dipilih',
                'status.in' => 'Status harus Aktif atau Tidak Aktif',
            ]);

            // Parse and validate dates manually
            try {
                $startDate = $this->parseDate($validated['start_date']);
                $endDate = $this->parseDate($validated['ended_date']);
                
                if ($startDate >= $endDate) {
                    return redirect()->back()
                        ->withErrors(['ended_date' => 'Tanggal berakhir harus setelah tanggal mulai'])
                        ->withInput();
                }
                
                $validated['start_date'] = $startDate->format('Y-m-d');
                $validated['ended_date'] = $endDate->format('Y-m-d');
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['start_date' => 'Format tanggal tidak valid'])
                    ->withInput();
            }

            // Generate unique code
            do {
                $code = 'TAK-' . strtoupper(Str::random(6));
            } while (TahunAkademik::where('code', $code)->exists());

            // Create new Tahun Akademik
            $tahunAkademik = TahunAkademik::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'code' => $code,
                'start_date' => $validated['start_date'],
                'ended_date' => $validated['ended_date'],
                'desc' => $validated['desc'] ?? null,
                'status' => $validated['status'], // Use input status
                'created_by' => Auth::check() ? Auth::id() : null,
            ]);

            // Jika langsung diaktifkan saat create, update semester mahasiswa
            if ($validated['status'] === 'Aktif') {
                TahunAkademik::where('status', 'Aktif')
                    ->where('id', '!=', $tahunAkademik->id)
                    ->update(['status' => 'Tidak Aktif']);
                
                // Update semester mahasiswa yang bukan calon mahasiswa dan bukan alumni
                \App\Models\Mahasantri::where('type', '!=', 0) // Bukan calon mahasiswa
                    ->where('type', '!=', 3) // Bukan alumni
                    ->increment('semester');
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'Tahun Akademik "' . $tahunAkademik->name . '" berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Tahun Akademik: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show actual error message for debugging
            $errorMessage = 'Gagal menambahkan Tahun Akademik: ' . $e->getMessage();
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $errorMessage = 'Database error: ' . str_replace('SQLSTATE[HY000]: General error: ', '', $e->getMessage());
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function updateTaka(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            // Validate input - allow flexible date formats
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:Ganjil,Genap',
                'start_date' => 'required|string',
                'ended_date' => 'required|string',
                'desc' => 'nullable|string',
                'status' => 'required|in:Aktif,Tidak Aktif',
            ]);

            // Parse and validate dates manually
            try {
                $startDate = $this->parseDate($validated['start_date']);
                $endDate = $this->parseDate($validated['ended_date']);
                
                if ($startDate >= $endDate) {
                    return redirect()->back()
                        ->withErrors(['ended_date' => 'Tanggal berakhir harus setelah tanggal mulai'])
                        ->withInput();
                }
                
                $validated['start_date'] = $startDate->format('Y-m-d');
                $validated['ended_date'] = $endDate->format('Y-m-d');
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['start_date' => 'Format tanggal tidak valid'])
                    ->withInput();
            }

            $taka = TahunAkademik::where('code', $code)->firstOrFail();

            // If status is being changed to Aktif, set all other records to Tidak Aktif
            if ($validated['status'] === 'Aktif' && $taka->status !== 'Aktif') {
                TahunAkademik::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);
                
                // Update semester mahasiswa yang bukan calon mahasiswa dan bukan alumni
                \App\Models\Mahasantri::where('type', '!=', 0) // Bukan calon mahasiswa
                    ->where('type', '!=', 3) // Bukan alumni
                    ->increment('semester');
            }

            $taka->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'start_date' => $validated['start_date'],
                'ended_date' => $validated['ended_date'],
                'desc' => $validated['desc'],
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Akademik berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Tahun Akademik: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteTaka($code)
    {
        try {
            DB::beginTransaction();

            $taka = TahunAkademik::where('code', $code)->firstOrFail();
            
            // Check if this is the active academic year
            if ($taka->status === 'Aktif') {
                return redirect()->back()->with('error', 'Tidak dapat menghapus Tahun Akademik yang sedang aktif');
            }

            $taka->update([
                'deleted_by' => Auth::id()
            ]);
            $taka->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Akademik berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Tahun Akademik: ' . $e->getMessage());
        }
    }

    /**
     * Preview academic year advancement
     */
    public function previewAdvancement()
    {
        try {
            $advancementService = new AcademicYearAdvancementService();
            
            // Validate if advancement can be performed
            $validation = $advancementService->validateAdvancement();
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat melakukan kenaikan tahun akademik',
                    'errors' => $validation['errors']
                ], 400);
            }

            // Get preview data
            $previewData = $advancementService->previewAdvancement();
            
            return response()->json([
                'success' => true,
                'message' => 'Preview kenaikan tahun akademik berhasil',
                'data' => $previewData,
                'warnings' => $validation['warnings']
            ]);

        } catch (\Exception $e) {
            Log::error('Preview Academic Year Advancement Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute automatic academic year advancement
     */
    public function autoAdvanceAcademicYear()
    {
        try {
            $advancementService = new AcademicYearAdvancementService();
            
            // Validate if advancement can be performed
            $validation = $advancementService->validateAdvancement();
            
            if (!$validation['valid']) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat melakukan kenaikan tahun akademik: ' . implode(', ', $validation['errors']));
            }

            // Execute advancement
            $result = $advancementService->executeAdvancement();
            
            if ($result['success']) {
                $message = $result['message'];
                $data = $result['data'];
                
                // Add detailed success message
                $detailMessage = sprintf(
                    'Tahun akademik berhasil dinaikkan dari "%s" ke "%s". %d mahasiswa naik semester, %d mahasiswa drop out.',
                    $data['previous_academic_year']['name'],
                    $data['new_academic_year']['name'],
                    $data['advancement_results']['advanced_count'],
                    $data['advancement_results']['dropped_out_count']
                );
                
                return redirect()->back()->with('success', $detailMessage);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Auto Advance Academic Year Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal melakukan kenaikan tahun akademik otomatis: ' . $e->getMessage());
        }
    }
}
