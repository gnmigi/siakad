<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PMB\PeriodePendaftaran;
use App\Models\PMB\JalurPendaftaran;
use App\Models\PMB\Pendaftar;
use App\Models\Akademik\ProgramStudi;
use App\Models\Pengaturan\WebSetting;
use App\Services\EmailVerificationService;
use App\Models\EmailVerification;

class PMBController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    public function showRegistrationForm()
    {
        // Get active periods and jalur
        $periodes = PeriodePendaftaran::whereDate('start_date', '<=', now())
            ->whereDate('ended_date', '>=', now())
            ->get();
            
        $prodis = ProgramStudi::where('status', 'aktif')->get();
        $webs = WebSetting::first();
        
        // Add required variables for the template
        $pages = 'Pendaftaran Mahasiswa Baru';
        $academy = $webs->school_name ?? 'SIAKAD';
        
        return view('public.pmb.registration-form', compact('periodes', 'prodis', 'webs', 'pages', 'academy'));
    }
    
    public function submitRegistration(Request $request)
    {
        // Log the start of registration process
        Log::info('PMB Registration started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        $validator = Validator::make($request->all(), [
            'periode_id' => 'required|exists:periode_pendaftarans,id',
            'jalur_id' => 'required|exists:jalur_pendaftarans,id',
            'prodi_1' => 'required|exists:program_studis,id',
            'prodi_2' => 'nullable|exists:program_studis,id|different:prodi_1',
            'prodi_3' => 'nullable|exists:program_studis,id|different:prodi_1,prodi_2',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pendaftars,email',
            'phone' => 'required|string|unique:pendaftars,phone',
            'password' => 'required|string|min:6|confirmed',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string',
            'alamat' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'asal_sekolah' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'nik' => 'required|string|size:16|unique:pendaftars,nik',
        ]);

        if ($validator->fails()) {
            Log::warning('PMB Registration validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Log::info('PMB Registration validation passed');

        // Check if email verification is enabled
        if ($this->emailVerificationService->isEnabled()) {
            // Store registration data in session for later use
            $request->session()->put('pmb_registration_data', $request->except(['password_confirmation']));
            
            // Send verification code
            $result = $this->emailVerificationService->sendVerificationCode(
                $request->email,
                'registration',
                ['type' => 'pmb_registration']
            );

            if ($result['success']) {
                return redirect()->route('pmb.email.verification', ['email' => $request->email])
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->withInput();
            }
        }

        // If email verification is disabled, proceed with normal registration
        return $this->processRegistration($request);
    }

    /**
     * Show email verification page
     */
    public function showEmailVerification(Request $request)
    {
        $email = $request->get('email');
        
        if (!$email || !$request->session()->has('pmb_registration_data')) {
            return redirect()->route('pmb.registration.form')
                ->with('error', 'Session expired. Please fill the registration form again.');
        }

        // Check if email verification is enabled
        if (!$this->emailVerificationService->isEnabled()) {
            return redirect()->route('pmb.registration.form')
                ->with('error', 'Email verification is not enabled.');
        }

        $webs = WebSetting::first();
        $pages = 'Verifikasi Email';
        $academy = $webs->school_name ?? 'SIAKAD';
        
        // Get verification status
        $status = $this->emailVerificationService->getVerificationStatus($email, 'registration');
        
        return view('public.pmb.email-verification', compact('email', 'webs', 'pages', 'academy', 'status'));
    }

    /**
     * Verify email code
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if registration data exists in session
        if (!$request->session()->has('pmb_registration_data')) {
            return redirect()->route('pmb.registration.form')
                ->with('error', 'Session expired. Please fill the registration form again.');
        }

        // Verify the code
        $result = $this->emailVerificationService->verifyCode(
            $request->email,
            $request->verification_code,
            'registration'
        );

        if ($result['success']) {
            // Get registration data from session
            $registrationData = $request->session()->get('pmb_registration_data');
            
            // Process registration
            $registrationResult = $this->processRegistration(new Request($registrationData));
            
            // Clear session data
            $request->session()->forget('pmb_registration_data');
            
            return $registrationResult;
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Email is required']);
        }

        // Check if registration data exists in session
        if (!$request->session()->has('pmb_registration_data')) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }

        $result = $this->emailVerificationService->resendCode(
            $request->email,
            'registration',
            ['type' => 'pmb_registration']
        );

        return response()->json($result);
    }

    /**
     * Process the actual registration
     */
    private function processRegistration($request)
    {
        try {
            DB::beginTransaction();
            
            // Generate registration number with format: 2025-[random]-[sequence]
            $currentYear = date('Y');
            $sequence = str_pad(Pendaftar::count() + 1, 3, '0', STR_PAD_LEFT);
            $randomNumber = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            
            $numb_reg = $currentYear . '-' . $randomNumber . '-' . $sequence;
            $code = 'CMHS'.$currentYear.$randomNumber.$sequence;
            
            $pendaftar = Pendaftar::create([
                'periode_id' => $request->periode_id ?? $request['periode_id'],
                'jalur_id' => $request->jalur_id ?? $request['jalur_id'],
                'mahasiswa_id' => 0, // Temporary, will be updated when converted
                'jenis_id' => 1, // Default jenis kelas
                'gelombang_id' => 1, // Default gelombang
                'prodi_1' => $request->prodi_1 ?? $request['prodi_1'],
                'prodi_2' => $request->prodi_2 ?? $request['prodi_2'],
                'prodi_3' => $request->prodi_3 ?? $request['prodi_3'],
                'name' => $request->name ?? $request['name'],
                'email' => $request->email ?? $request['email'],
                'phone' => $request->phone ?? $request['phone'],
                'password' => Hash::make($request->password ?? $request['password']),
                'tempat_lahir' => $request->tempat_lahir ?? $request['tempat_lahir'],
                'tanggal_lahir' => $request->tanggal_lahir ?? $request['tanggal_lahir'],
                'jenis_kelamin' => $request->jenis_kelamin ?? $request['jenis_kelamin'],
                'agama' => $request->agama ?? $request['agama'],
                'alamat' => $request->alamat ?? $request['alamat'],
                'nama_ayah' => $request->nama_ayah ?? $request['nama_ayah'],
                'nama_ibu' => $request->nama_ibu ?? $request['nama_ibu'],
                'asal_sekolah' => $request->asal_sekolah ?? $request['asal_sekolah'],
                'tahun_lulus' => $request->tahun_lulus ?? $request['tahun_lulus'],
                'nik' => $request->nik ?? $request['nik'],
                'code' => $code,
                'numb_reg' => $numb_reg,
                'register_date' => now(),
                'status' => 'Pending',
                'status_validasi' => 'pending',
            ]);

            DB::commit();
            
            Log::info('PMB Registration successful', [
                'pendaftar_id' => $pendaftar->id,
                'code' => $pendaftar->code,
                'email' => $pendaftar->email
            ]);
            
            // Send registration success email
            try {
                $emailSent = app(\App\Services\NotificationService::class)->sendRegistrationSuccessEmail($pendaftar);
                
                if ($emailSent) {
                    Log::info('Registration success email sent', [
                        'pendaftar_id' => $pendaftar->id,
                        'email' => $pendaftar->email
                    ]);
                } else {
                    Log::warning('Failed to send registration success email', [
                        'pendaftar_id' => $pendaftar->id,
                        'email' => $pendaftar->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending registration success email', [
                    'pendaftar_id' => $pendaftar->id,
                    'email' => $pendaftar->email,
                    'error' => $e->getMessage()
                ]);
            }
            
            return redirect()->route('pmb.registration.success', $pendaftar->code)
                ->with('success', 'Pendaftaran berhasil! Email Anda telah diverifikasi. Email konfirmasi telah dikirim ke alamat email Anda.');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('PMB Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => is_array($request) ? $request : $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function registrationSuccess($code)
    {
        $pendaftar = Pendaftar::where('code', $code)->firstOrFail();
        $webs = WebSetting::first();
        
        // Add required variables for the template
        $pages = 'Pendaftaran Berhasil';
        $academy = $webs->school_name ?? 'SIAKAD';
        
        return view('public.pmb.registration-success', compact('pendaftar', 'webs', 'pages', 'academy'));
    }
    
    public function getJalursByPeriode($periode_id)
    {
        try {
            Log::info('Getting jalurs for periode', ['periode_id' => $periode_id]);
            
            $jalurs = JalurPendaftaran::where('periode_id', $periode_id)
                ->whereNull('deleted_at')
                ->select('id', 'name', 'code', 'desc')
                ->get();
            
            Log::info('Found jalurs', ['count' => $jalurs->count(), 'jalurs' => $jalurs->toArray()]);
            
            return response()->json($jalurs);
        } catch (\Exception $e) {
            Log::error('Error getting jalurs', [
                'periode_id' => $periode_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to load jalur data'], 500);
        }
    }
}
