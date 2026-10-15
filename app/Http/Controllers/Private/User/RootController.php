<?php

namespace App\Http\Controllers\Private\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
// USE MODELS
use App\Models\User;
use App\Models\Pengaturan\WebSetting;
// USE PLUGINS
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RealRashid\SweetAlert\Facades\Alert;

class RootController extends Controller
{
    public function renderDashboard()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Dashboard";
        $data['pages'] = "Home";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Check if user is finance department (type 2)
        $isFinanceUser = ($user && $user->raw_type === 2);
        
        if ($isFinanceUser) {
            // Finance-specific dashboard data
            $data = $this->getFinanceDashboardData($data);
            
            // Handle auto-refresh AJAX request
            if (request()->has('finance_refresh') || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'totalMahasiswa' => $data['totalMahasiswa'] ?? 0,
                        'monthlyRevenue' => $data['monthlyRevenue'] ?? 0,
                        'pendingPayments' => $data['pendingPayments'] ?? 0,
                        'outstandingBalance' => $data['outstandingBalance'] ?? 0,
                        'lastUpdated' => $data['lastUpdated'] ?? '-'
                    ]
                ]);
            }
        } else {
            // Default admin dashboard data
            $data = $this->getDefaultDashboardData($data);
        }
        
        // Use different views based on user type
        if ($isFinanceUser) {
            return view('central.finance-dashboard', $data, compact('user'));
        } else {
            return view('central.back-content', $data, compact('user'));
        }
    }

    private function getFinanceDashboardData($data)
    {
        try {
            $now = Carbon::now();

            $paymentModel = \App\Models\Keuangan\PaymentTransaction::query();
            $tagihanModel = \App\Models\Keuangan\TagihanKuliah::query();
            $mahasiswaModel = \App\Models\Mahasantri::query();

            $data['totalMahasiswa'] = $mahasiswaModel->count();
            $data['totalTagihan'] = (clone $tagihanModel)->count();
            $data['totalPembayaran'] = (clone $paymentModel)->count();
            $data['totalSaldo'] = \App\Models\Keuangan\Saldo::query()->sum('amount');

            $paidStatuses = ['paid', 'verified', 'success', 'sukses'];

            $data['pendingPayments'] = (clone $paymentModel)
                ->whereIn('status', ['pending', 'waiting', 'menunggu'])
                ->count();

            $data['monthlyRevenue'] = (clone $paymentModel)
                ->whereIn('status', $paidStatuses)
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->sum('paid_amount');

            $data['outstandingBalance'] = (clone $tagihanModel)
                ->sum(\Illuminate\Support\Facades\DB::raw('GREATEST(COALESCE(amount,0) - COALESCE(paid_amount,0), 0)'));

            $data['completedPayments'] = (clone $paymentModel)
                ->whereIn('status', $paidStatuses)
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->count();

            $data['recentPayments'] = \App\Models\Keuangan\PaymentTransaction::query()
                ->with(['mahasiswa:id,name', 'paymentMethod:id,name'])
                ->orderByRaw('COALESCE(paid_at, created_at) DESC')
                ->take(8)
                ->get()
                ->map(function ($payment) {
                    $payment->description = ($payment->mahasiswa->name ?? 'Mahasantri') . ' • ' . ($payment->paymentMethod->name ?? 'Metode tidak diketahui');
                    $payment->amount = (float) ($payment->paid_amount ?? $payment->total_amount ?? 0);
                    return $payment;
                });

            $data['recentTagihan'] = \App\Models\Keuangan\TagihanKuliah::query()
                ->with('mahasiswa:id,name')
                ->latest('created_at')
                ->take(8)
                ->get()
                ->map(function ($tagihan) {
                    $tagihan->name = ($tagihan->mahasiswa->name ?? 'Mahasantri') . ' • ' . ($tagihan->desc ?? 'Tagihan');
                    $tagihan->amount = (float) ($tagihan->amount ?? 0);
                    return $tagihan;
                });

            $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
            $monthlySummaries = \App\Models\Keuangan\PaymentTransaction::query()
                ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(paid_amount) as amount')
                ->whereIn('status', $paidStatuses)
                ->where('paid_at', '>=', $sixMonthsAgo)
                ->groupByRaw('YEAR(paid_at), MONTH(paid_at)')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            $trendLabels = [];
            $trendValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $key = $month->format('Y-m');
                $summary = $monthlySummaries->get($key);

                $trendLabels[] = $month->translatedFormat('M');
                $trendValues[] = $summary ? (float)$summary->amount : 0.0;
            }

            $methodRows = \App\Models\Keuangan\PaymentTransaction::query()
                ->selectRaw('payment_method_id, COUNT(*) as total')
                ->groupBy('payment_method_id')
                ->with('paymentMethod:id,name')
                ->get();

            $methodLabels = [];
            $methodValues = [];
            foreach ($methodRows as $row) {
                $methodLabels[] = $row->paymentMethod->name ?? 'Tidak diketahui';
                $methodValues[] = (int) $row->total;
            }

            if (empty($methodLabels)) {
                $methodLabels = ['Belum ada data'];
                $methodValues = [1];
            }

            $data['paymentTrends'] = [
                'labels' => $trendLabels,
                'values' => $trendValues,
            ];

            $data['paymentMethods'] = [
                'labels' => $methodLabels,
                'values' => $methodValues,
            ];

            $data['lastUpdated'] = Carbon::now()->format('d/m/Y H:i:s');
        } catch (\Exception $e) {
            Log::error('getFinanceDashboardData failed', [
                'message' => $e->getMessage(),
            ]);

            $data['totalMahasiswa'] = 0;
            $data['totalTagihan'] = 0;
            $data['totalPembayaran'] = 0;
            $data['totalSaldo'] = 0;
            $data['pendingPayments'] = 0;
            $data['monthlyRevenue'] = 0;
            $data['outstandingBalance'] = 0;
            $data['completedPayments'] = 0;
            $data['recentPayments'] = collect();
            $data['recentTagihan'] = collect();
            $data['paymentTrends'] = ['labels' => ['Belum ada data'], 'values' => [0]];
            $data['paymentMethods'] = ['labels' => ['Belum ada data'], 'values' => [1]];
            $data['lastUpdated'] = Carbon::now()->format('d/m/Y H:i:s');
        }

        return $data;
    }

    private function getDefaultDashboardData($data)
    {
        try {
            $data['totalMahasiswa'] = \App\Models\Mahasantri::count();
            $data['totalDosen'] = \App\Models\Dosen::count();
            $data['totalUsers'] = \App\Models\User::whereIn('raw_type', [1, 2, 3, 4])->count();
            $data['totalMataKuliah'] = \App\Models\Akademik\MataKuliah::count();
            $data['totalKelas'] = \App\Models\Akademik\Kelas::count();
            $data['totalProdi'] = \App\Models\Akademik\ProgramStudi::count();
            $data['totalFakultas'] = \App\Models\Akademik\Fakultas::count();
            $data['totalTahunAkademik'] = \App\Models\Akademik\TahunAkademik::count();
            
            // Get pending KRS count
            $data['pendingKrs'] = \App\Models\Mahasantri\KrsMahasiswa::where('status', 'submitted')->count();
            
            // Get recent announcements
            $data['recentAnnouncements'] = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')
                ->latest()
                ->take(5)
                ->get();
                
            // Get recent news
            $data['recentNews'] = \App\Models\Publikasi\Berita::where('status', 'Publish')
                ->latest()
                ->take(5)
                ->get();
                
            // Get program studi distribution for chart
            $data['prodiDistribution'] = \App\Models\Akademik\ProgramStudi::withCount('mahasiswas')
                ->orderBy('mahasiswas_count', 'desc')
                ->take(5)
                ->get();
                
            // Get gender distribution
            $data['genderMahasiswa'] = [
                'male' => \App\Models\Mahasantri::where('bio_gender', 'Laki-laki')->count(),
                'female' => \App\Models\Mahasantri::where('bio_gender', 'Perempuan')->count()
            ];
            
            $data['genderDosen'] = [
                'male' => \App\Models\Dosen::where('bio_gender', 'Laki-laki')->count(),
                'female' => \App\Models\Dosen::where('bio_gender', 'Perempuan')->count()
            ];

            // Realtime recent activities (Pendaftaran, KRS Submit, Pembayaran/Transfer)
            $pendaftarActivities = \App\Models\PMB\Pendaftar::query()
                ->latest('created_at')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'pendaftaran',
                        'title' => 'Pendaftaran Mahasantri Baru',
                        'description' => ($item->name ?? 'Pendaftar') . ' baru mendaftar',
                        'icon' => 'fa-user-plus',
                        'colorClass' => 'text-success',
                        'time' => $item->created_at,
                    ];
                });

            $krsActivities = collect();

            // Prefer model in App\Models\Mahasantri\KrsMahasiswa (new module)
            try {
                $krsActivities = \App\Models\Mahasantri\KrsMahasiswa::query()
                    ->with('mahasiswa:id,name')
                    ->where(function ($q) {
                        $q->whereIn('status', ['submitted', 'diajukan', 'pending', 'menunggu'])
                          ->orWhereNotNull('submitted_at');
                    })
                    ->orderByRaw('COALESCE(submitted_at, updated_at, created_at) DESC')
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'krs',
                            'title' => 'Submit KRS',
                            'description' => ($item->mahasiswa->name ?? 'Mahasantri') . ' baru submit KRS',
                            'icon' => 'fa-file-alt',
                            'colorClass' => 'text-warning',
                            'time' => $item->submitted_at ?? $item->updated_at ?? $item->created_at,
                        ];
                    });
            } catch (\Throwable $th) {
                $krsActivities = collect();
            }

            // Fallback to legacy model App\Models\Akademik\KrsMahasiswa if available/used by existing menu KRS
            if ($krsActivities->isEmpty() && class_exists('\App\Models\Akademik\KrsMahasiswa')) {
                try {
                    $krsActivities = \App\Models\Akademik\KrsMahasiswa::query()
                        ->with('mahasiswa:id,name')
                        ->where(function ($q) {
                            $q->whereIn('status', ['submitted', 'diajukan', 'pending', 'menunggu'])
                              ->orWhereNotNull('submitted_at');
                        })
                        ->orderByRaw('COALESCE(submitted_at, updated_at, created_at) DESC')
                        ->take(10)
                        ->get()
                        ->map(function ($item) {
                            return [
                                'type' => 'krs',
                                'title' => 'Submit KRS',
                                'description' => ($item->mahasiswa->name ?? 'Mahasantri') . ' baru submit KRS',
                                'icon' => 'fa-file-alt',
                                'colorClass' => 'text-warning',
                                'time' => $item->submitted_at ?? $item->updated_at ?? $item->created_at,
                            ];
                        });
                } catch (\Throwable $th) {
                    $krsActivities = collect();
                }
            }

            $paymentActivities = \App\Models\Keuangan\PaymentTransaction::query()
                ->with(['mahasiswa:id,name', 'paymentMethod:id,name'])
                ->where(function ($q) {
                    $q->where('status', \App\Models\Keuangan\PaymentTransaction::STATUS_PAID)
                      ->orWhere('status', \App\Models\Keuangan\PaymentTransaction::STATUS_VERIFIED);
                })
                ->latest('paid_at')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    $methodName = strtolower($item->paymentMethod->name ?? '');
                    $isTransfer = str_contains($methodName, 'transfer') || str_contains($methodName, 'bank');

                    return [
                        'type' => 'payment',
                        'title' => $isTransfer ? 'Transfer Bank Diterima' : 'Pembayaran SPP',
                        'description' => ($item->mahasiswa->name ?? 'Mahasantri') . ' melakukan pembayaran ' . $item->getFormattedAmount('total_amount'),
                        'icon' => 'fa-money-check-alt',
                        'colorClass' => 'text-primary',
                        'time' => $item->paid_at ?? $item->updated_at ?? $item->created_at,
                    ];
                });

            $data['recentActivities'] = collect()
                ->merge($pendaftarActivities)
                ->merge($krsActivities)
                ->merge($paymentActivities)
                ->sortByDesc('time')
                ->take(10)
                ->values();
            
        } catch (\Exception $e) {
            // Log error detail supaya tidak silent fail
            Log::error('getDefaultDashboardData failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Keep existing summary stats (if any) and only fallback safely for activity-related keys
            $data['totalMahasiswa'] = $data['totalMahasiswa'] ?? 0;
            $data['totalDosen'] = $data['totalDosen'] ?? 0;
            $data['totalUsers'] = $data['totalUsers'] ?? 0;
            $data['totalMataKuliah'] = $data['totalMataKuliah'] ?? 0;
            $data['totalKelas'] = $data['totalKelas'] ?? 0;
            $data['totalProdi'] = $data['totalProdi'] ?? 0;
            $data['totalFakultas'] = $data['totalFakultas'] ?? 0;
            $data['totalTahunAkademik'] = $data['totalTahunAkademik'] ?? 0;
            $data['pendingKrs'] = $data['pendingKrs'] ?? 0;
            $data['recentAnnouncements'] = $data['recentAnnouncements'] ?? collect();
            $data['recentNews'] = $data['recentNews'] ?? collect();
            $data['prodiDistribution'] = $data['prodiDistribution'] ?? collect();
            $data['genderMahasiswa'] = $data['genderMahasiswa'] ?? ['male' => 0, 'female' => 0];
            $data['genderDosen'] = $data['genderDosen'] ?? ['male' => 0, 'female' => 0];

            // IMPORTANT: jangan kosongkan activity total, coba fallback minimal KRS saja
            try {
                $data['recentActivities'] = \App\Models\Mahasantri\KrsMahasiswa::query()
                    ->with('mahasiswa:id,name')
                    ->where(function ($q) {
                        $q->whereIn('status', ['submitted', 'diajukan', 'pending', 'menunggu'])
                          ->orWhereNotNull('submitted_at');
                    })
                    ->orderByRaw('COALESCE(submitted_at, updated_at, created_at) DESC')
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'krs',
                            'title' => 'Submit KRS',
                            'description' => ($item->mahasiswa->name ?? 'Mahasantri') . ' baru submit KRS',
                            'icon' => 'fa-file-alt',
                            'colorClass' => 'text-warning',
                            'time' => $item->submitted_at ?? $item->updated_at ?? $item->created_at,
                        ];
                    })
                    ->values();
            } catch (\Throwable $th) {
                Log::error('Fallback recentActivities failed', [
                    'message' => $th->getMessage(),
                ]);
                $data['recentActivities'] = collect();
            }
        }
        
        return $data;
    }

    public function renderProfile()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Detail";
        $data['pages'] = "Profile";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        return view('central.backpage.profile-index', $data, compact('user'));
    }

    public function handleProfile(Request $request)
    {
        try {
            // Debug: Log bahwa method dipanggil
            Log::info('handleProfile method called', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['photo', '_token'])
            ]);

            // Validate the request - INCLUDING foto
            $validator = Validator::make($request->all(), [
                // Personal Information - INCLUDING foto
                'name' => 'nullable|string|max:255',
                'title_front' => 'nullable|string|max:50',
                'title_behind' => 'nullable|string|max:50',
                'bio_placebirth' => 'nullable|string|max:100',
                'bio_datebirth' => 'nullable|date',
                'bio_gender' => 'nullable|in:Laki-laki,Perempuan',
                'bio_religion' => 'nullable|string|max:50',
                'bio_nationality' => 'nullable|string|max:50',
                'bio_blood' => 'nullable|string|max:5',
                'bio_height' => 'nullable|numeric',
                'bio_weight' => 'nullable|numeric',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=100,min_height=100',

                // Contact Information
                'email' => 'nullable|email|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|unique:users,phone,' . Auth::id(),
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

                // Education Information
                'edu1_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu1_place' => 'nullable|string|max:255',
                'edu1_major' => 'nullable|string|max:255',
                'edu1_average_score' => 'nullable|string|max:10',
                'edu1_graduate_year' => 'nullable|string|max:4',
                'edu2_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu2_place' => 'nullable|string|max:255',
                'edu2_major' => 'nullable|string|max:255',
                'edu2_average_score' => 'nullable|string|max:10',
                'edu2_graduate_year' => 'nullable|string|max:4',
                'edu3_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu3_place' => 'nullable|string|max:255',
                'edu3_major' => 'nullable|string|max:255',
                'edu3_average_score' => 'nullable|string|max:10',
                'edu3_graduate_year' => 'nullable|string|max:4',

                // Identity Information
                'numb_kk' => 'nullable|string|max:20',
                'numb_ktp' => 'nullable|string|max:20',
                'numb_npsn' => 'nullable|string|max:20',
                'numb_nitk' => 'nullable|string|max:20',
                'numb_staff' => 'nullable|string|max:20',
            ], [
                'photo.image' => 'Foto profil harus berupa gambar',
                'photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG',
                'photo.max' => 'Foto profil maksimal 2MB',
                'photo.dimensions' => 'Foto minimal 100x100 pixel',
                // Custom error messages
                'name.required' => 'Nama lengkap wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.unique' => 'Nomor telepon sudah digunakan',
                'edu1_type.required' => 'Jenis pendidikan pertama wajib diisi',
                'edu1_place.required' => 'Institusi pendidikan pertama wajib diisi',
                'edu1_major.required' => 'Jurusan pendidikan pertama wajib diisi',
                'edu1_average_score.required' => 'IPK/Nilai rata-rata pendidikan pertama wajib diisi',
                'edu1_graduate_year.required' => 'Tahun lulus pendidikan pertama wajib diisi',
                'domicile_addres.required_if' => 'Alamat domisili wajib diisi jika berbeda dengan KTP',
                'domicile_rt.required_if' => 'RT domisili wajib diisi jika berbeda dengan KTP',
                'domicile_rw.required_if' => 'RW domisili wajib diisi jika berbeda dengan KTP',
                'domicile_village.required_if' => 'Kelurahan domisili wajib diisi jika berbeda dengan KTP',
                'domicile_subdistrict.required_if' => 'Kecamatan domisili wajib diisi jika berbeda dengan KTP',
                'domicile_city.required_if' => 'Kota domisili wajib diisi jika berbeda dengan KTP',
                'domicile_province.required_if' => 'Provinsi domisili wajib diisi jika berbeda dengan KTP',
                'domicile_poscode.required_if' => 'Kode pos domisili wajib diisi jika berbeda dengan KTP',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Mohon lengkapi data yang wajib diisi');
            }

            $user = Auth::user();
            $data = $validator->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika bukan default
                if ($user->photo && !str_contains($user->photo, 'default.jpg')) {
                    $oldPhoto = str_replace(asset('storage/images/profile/'), '', $user->photo);
                    Storage::disk('public')->delete('images/profile/' . $oldPhoto);
                }
            
                // Kompres dan simpan foto profil dengan nama unik
                $photoName = time() . '-' . $user->id . '-' . uniqid() . '.jpg';
                
                // Buat instance ImageManager dengan driver GD
                $manager = new ImageManager(new Driver());
                
                // Baca dan kompres gambar
                $image = $manager->read($request->photo->getRealPath());
                
                // Resize dengan ukuran yang lebih besar untuk foto profil
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200); 
                }
                
                // Simpan dengan kualitas tinggi (90%)
                Storage::disk('public')->put('images/profile/' . $photoName, $image->toJpeg(90));
                
                // Update foto user
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

            // Update user data only if there's data to update
            if (!empty($data)) {
                $user->update($data);
            }

            // Debug: Log bahwa data berhasil diupdate
            Log::info('Profile updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($data)
            ]);

            // Hanya gunakan session flash message, hapus Alert::success untuk menghindari konflik
            return redirect()->back()->with('success', 'Profile berhasil diperbarui');
            
        } catch (\Exception $e) {
            // Debug: Log error
            Log::error('Profile update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto profil secara terpisah
     */
    public function uploadPhoto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240|dimensions:min_width=100,min_height=100',
            ], [
                'photo.required' => 'Silakan pilih foto profil',
                'photo.max' => 'Foto profil maksimal 10MB',
                'photo.dimensions' => 'Foto minimal 100x100 pixel',
                'photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Hapus foto lama
                if ($user->photo && $user->photo !== 'default.jpg') {
                    Storage::disk('public')->delete('images/profile/' . $user->photo);
                }
            
                // Kompres dan simpan foto profil dengan nama unik
                $photoName = time() . '-' . $user->id . '-' . uniqid() . '.jpg';
                
                // Buat instance ImageManager dengan driver GD
                $manager = new ImageManager(new Driver());
                
                // Baca dan kompres gambar
                $image = $manager->read($request->photo->getRealPath());
                
                // Resize dengan ukuran yang lebih besar untuk foto profil
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200); 
                }
                
                // Simpan dengan kualitas tinggi (90%)
                Storage::disk('public')->put('images/profile/' . $photoName, $image->toJpeg(90));
                
                // Update foto user
                User::where('id', $user->id)->update(['photo' => $photoName]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui',
                    'photo_url' => asset('storage/images/profile/' . $photoName)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardChartData()
    {
        try {
            // Get student distribution by program studi for Ma'had Aly
            $programStudis = \App\Models\Akademik\ProgramStudi::withCount('mahasiswas')->get();

            $labels = [];
            $values = [];

            foreach ($programStudis as $prodi) {
                $labels[] = $prodi->name;
                $values[] = $prodi->mahasiswas_count;
            }

            // If no data, provide default Islamic program labels for Ma'had Aly
            if (empty($labels)) {
                $labels = ['Syariah', 'Dakwah', 'Tarbiyah', 'Ushuluddin', 'Adab'];
                $values = [25, 30, 20, 15, 10];
            }

            return response()->json([
                'labels' => $labels,
                'values' => $values
            ]);

        } catch (\Exception $e) {
            // Return default Islamic program data if error occurs
            return response()->json([
                'labels' => ['Syariah', 'Dakwah', 'Tarbiyah', 'Ushuluddin', 'Adab'],
                'values' => [25, 30, 20, 15, 10]
            ]);
        }
    }
}
