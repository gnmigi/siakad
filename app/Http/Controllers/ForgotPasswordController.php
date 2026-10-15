<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Dosen;
use App\Models\Mahasantri;
use App\Models\Pengaturan\WebSetting;
use App\Services\EmailVerificationService;
use RealRashid\SweetAlert\Facades\Alert;

class ForgotPasswordController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Show forgot password form for Dosen
     */
    public function showDosenForgotForm()
    {
        $data['web'] = WebSetting::first();
        $data['pages'] = 'Lupa Password Dosen';
        $data['academy'] = $data['web']->school_apps . ' oleh ' . $data['web']->school_name;
        
        return view('auth.dosen-forgot-password', $data);
    }

    /**
     * Show forgot password form for Mahasiswa
     */
    public function showMahasiswaForgotForm()
    {
        $data['web'] = WebSetting::first();
        $data['pages'] = 'Lupa Password Mahasiswa';
        $data['academy'] = $data['web']->school_apps . ' oleh ' . $data['web']->school_name;
        
        return view('auth.mahasiswa-forgot-password', $data);
    }

    /**
     * Handle forgot password request for Dosen
     */
    public function handleDosenForgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'cf-turnstile-response' => 'required|turnstile'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if dosen exists
            $dosen = Dosen::where('dsn_mail', $request->email)->first();
            
            if (!$dosen) {
                Alert::error('Error', 'Email tidak ditemukan dalam sistem dosen.');
                return redirect()->back()->withInput();
            }

            // Check if email verification is enabled
            if (!$this->emailVerificationService->isEnabled()) {
                Alert::error('Error', 'Sistem verifikasi email tidak aktif. Hubungi administrator.');
                return redirect()->back();
            }

            // Send verification code
            $result = $this->emailVerificationService->sendVerificationCode(
                $request->email, 
                'password_reset',
                [
                    'user_type' => 'dosen',
                    'user_id' => $dosen->id,
                    'name' => $dosen->dsn_name
                ]
            );

            if ($result['success']) {
                Alert::success('Berhasil!', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek email dan masukkan kode verifikasi.');
                return redirect()->route('dosen.password-reset.verify', ['email' => $request->email]);
            } else {
                Alert::error('Error', $result['message']);
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Dosen forgot password error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Handle forgot password request for Mahasiswa
     */
    public function handleMahasiswaForgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'cf-turnstile-response' => 'required|turnstile'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if mahasiswa exists
            $mahasiswa = Mahasantri::where('email', $request->email)->first();
            
            if (!$mahasiswa) {
                Alert::error('Error', 'Email tidak ditemukan dalam sistem mahasiswa.');
                return redirect()->back()->withInput();
            }

            // Check if email verification is enabled
            if (!$this->emailVerificationService->isEnabled()) {
                Alert::error('Error', 'Sistem verifikasi email tidak aktif. Hubungi administrator.');
                return redirect()->back();
            }

            // Send verification code
            $result = $this->emailVerificationService->sendVerificationCode(
                $request->email, 
                'password_reset',
                [
                    'user_type' => 'mahasiswa',
                    'user_id' => $mahasiswa->id,
                    'name' => $mahasiswa->name
                ]
            );

            if ($result['success']) {
                Alert::success('Berhasil!', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek email dan masukkan kode verifikasi.');
                return redirect()->route('mahasiswa.password-reset.verify', ['email' => $request->email]);
            } else {
                Alert::error('Error', $result['message']);
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Mahasiswa forgot password error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show verification form for Dosen
     */
    public function showDosenVerifyForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            Alert::error('Error', 'Email tidak valid.');
            return redirect()->route('dosen.auth-forgot-page');
        }

        // Get verification status
        $status = $this->emailVerificationService->getVerificationStatus($email, 'password_reset');
        
        if ($status['status'] === 'not_found') {
            Alert::error('Error', 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.');
            return redirect()->route('dosen.auth-forgot-page');
        }

        if ($status['status'] === 'expired') {
            Alert::error('Error', 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.');
            return redirect()->route('dosen.auth-forgot-page');
        }

        $data['web'] = WebSetting::first();
        $data['pages'] = 'Verifikasi Reset Password Dosen';
        $data['academy'] = $data['web']->school_apps . ' oleh ' . $data['web']->school_name;
        $data['email'] = $email;
        $data['status'] = $status;
        
        return view('auth.dosen-verify-reset', $data);
    }

    /**
     * Show verification form for Mahasiswa
     */
    public function showMahasiswaVerifyForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            Alert::error('Error', 'Email tidak valid.');
            return redirect()->route('mahasiswa.auth-forgot-page');
        }

        // Get verification status
        $status = $this->emailVerificationService->getVerificationStatus($email, 'password_reset');
        
        if ($status['status'] === 'not_found') {
            Alert::error('Error', 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.');
            return redirect()->route('mahasiswa.auth-forgot-page');
        }

        if ($status['status'] === 'expired') {
            Alert::error('Error', 'Kode verifikasi sudah kadaluarsa. Silakan minta kode baru.');
            return redirect()->route('mahasiswa.auth-forgot-page');
        }

        $data['web'] = WebSetting::first();
        $data['pages'] = 'Verifikasi Reset Password Mahasiswa';
        $data['academy'] = $data['web']->school_apps . ' oleh ' . $data['web']->school_name;
        $data['email'] = $email;
        $data['status'] = $status;
        
        return view('auth.mahasiswa-verify-reset', $data);
    }

    /**
     * Verify code and reset password for Dosen
     */
    public function verifyAndResetDosenPassword(Request $request)
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

            // Find dosen and update password
            $dosen = Dosen::where('dsn_mail', $request->email)->first();
            
            if (!$dosen) {
                Alert::error('Error', 'Dosen tidak ditemukan.');
                return redirect()->route('dosen.auth-forgot-page');
            }

            // Update password
            $dosen->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('Dosen password reset successful', [
                'dosen_id' => $dosen->id,
                'email' => $request->email
            ]);

            Alert::success('Berhasil!', 'Password berhasil direset. Silakan login dengan password baru Anda.');
            return redirect()->route('dosen.auth-signin-page');

        } catch (\Exception $e) {
            Log::error('Dosen password reset error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Verify code and reset password for Mahasiswa
     */
    public function verifyAndResetMahasiswaPassword(Request $request)
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

            // Find mahasiswa and update password
            $mahasiswa = Mahasantri::where('email', $request->email)->first();
            
            if (!$mahasiswa) {
                Alert::error('Error', 'Mahasiswa tidak ditemukan.');
                return redirect()->route('mahasiswa.auth-forgot-page');
            }

            // Update password
            $mahasiswa->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('Mahasiswa password reset successful', [
                'mahasiswa_id' => $mahasiswa->id,
                'email' => $request->email
            ]);

            Alert::success('Berhasil!', 'Password berhasil direset. Silakan login dengan password baru Anda.');
            return redirect()->route('mahasiswa.auth-signin-page');

        } catch (\Exception $e) {
            Log::error('Mahasiswa password reset error: ' . $e->getMessage());
            Alert::error('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Resend verification code for Dosen
     */
    public function resendDosenCode(Request $request)
    {
        $email = $request->input('email');
        
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email tidak valid']);
        }

        try {
            $result = $this->emailVerificationService->resendCode($email, 'password_reset');
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Dosen resend code error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }

    /**
     * Resend verification code for Mahasiswa
     */
    public function resendMahasiswaCode(Request $request)
    {
        $email = $request->input('email');
        
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email tidak valid']);
        }

        try {
            $result = $this->emailVerificationService->resendCode($email, 'password_reset');
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Mahasiswa resend code error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
        }
    }
}
