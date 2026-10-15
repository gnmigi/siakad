<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Models
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasantri;
use App\Models\Pengaturan\WebSetting;
// Auth
use Illuminate\Support\Facades\Auth;
// Plugins
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }
    public function renderSignin()
    {
        $user = Auth::user() ?: Auth::guard('dosen')->user() ?: Auth::guard('mahasiswa')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Login";
        $data['pages'] = "Siakad";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        return view('central.auth.signin-content', $data, compact('user'));
    }

    public function handleSignin(Request $request)
    {
        // Get web settings to check if CAPTCHA is enabled
        $webSettings = WebSetting::first();
        
        // Base validation rules
        $validationRules = [
            'login' => 'required',
            'password' => 'required',
        ];
        
        // Add CAPTCHA validation if enabled
        if ($webSettings && $webSettings->enable_captcha) {
            $validationRules['cf-turnstile-response'] = 'required|turnstile';
        }
        
        $request->validate($validationRules);

        $login = $request->input('login');
        $password = $request->input('password');

        // Determine field type for login
        $fieldType = 'username'; // default
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email';
        } elseif (preg_match('/^[0-9+\-\s]+$/', $login)) {
            $fieldType = 'phone';
        }

        // Try authentication in order of priority: User (Staff/Admin) -> Dosen -> Mahasantri -> Pendaftar (Calon Mahasiswa)
        // Each attempt must validate both email AND password together
        
        // 1. Try Login as User/Staff/Admin first
        if (Auth::attempt(array($fieldType => $login, 'password' => $password))) {
            // Authentication successful for User table
            \App\Services\LogAktivitasService::logAuthEvent(Auth::user(), 'user', 'login', 'Berhasil login ke sistem');
            Alert::toast('Kamu telah berhasil login sebagai ' . Auth::user()->name, 'success');
            // Fix for calon mahasiswa prefix typo 'camaba' to 'mahasiswa'
            $prefix = Auth::user()->prefix;
            if ($prefix === 'camaba') {
                $prefix = 'mahasiswa';
            }
            // Redirect to dashboard instead of profile for better UX
            return redirect()->route($prefix . 'dashboard-render');
        }

        // 2. Try Login as Dosen
        if (Auth::guard('dosen')->attempt(array($fieldType => $login, 'password' => $password))) {
            // Authentication successful for Dosen table
            \App\Services\LogAktivitasService::logAuthEvent(Auth::guard('dosen')->user(), 'dosen', 'login', 'Berhasil login ke sistem');
            $rawType = Auth::guard('dosen')->user()->raw_type;
            if($rawType != 1){
                Auth::guard('dosen')->logout();
                Alert::error('Error', 'Mohon Maaf, akun dosen Anda tidak aktif.');
                return back();
            }
            Alert::toast('Kamu telah berhasil login sebagai ' . Auth::guard('dosen')->user()->name, 'success');
            return redirect()->route('dosen.dashboard-render');
        }

        // 3. Try Login as Mahasantri (check multiple field types including NIM)
        $mahasiswaAttempts = [
            ['email' => $login, 'password' => $password],
            ['username' => $login, 'password' => $password],
            ['phone' => $login, 'password' => $password],
            ['numb_nim' => $login, 'password' => $password] // Add NIM login support
        ];

        foreach ($mahasiswaAttempts as $credentials) {
            if (Auth::guard('mahasiswa')->attempt($credentials)) {
                // Authentication successful for Mahasantri table
                $mahasiswa = Auth::guard('mahasiswa')->user();
                \App\Services\LogAktivitasService::logAuthEvent($mahasiswa, 'mahasiswa', 'login', 'Berhasil login ke sistem');
                
                // Allow login for: type 0 (calon mahasiswa), type 1 (mahasiswa aktif), type 3 (alumni)
                // Block only type 2 (mahasiswa non-aktif)
                if($mahasiswa->raw_type == 2){
                    Auth::guard('mahasiswa')->logout();
                    Alert::error('Error', 'Mohon Maaf, akun mahasiswa Anda tidak aktif.');
                    return back();
                }
                
                // Check auto-terminal status on login (but don't block login)
                try {
                    $terminalService = app(\App\Services\TerminalService::class);
                    $terminalService->checkAutoTerminal($mahasiswa);
                    
                    // Refresh mahasiswa data to get updated terminal status
                    $mahasiswa->refresh();
                    
                    // If student is terminal, show warning but allow login (so they can pay)
                    if ($mahasiswa->isTerminal()) {
                        $terminalInfo = $mahasiswa->getTerminalInfo();
                        $reason = $terminalInfo['reason'] ?? 'tunggakan SPP';
                        
                        Alert::warning('Perhatian!', 
                            'Akun Anda dalam status terminal karena ' . $reason . '. ' .
                            'Anda masih dapat mengakses dashboard dan melakukan pembayaran, ' .
                            'namun fitur akademik (KRS, KHS, Transkrip) tidak tersedia sampai tunggakan diselesaikan.'
                        )->persistent();
                    }
                } catch (\Exception $e) {
                    // Log error but don't block login
                    Log::error('Auto-terminal check failed on login', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Determine redirect route based on user type
                $redirectRoute = 'mahasiswa.dashboard-render'; // default for active students
                
                if ($mahasiswa->raw_type == 3) {
                    // Alumni - redirect to alumni dashboard
                    $redirectRoute = 'alumni.dashboard-render';
                    Alert::toast('Selamat datang kembali, ' . $mahasiswa->name . '! Login sebagai Alumni berhasil.', 'success');
                } else {
                    Alert::toast('Kamu telah berhasil login sebagai ' . $mahasiswa->name, 'success');
                }
                
                return redirect()->route($redirectRoute);
            }
        }

        // 4. Try Login as Pendaftar (Calon Mahasiswa) - check multiple field types
        $pendaftarAttempts = [
            ['email' => $login, 'password' => $password],
            ['phone' => $login, 'password' => $password],
            ['numb_reg' => $login, 'password' => $password], // Allow login with registration number
            ['code' => $login, 'password' => $password] // Allow login with code
        ];

        foreach ($pendaftarAttempts as $credentials) {
            if (Auth::guard('pendaftar')->attempt($credentials)) {
                // Authentication successful for Pendaftar table
                $pendaftar = Auth::guard('pendaftar')->user();
                \App\Services\LogAktivitasService::logAuthEvent($pendaftar, 'pendaftar', 'login', 'Berhasil login ke sistem');
                
                // Check if pendaftar is active (you can add status validation here if needed)
                if($pendaftar->status === 'Batal'){
                    Auth::guard('pendaftar')->logout();
                    Alert::error('Error', 'Mohon Maaf, pendaftaran Anda telah dibatalkan.');
                    return back();
                }
                
                // Check if pendaftar has been converted to mahasiswa
                if($pendaftar->mahasiswa_id && $pendaftar->mahasiswa_id > 0) {
                    // Get mahasiswa data
                    $mahasiswa = \App\Models\Mahasantri::find($pendaftar->mahasiswa_id);
                    
                    if($mahasiswa) {
                        Auth::guard('pendaftar')->logout();
                        
                        // Show alert with conversion information - no auto close
                        Alert::html('Akun Sudah Dikonversi!', 
                            '<div class="text-start">
                                <p><strong>🎓 Selamat! Anda sudah menjadi mahasiswa aktif!</strong></p>
                                <p>Silakan login menggunakan akun mahasiswa Anda:</p>
                                <ul>
                                    <li><strong>Username/NIM:</strong> ' . $mahasiswa->numb_nim . '</li>
                                    <li><strong>Password:</strong> <span class="text-muted">(sama dengan password akun calon mahasiswa Anda)</span></li>
                                </ul>
                                <p class="mb-0"><small class="text-muted">Gunakan NIM di atas untuk login ke sistem akademik.</small></p>
                            </div>', 
                            'info'
                        )->persistent();
                        
                        return back()->withInput(['login' => $mahasiswa->numb_nim]);
                    }
                }
                
                Alert::toast('Selamat datang, ' . $pendaftar->name . '! Login sebagai Calon Mahasiswa berhasil.', 'success');
                return redirect()->route('camaba.dashboard-render');
            }
        }

        // If all authentication attempts failed
        Alert::error('Error', 'Mohon Maaf, Username/Email/Phone atau password salah.');
        return back();
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = "Lupa Password";
        $data['pages'] = "Reset Password";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        return view('central.auth.forgot-password', $data);
    }

    /**
     * Handle forgot password request (unified for dosen and mahasiswa)
     */
    public function handleForgotPassword(Request $request)
    {
        // Get web settings to check if CAPTCHA is enabled
        $webSettings = WebSetting::first();
        
        // Base validation rules
        $validationRules = [
            'email' => 'required|email',
        ];
        
        // Add CAPTCHA validation if enabled
        if ($webSettings && $webSettings->enable_captcha) {
            $validationRules['cf-turnstile-response'] = 'required|turnstile';
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $email = $request->email;
            $userFound = false;
            $userType = null;
            $userName = null;

            // Check if email exists in Dosen table
            $dosen = Dosen::where('email', $email)->first();
            if ($dosen) {
                $userFound = true;
                $userType = 'dosen';
                $userName = $dosen->name;
            }

            // If not found in Dosen, check Mahasiswa table
            if (!$userFound) {
                $mahasiswa = Mahasantri::where('email', $email)->first();
                if ($mahasiswa) {
                    $userFound = true;
                    $userType = 'mahasiswa';
                    $userName = $mahasiswa->name;
                }
            }

            // If not found in Mahasiswa, check Pendaftar (Calon Mahasiswa) table
            if (!$userFound) {
                $pendaftar = \App\Models\PMB\Pendaftar::where('email', $email)->first();
                if ($pendaftar) {
                    $userFound = true;
                    $userType = 'pendaftar';
                    $userName = $pendaftar->name;
                }
            }

            if (!$userFound) {
                Alert::error('Error', 'Email tidak ditemukan dalam sistem. Pastikan Anda menggunakan email yang terdaftar sebagai dosen, mahasiswa, atau calon mahasiswa.');
                return redirect()->back()->withInput();
            }

            // Check if email verification is enabled
            if (!$this->emailVerificationService->isEnabled()) {
                Alert::error('Error', 'Sistem verifikasi email tidak aktif. Hubungi administrator.');
                return redirect()->back();
            }

            // Send verification code
            $result = $this->emailVerificationService->sendVerificationCode(
                $email, 
                'password_reset',
                [
                    'user_type' => $userType,
                    'name' => $userName
                ]
            );

            if ($result['success']) {
                Alert::success('Berhasil!', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek email dan masukkan kode verifikasi.');
                return redirect()->route('auth.verify-reset-password', ['email' => $email]);
            } else {
                Alert::error('Error', $result['message']);
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show verification form for reset password
     */
    public function showVerifyResetForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            Alert::error('Error', 'Email tidak valid.');
            return redirect()->route('auth.forgot-password');
        }

        // Get verification status
        $status = $this->emailVerificationService->getVerificationStatus($email, 'password_reset');
        
        if ($status['status'] === 'not_found') {
            Alert::error('Error', 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.');
            return redirect()->route('auth.forgot-password');
        }

        if ($status['status'] === 'expired') {
            Alert::error('Error', 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.');
            return redirect()->route('auth.forgot-password');
        }

        $data['webs'] = WebSetting::first();
        $data['spref'] = '';
        $data['menus'] = "Verifikasi Reset Password";
        $data['pages'] = "Reset Password";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['email'] = $email;
        $data['status'] = $status;
        
        return view('central.auth.verify-reset-password', $data);
    }

    /**
     * Verify code and reset password (unified)
     */
    public function verifyAndResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Verify the code
            $verifyResult = $this->emailVerificationService->verifyCode(
                $request->email, 
                $request->verification_code, 
                'password_reset'
            );

            if (!$verifyResult['success']) {
                Alert::error('Error', $verifyResult['message']);
                return redirect()->back()->withInput();
            }

            $email = $request->email;
            $newPassword = Hash::make($request->password);
            $userUpdated = false;

            // Try to update Dosen password
            $dosen = Dosen::where('email', $email)->first();
            if ($dosen) {
                $dosen->update(['password' => $newPassword]);
                $userUpdated = true;
                
                Log::info('Dosen password reset successful', [
                    'dosen_id' => $dosen->id,
                    'email' => $email
                ]);
            }

            // If not dosen, try Mahasiswa
            if (!$userUpdated) {
                $mahasiswa = Mahasantri::where('email', $email)->first();
                if ($mahasiswa) {
                    $mahasiswa->update(['password' => $newPassword]);
                    $userUpdated = true;
                    
                    Log::info('Mahasiswa password reset successful', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'email' => $email
                    ]);
                }
            }

            // If not mahasiswa, try Pendaftar (Calon Mahasiswa)
            if (!$userUpdated) {
                $pendaftar = \App\Models\PMB\Pendaftar::where('email', $email)->first();
                if ($pendaftar) {
                    $pendaftar->update(['password' => $newPassword]);
                    $userUpdated = true;
                    
                    Log::info('Pendaftar password reset successful', [
                        'pendaftar_id' => $pendaftar->id,
                        'email' => $email
                    ]);
                }
            }

            if (!$userUpdated) {
                Alert::error('Error', 'User tidak ditemukan.');
                return redirect()->route('auth.forgot-password');
            }

            Alert::success('Berhasil!', 'Password berhasil direset. Silakan login dengan password baru Anda.');
            return redirect()->route('auth.render-signin');

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Resend verification code for password reset
     */
    public function resendResetCode(Request $request)
    {
        $email = $request->input('email');
        
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email tidak valid']);
        }

        try {
            $result = $this->emailVerificationService->resendCode($email, 'password_reset');
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Resend reset code error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function handleLogout(Request $request) {
        if (Auth::check()) {
            \App\Services\LogAktivitasService::logAuthEvent(Auth::user(), 'user', 'logout', 'Berhasil logout dari sistem');
            Auth::logout();
            Alert::success('Berhasil!', 'Logout telah sukses!');
            return redirect()->route('auth.render-signin');
        } elseif (Auth::guard('dosen')->check()) {
            \App\Services\LogAktivitasService::logAuthEvent(Auth::guard('dosen')->user(), 'dosen', 'logout', 'Berhasil logout dari sistem');
            Auth::guard('dosen')->logout();
            Alert::success('Berhasil!', 'Logout telah sukses!');
            return redirect()->route('auth.render-signin');

        } elseif (Auth::guard('mahasiswa')->check()) {
            \App\Services\LogAktivitasService::logAuthEvent(Auth::guard('mahasiswa')->user(), 'mahasiswa', 'logout', 'Berhasil logout dari sistem');
            Auth::guard('mahasiswa')->logout();
            Alert::success('Berhasil!', 'Logout telah sukses!');
            return redirect()->route('auth.render-signin');

        } elseif (Auth::guard('pendaftar')->check()) {
            \App\Services\LogAktivitasService::logAuthEvent(Auth::guard('pendaftar')->user(), 'pendaftar', 'logout', 'Berhasil logout dari sistem');
            Auth::guard('pendaftar')->logout();
            Alert::success('Berhasil!', 'Logout calon mahasiswa telah sukses!');
            return redirect()->route('auth.render-signin');

        } else {

            Alert::error('Gagal!', 'Logout gagal, Silahkan coba lagi!');
            return back();
        }
    }
}
