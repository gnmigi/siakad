<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    /**
     * Get Dosen Profile
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'nidn' => $user->nidn ?? '-',
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                'address' => $user->address ?? 'N/A'
            ]
        ]);
    }

    /**
     * Get Dashboard Summary for Dosen
     */
    public function getDashboard(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_info' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                ],
                'stats' => [
                    'total_mata_kuliah' => 5, // Bisa dihubungkan ke relasi nanti
                    'total_mahasiswa' => 120,
                    'tugas_pending' => 72,
                    'pertemuan_hari_ini' => 0
                ]
            ]
        ]);
    }
}
