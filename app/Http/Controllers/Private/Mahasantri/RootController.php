<?php

namespace App\Http\Controllers\Private\Mahasantri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Mahasantri;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;
use App\Models\Publikasi\Pengumuman;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RealRashid\SweetAlert\Facades\Alert;

class RootController extends Controller
{
    public function renderDashboard()
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();
        
        // Load mahasiswa with all necessary relationships for dashboard
        $mahasiswa = Mahasantri::with(['prodi', 'kelas.taka', 'dosenPenasehat', 'terminalBy'])->find($mahasiswaId);
        
        // Check if this is an alumni - redirect to alumni dashboard
        if ($mahasiswa && $mahasiswa->getRawTypeAttribute() === 3) {
            return redirect()->route('alumni.dashboard-render');
        }
        
        // Check if student is in terminal status and prepare popup alert
        if ($mahasiswa && $mahasiswa->isTerminal()) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            $message = 'Anda sedang dalam status terminal. Akses ke beberapa fitur akademik dibatasi.';
            if ($terminalInfo && $terminalInfo['reason']) {
                $message .= ' Alasan: ' . $terminalInfo['reason'];
            }
            if ($terminalInfo && $terminalInfo['date']) {
                $message .= ' Sejak: ' . $terminalInfo['date'];
            }

            // Keep package alert
            Alert::warning('Status Terminal', $message);
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'mahasiswa.'; // Fixed: Always use mahasiswa prefix for mahasiswa routes
        $data['menus'] = "Dashboard";
        $data['pages'] = "Dashboard Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Load published announcements for dashboard
        $data['announcements'] = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();
        
        // Get current academic year
        $currentTahunAkademik = TahunAkademik::where('status', 'Aktif')->first();
        
        // Get statistics for dashboard
        $data['stats'] = [
            'mata_kuliah' => 0,
            'tugas_pending' => 0,
            'kehadiran' => 0,
            'total_tagihan' => 0,
            'tagihan_pending' => 0,
            'total_paid' => 0
        ];
        
        if ($currentTahunAkademik && $mahasiswa) {
            // Get current KRS for mata kuliah count
            $currentKrs = \App\Models\Mahasantri\KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $currentTahunAkademik->id)
                ->where('semester', $mahasiswa->semester)
                ->first();
            
            if (!$currentKrs) {
                // Fallback to latest approved KRS
                $currentKrs = \App\Models\Mahasantri\KrsMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                    ->where('status', 'approved')
                    ->orderBy('semester', 'desc')
                    ->first();
            }
            
            if ($currentKrs) {
                // Count mata kuliah from KRS details
                $krsDetails = \App\Models\Mahasantri\KrsDetailMahasiswa::where('krs_mahasiswa_id', $currentKrs->id)->get();
                $data['stats']['mata_kuliah'] = $krsDetails->count();
                $mataKuliahIds = $krsDetails->pluck('mata_kuliah_id');

                // 1. Calculate Kehadiran (Attendance Percentage)
                $kelasName = $mahasiswa->kelas->name ?? '';
                if ($mataKuliahIds->isNotEmpty() && !empty($kelasName)) {
                    $cleanKel = $kelasName;
                    $exactKel = $kelasName . ' (Semester Aktif ' . $currentKrs->semester . ')';
                    
                    $meetings = \App\Models\Akademik\PertemuanKuliah::whereIn('mata_kuliah_id', $mataKuliahIds)
                        ->where(function($q) use ($cleanKel, $exactKel) {
                            $q->where('kelompok', $exactKel)
                              ->orWhere('kelompok', 'like', $cleanKel . ' %')
                              ->orWhere('kelompok', $cleanKel);
                        })
                        ->get();
                    
                    $meetingIds = $meetings->pluck('id');
                    
                    if ($meetingIds->isNotEmpty()) {
                        $totalMeetings = $meetingIds->count();
                        
                        $totalHadir = \App\Models\Akademik\AbsensiMahasiswa::whereIn('pertemuan_id', $meetingIds)
                            ->where('mahasiswa_id', $mahasiswa->id)
                            ->where('status', 'Hadir')
                            ->count();
                        
                        $data['stats']['kehadiran'] = round(($totalHadir / $totalMeetings) * 100, 2);
                    } else {
                        $data['stats']['kehadiran'] = 0;
                    }
                } else {
                    $data['stats']['kehadiran'] = 0;
                }

                // 2. Calculate Tugas Pending (courses in KRS with no final grade yet)
                $gradedCoursesCount = \App\Models\NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                    ->whereIn('mata_kuliah_id', $mataKuliahIds)
                    ->whereNotNull('nilai_akhir')
                    ->count();
                
                $data['stats']['tugas_pending'] = max(0, $data['stats']['mata_kuliah'] - $gradedCoursesCount);
            }
            
            // Get payment statistics from new payment system
            try {
                if (class_exists('\App\Models\Keuangan\TagihanKuliah')) {
                    $data['stats']['total_tagihan'] = \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)->count();
                    
                    // Fetch unpaid and partially paid (cicilan) bills
                    $unpaidTagihan = \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                        ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
                        ->get();
                    
                    // Calculate remaining pending amount
                    $totalRemainingAmount = 0;
                    foreach ($unpaidTagihan as $tagihan) {
                        $remainingAmount = $tagihan->remaining_amount ?? ($tagihan->amount - ($tagihan->paid_amount ?? 0));
                        $totalRemainingAmount += $remainingAmount;
                    }
                    
                    $data['stats']['tagihan_pending'] = $totalRemainingAmount;
                    
                    // Sum up verified payment transactions
                    $data['stats']['total_paid'] = \App\Models\Keuangan\PaymentTransaction::where('mahasiswa_id', $mahasiswa->id)
                        ->where('status', 'verified')
                        ->sum('total_amount');
                }
            } catch (\Exception $e) {
                // If tables don't exist yet, keep default values
            }
        }
        
        // Get recent unpaid/installment tagihan for dashboard from new system
        $data['recentTagihan'] = [];
        try {
            if (class_exists('\App\Models\Keuangan\TagihanKuliah')) {
                $data['recentTagihan'] = \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                    ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Cicilan', 'cicilan', 'CICILAN'])
                    ->orderBy('due_date')
                    ->take(3)
                    ->get();
            }
        } catch (\Exception $e) {
            // If tables don't exist yet, keep empty array
        }
        
        return view('central.backpage.dashboard-mahasiswa', $data, compact('mahasiswa'));
    }

    public function renderAlumniDashboard()
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();
        
        // Load alumni with all necessary relationships for dashboard
        $mahasiswa = Mahasantri::with(['prodi', 'kelas.taka', 'dosenPenasehat'])->find($mahasiswaId);
        
        // Verify this is actually an alumni
        if (!$mahasiswa || $mahasiswa->getRawTypeAttribute() !== 3) {
            Alert::error('Error', 'Akses ditolak. Anda bukan alumni.');
            return redirect()->route('auth.render-signin');
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'alumni.'; // Use alumni prefix for alumni routes
        $data['menus'] = "Dashboard";
        $data['pages'] = "Dashboard Alumni";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Load published announcements for dashboard
        $data['announcements'] = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();
        
        // Get graduation information
        $data['graduation_info'] = [
            'graduated_at' => $mahasiswa->graduated_at ? $mahasiswa->graduated_at->format('d F Y') : 'Tidak tersedia',
            'graduation_note' => $mahasiswa->graduation_note ?? 'Tidak ada catatan',
            'final_semester' => $mahasiswa->semester ?? 'Tidak tersedia'
        ];
        
        // Get alumni statistics for dashboard
        $data['stats'] = [
            'total_semester' => $mahasiswa->semester ?? 0,
            'program_studi' => $mahasiswa->prodi->name ?? 'Tidak tersedia',
            'kelas_terakhir' => $mahasiswa->kelas ? $mahasiswa->kelas->name : 'Tidak tersedia',
            'total_tagihan' => 0,
            'total_paid' => 0
        ];
        
        // Get payment history statistics
        try {
            if (class_exists('\App\Models\Keuangan\TagihanKuliah')) {
                $data['stats']['total_tagihan'] = \App\Models\Keuangan\TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)->count();
                $data['stats']['total_paid'] = \App\Models\Keuangan\PaymentTransaction::where('mahasiswa_id', $mahasiswa->id)
                    ->where('status', 'verified')
                    ->sum('total_amount');
            }
        } catch (\Exception $e) {
            // If tables don't exist yet, keep default values
        }
        
        return view('central.backpage.dashboard-alumni', $data, compact('mahasiswa'));
    }

    public function renderProfile()
    {
        $mahasiswaId = Auth::guard('mahasiswa')->id();
        
        // Load mahasiswa with relationships for readonly display
        $mahasiswa = Mahasantri::with(['prodi', 'kelas.taka', 'kelas.jenisKelas', 'terminalBy'])->find($mahasiswaId);

        // Show terminal alert in profile page as well
        if ($mahasiswa && $mahasiswa->isTerminal()) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            $message = 'Anda sedang dalam status terminal. Akses ke beberapa fitur akademik dibatasi.';
            if ($terminalInfo && $terminalInfo['reason']) {
                $message .= ' Alasan: ' . $terminalInfo['reason'];
            }
            if ($terminalInfo && $terminalInfo['date']) {
                $message .= ' Sejak: ' . $terminalInfo['date'];
            }
            Alert::warning('Status Terminal', $message);
        }
        
        $data['webs'] = WebSetting::first();
        $data['spref'] = 'mahasiswa.'; // Fixed: Always use mahasiswa prefix for mahasiswa routes
        $data['menus'] = "Profile";
        $data['pages'] = "Profile Mahasantri";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        return view('central.backpage.profile-mahasiswa', $data, compact('mahasiswa'));
    }

    public function handleProfile(Request $request)
    {
        try {
            $mahasiswa = Auth::guard('mahasiswa')->user();

            // Validate the request (REMOVED ACADEMIC DATA VALIDATION - ONLY ADMIN CAN EDIT)
            $validator = Validator::make($request->all(), [
                // Personal Information
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:mahasiswas,email,' . $mahasiswa->id,
                'phone' => 'required|string|unique:mahasiswas,phone,' . $mahasiswa->id,
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=100,min_height=100',
                
                // Password fields
                'current_password' => 'nullable|string|min:6',
                'password' => 'nullable|string|min:6|confirmed',

                // Personal Details
                'pointer_id' => 'nullable',
                'bio_placebirth' => 'nullable|string|max:100',
                'bio_datebirth' => 'nullable|date',
                'bio_gender' => 'nullable|in:Laki-laki,Perempuan',
                'pointer_reli' => 'nullable',
                'bio_religion' => 'nullable|string|max:50',
                'bio_nationality' => 'nullable|string|max:50',
                'bio_blood' => 'nullable|string|max:5',
                'bio_height' => 'nullable|numeric',
                'bio_weight' => 'nullable|numeric',

                // Contact Information
                'link_ig' => 'nullable|url',
                'link_fb' => 'nullable|url',
                'link_in' => 'nullable|url',

                // Address Information
                'ktp_addres' => 'nullable|string',
                'ktp_rt' => 'nullable|string|max:10',
                'ktp_rw' => 'nullable|string|max:10',
                'ktp_village' => 'nullable|string|max:100',
                'ktp_subdistrict' => 'nullable|string|max:100',
                'ktp_city' => 'nullable|string|max:100',
                'ktp_province' => 'nullable|string|max:100',
                'ktp_poscode' => 'nullable|string|max:10',
                'domicile_same' => 'nullable|in:Yes,No',
                'domicile_addres' => 'nullable|string',
                'domicile_rt' => 'nullable|string|max:10',
                'domicile_rw' => 'nullable|string|max:10',
                'domicile_village' => 'nullable|string|max:100',
                'domicile_subdistrict' => 'nullable|string|max:100',
                'domicile_city' => 'nullable|string|max:100',
                'domicile_province' => 'nullable|string|max:100',
                'domicile_poscode' => 'nullable|string|max:10',

                // Parent Information (Fixed field names to match database schema)
                'father_name' => 'nullable|string|max:255',
                'father_occupation' => 'nullable|string|max:255',
                'father_income' => 'nullable|string|max:50',
                'father_phone' => 'nullable|string|max:20',
                'mother_name' => 'nullable|string|max:255',
                'mother_occupation' => 'nullable|string|max:255',
                'mother_income' => 'nullable|string|max:50',
                'mother_phone' => 'nullable|string|max:20',

                // Education Information
                'edu1_type' => 'nullable|in:SMA/SMK,Pesantren,Diploma,Sarjana,Magister,Doktor',
                'edu_school_name' => 'nullable|string|max:255',
                'edu_school_address' => 'nullable|string',
                'edu_school_major' => 'nullable|string|max:255',
                'edu_school_graduate_year' => 'nullable|string|max:4',
                'edu_school_certificate_number' => 'nullable|string|max:100',

                // Identity Information
                'numb_kk' => 'nullable|string|max:20',
                'numb_ktp' => 'nullable|string|max:20',
                'numb_nisn' => 'nullable|string|max:20',
            ], [
                'photo.image' => 'Foto profil harus berupa gambar',
                'photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG',
                'photo.max' => 'Foto profil maksimal 2MB',
                'photo.dimensions' => 'Foto minimal 100x100 pixel',
                'name.required' => 'Nama lengkap wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.unique' => 'Nomor telepon sudah digunakan',
                'current_password.min' => 'Password lama minimal 6 karakter',
                'password.min' => 'Password baru minimal 6 karakter',
                'password.confirmed' => 'Konfirmasi password baru tidak cocok',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $validator->validated();

            // Handle password change
            if ($request->filled('current_password') && $request->filled('password')) {
                if (!Hash::check($request->current_password, $mahasiswa->password)) {
                    return redirect()->back()->with('error', 'Password lama tidak sesuai')->withInput();
                }
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }
            unset($data['current_password']);
            unset($data['password_confirmation']);

            // SECURITY: Remove any academic data from the request to prevent unauthorized updates
            $academicFields = ['nim', 'numb_nim', 'prodi_id', 'kelas_id', 'taka_id', 'semester', 'takhasus'];
            foreach ($academicFields as $field) {
                unset($data[$field]);
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if not default
                if ($mahasiswa->photo && !str_contains($mahasiswa->photo, 'default.jpg')) {
                    $oldPhoto = str_replace(asset('storage/images/profile/'), '', $mahasiswa->photo);
                    Storage::disk('public')->delete('images/profile/' . $oldPhoto);
                }
            
                // Get original file extension to preserve format
                $originalExtension = $request->photo->getClientOriginalExtension();
                $photoName = time() . '-' . $mahasiswa->id . '-' . uniqid() . '.' . $originalExtension;
                
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($request->photo->getRealPath());
                
                // Resize if too large
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200); 
                }
                
                // Save in original format to preserve transparency
                if (strtolower($originalExtension) === 'png') {
                    Storage::disk('public')->put('images/profile/' . $photoName, $image->toPng());
                } else {
                    Storage::disk('public')->put('images/profile/' . $photoName, $image->toJpeg(90));
                }
                
                $data['photo'] = $photoName;
            }

            // Remove photo from data if no file uploaded
            if (!isset($data['photo'])) {
                unset($data['photo']);
            }

            // Filter out empty values to avoid overwriting existing data
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            // Update mahasiswa data (ONLY PERSONAL DATA - NO ACADEMIC DATA)
            if (!empty($data)) {
                $mahasiswa->update($data);
            }

            return redirect()->back()->with('success', 'Profile berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui profile: ' . $e->getMessage())->withInput();
        }
    }
}
