<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Dosen;
use App\Models\Mahasantri;

class AuthController extends Controller
{
    /**
     * Handle API Login for Dosen and Mahasiswa (Smart Detection)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->login;
        $password = $request->password;

        // Determine field type for search
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // 1. Coba cari di Mahasiswa dulu (Biasanya user terbanyak)
        $user = Mahasantri::where('email', $login)
            ->orWhere('username', $login)
            ->orWhere('phone', $login)
            ->orWhere('numb_nim', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->raw_type == 2) {
                return response()->json(['status' => 'error', 'message' => 'Akun Mahasiswa tidak aktif'], 403);
            }
            $token = $user->createToken('mahasiswa_token')->plainTextToken;
            return $this->respondWithToken($token, $user, 'mahasiswa');
        }

        // 2. Jika tidak ada di Mahasiswa, coba cari di Dosen
        $user = Dosen::where('email', $login)
            ->orWhere('username', $login)
            ->orWhere('phone', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->raw_type != 1) {
                return response()->json(['status' => 'error', 'message' => 'Akun Dosen tidak aktif'], 403);
            }
            $token = $user->createToken('dosen_token')->plainTextToken;
            return $this->respondWithToken($token, $user, 'dosen');
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Kredensial yang Anda masukkan salah.'
        ], 401);
    }

    protected function respondWithToken($token, $user, $role)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role,
                    'photo' => $user->photo, // The accessor already handles the full URL
                ]
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
