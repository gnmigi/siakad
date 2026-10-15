<?php

namespace App\Http\Controllers\Master\PMB;

use App\Http\Controllers\Controller;
use App\Models\PMB\PeriodePendaftaran;
use App\Models\PMB\JalurPendaftaran;
use App\Models\PMB\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftarExport;
use App\Models\PMB\PMBRequirementUpload;

class PMBManagementController extends Controller
{
    public function index()
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();
        $jalurs = JalurPendaftaran::orderBy('created_at', 'desc')->get();
        $pendaftars = Pendaftar::with(['periode', 'jalur'])->orderBy('created_at', 'desc')->get();
        
        // Get Tahun Akademik for dropdown
        $takas = \App\Models\Akademik\TahunAkademik::orderBy('name', 'desc')->get();
        
        // Calculate statistics for dashboard
        $stats = [
            'total_periode' => $periodes->count(),
            'periode_aktif' => $periodes->where('status', 'aktif')->count(),
            'total_pendaftar' => $pendaftars->count(),
            'pendaftar_validated' => $pendaftars->where('status_validasi', 'validated')->count(),
            'total_jalur' => $jalurs->count(),
            'total_gelombang' => 0, // This would need to be calculated based on your gelombang structure
        ];
        
        $pages = 'Manajemen PMB';
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $spref = $prefix . '.';
        
        return view('master.pmb.management-index', compact('periodes', 'jalurs', 'pendaftars', 'stats', 'pages', 'takas', 'spref'));
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'taka_id' => 'required|exists:tahun_akademiks,id',
            'start_date' => 'required|date',
            'ended_date' => 'required|date|after:start_date',
            'desc' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Convert date format from dd-mm-yyyy to yyyy-mm-dd
            $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
            $endedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->ended_date)->format('Y-m-d');
            
            PeriodePendaftaran::create([
                'name' => $request->name,
                'taka_id' => $request->taka_id,
                'start_date' => $startDate,
                'ended_date' => $endedDate,
                'desc' => $request->desc,
                'code' => uniqid('PERIODE_', true),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Periode pendaftaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan periode: ' . $e->getMessage());
        }
    }

    public function updatePeriode(Request $request, $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'taka_id' => 'required|exists:tahun_akademiks,id',
            'start_date' => 'required|date',
            'ended_date' => 'required|date|after:start_date',
            'desc' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $periode = PeriodePendaftaran::where('code', $code)->firstOrFail();
            $periode->update([
                'name' => $request->name,
                'taka_id' => $request->taka_id,
                'start_date' => $request->start_date,
                'ended_date' => $request->ended_date,
                'desc' => $request->desc,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Periode pendaftaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui periode: ' . $e->getMessage());
        }
    }

    public function deletePeriode($code)
    {
        try {
            DB::beginTransaction();
            
            $periode = PeriodePendaftaran::where('code', $code)->firstOrFail();
            
            // Check if there are pendaftar using this periode
            $pendaftarCount = Pendaftar::where('periode_id', $periode->id)->count();
            if ($pendaftarCount > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus periode karena masih ada ' . $pendaftarCount . ' calon mahasiswa yang menggunakan periode ini');
            }
            
            $periode->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Periode pendaftaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus periode: ' . $e->getMessage());
        }
    }

    public function updateJalur(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'periode_id' => 'required|exists:periode_pendaftarans,id',
            'desc' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $jalur = JalurPendaftaran::findOrFail($id);
            $jalur->update([
                'name' => $request->name,
                'periode_id' => $request->periode_id,
                'desc' => $request->desc,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Jalur pendaftaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui jalur: ' . $e->getMessage());
        }
    }

    public function storeJalur(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'periode_id' => 'required|exists:periode_pendaftarans,id',
            'desc' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            JalurPendaftaran::create([
                'name' => $request->name,
                'periode_id' => $request->periode_id,
                'desc' => $request->desc,
                'code' => uniqid('JALUR_', true),
                'status' => 'aktif',
                'created_by' => Auth::check() ? Auth::id() : null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Jalur pendaftaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan jalur: ' . $e->getMessage());
        }
    }

    public function updatePendaftarStatus(Request $request)
    {
        $request->validate([
            'pendaftar_id' => 'required|exists:pendaftars,id',
            'status' => 'required|in:baru,verifikasi,diterima,ditolak',
        ]);

        try {
            DB::beginTransaction();
            
            $pendaftar = Pendaftar::findOrFail($request->pendaftar_id);
            $pendaftar->update(['status' => $request->status]);

            DB::commit();
            return redirect()->back()->with('success', 'Status pendaftar berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function viewPendaftar($id)
    {
        try {
            $pendaftar = Pendaftar::with(['periode', 'jalur', 'prodi1', 'prodi2', 'prodi3'])->findOrFail($id);
            
            // Get all requirements
            $requirements = \App\Models\PMB\PMBRequirement::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();
            
            // Get uploads for this pendaftar
            $uploads = \App\Models\PMB\PMBRequirementUpload::where('pendaftar_id', $pendaftar->id)
                ->with('requirement')
                ->get()
                ->keyBy('requirement_id');
            
            $pages = 'Detail Calon Mahasiswa';
            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            $spref = $prefix . '.';
            
            return view('master.pmb.pendaftar-detail', compact('pendaftar', 'requirements', 'uploads', 'pages', 'spref'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Calon mahasiswa tidak ditemukan');
        }
    }

    public function deletePendaftar($id)
    {
        try {
            DB::beginTransaction();
            
            $pendaftar = Pendaftar::findOrFail($id);
            $pendaftar->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data calon mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus data calon mahasiswa: ' . $e->getMessage());
        }
    }

    public function deleteJalur($id)
    {
        try {
            DB::beginTransaction();
            
            $jalur = JalurPendaftaran::findOrFail($id);
            
            // Check if there are pendaftar using this jalur
            $pendaftarCount = Pendaftar::where('jalur_id', $id)->count();
            if ($pendaftarCount > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus jalur karena masih ada ' . $pendaftarCount . ' calon mahasiswa yang menggunakan jalur ini');
            }
            
            $jalur->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Jalur pendaftaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus jalur: ' . $e->getMessage());
        }
    }

    public function convertToMahasiswa(Request $request)
    {
        $request->validate([
            'pendaftar_id' => 'required|exists:pendaftars,id',
            'prodi_id' => 'required|exists:program_studis,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        try {
            DB::beginTransaction();
            
            $pendaftar = Pendaftar::findOrFail($request->pendaftar_id);
            
            // Check if already converted
            if ($pendaftar->mahasiswa_id && $pendaftar->mahasiswa_id > 0) {
                return redirect()->back()->with('error', 'Calon mahasiswa ini sudah dikonversi menjadi mahasiswa');
            }
            
            // Generate NIM
            $prodi = \App\Models\Akademik\ProgramStudi::findOrFail($request->prodi_id);
            $currentYear = date('Y');
            $prodiCode = str_pad($prodi->id, 2, '0', STR_PAD_LEFT);
            
            // Get last sequence number for this year and prodi
            $lastMahasiswa = \App\Models\Mahasantri::where('numb_nim', 'like', $currentYear . $prodiCode . '%')
                ->orderBy('numb_nim', 'desc')
                ->first();
            
            if ($lastMahasiswa) {
                $lastSequence = (int) substr($lastMahasiswa->numb_nim, -4);
                $newSequence = $lastSequence + 1;
            } else {
                $newSequence = 1;
            }
            
            $nim = $currentYear . $prodiCode . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
            
            // Create mahasiswa record
            $mahasiswa = \App\Models\Mahasantri::create([
                'numb_nim' => $nim,
                'name' => $pendaftar->name,
                'email' => $pendaftar->email,
                'phone' => $pendaftar->phone,
                'password' => $pendaftar->password, // Keep the same password
                'code' => 'MHS-' . uniqid(),
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id ?? 1, // Default kelas ID if not provided
                'taka_regist' => $currentYear,
                'taka_active' => $currentYear,
                'type' => 1, // Default type
                'semester' => 1, // Default semester
                'bio_gender' => $pendaftar->jenis_kelamin,
                'bio_placebirth' => $pendaftar->tempat_lahir,
                'bio_datebirth' => $pendaftar->tanggal_lahir,
                'ktp_addres' => $pendaftar->alamat,
                'numb_ktp' => $pendaftar->nik,
                'father_name' => $pendaftar->nama_ayah,
                'mother_name' => $pendaftar->nama_ibu,
                'edu1_place' => $pendaftar->asal_sekolah,
                'edu1_graduate_year' => $pendaftar->tahun_lulus,
                'edu1_type' => 'SMA/SMK',
                'photo' => $pendaftar->photo ?? 'default.jpg',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => Auth::id(),
            ]);
            
            // Update pendaftar record - use ENUM values that exist
            $pendaftar->update([
                'mahasiswa_id' => $mahasiswa->id,
                'status' => 'Lulus', // Use 'Lulus' which exists in ENUM
                'status_validasi' => 'validated',
                'converted_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Calon mahasiswa berhasil dikonversi menjadi mahasiswa dengan NIM: ' . $nim);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengkonversi calon mahasiswa: ' . $e->getMessage());
        }
    }

    public function approvePendaftar($id)
    {
        try {
            DB::beginTransaction();
            
            $pendaftar = Pendaftar::findOrFail($id);
            $pendaftar->update([
                'status' => 'Lulus', // Use 'Lulus' which exists in ENUM
                'status_validasi' => 'validated',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Calon mahasiswa berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyetujui calon mahasiswa: ' . $e->getMessage());
        }
    }

    public function rejectPendaftar(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            
            $pendaftar = Pendaftar::findOrFail($id);
            $pendaftar->update([
                'status' => 'Gagal', // Use 'Gagal' which exists in ENUM
                'status_validasi' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Calon mahasiswa berhasil ditolak');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menolak calon mahasiswa: ' . $e->getMessage());
        }
    }

    public function exportPendaftar(Request $request)
    {
        $type = $request->get('type', 'excel');
        
        // Get pendaftar data for export
        $pendaftars = Pendaftar::with(['periode', 'jalur'])->orderBy('created_at', 'desc')->get();
        
        if ($type === 'excel') {
            return Excel::download(new PendaftarExport($pendaftars), 'pendaftar-pmb.xlsx');
        } elseif ($type === 'pdf') {
            // For PDF export, you might want to create a separate export class
            return Excel::download(new PendaftarExport($pendaftars), 'pendaftar-pmb.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        }
        
        return redirect()->back()->with('error', 'Format export tidak valid');
    }

    public function validasiDokumen(Request $request, $code)
    {
        $request->validate([
            'upload_id' => 'required|exists:pmb_requirement_uploads,id',
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $upload = PMBRequirementUpload::findOrFail($request->upload_id);
        
        $upload->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);
        
        $statusText = $request->status == 'approved' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "Dokumen berhasil {$statusText}");
    }

    public function batchValidasiDokumen(Request $request, $code)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'upload_ids' => 'required|array|min:1',
            'upload_ids.*' => 'required|exists:pmb_requirement_uploads,id',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $pendaftar = Pendaftar::where('code', $code)->firstOrFail();
            
            // Get uploads that belong to this pendaftar
            $uploads = PMBRequirementUpload::whereIn('id', $request->upload_ids)
                ->where('pendaftar_id', $pendaftar->id)
                ->where('status', 'pending')
                ->get();
            
            if ($uploads->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diproses');
            }
            
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $adminNotes = $request->admin_notes;
            
            // Update all selected uploads
            $updatedCount = 0;
            foreach ($uploads as $upload) {
                $upload->update([
                    'status' => $status,
                    'admin_notes' => $adminNotes,
                    'validated_at' => now(),
                    'validated_by' => auth()->user()->id ?? null
                ]);
                $updatedCount++;
            }
            
            $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "{$updatedCount} dokumen berhasil {$actionText}";
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
