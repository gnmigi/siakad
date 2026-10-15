<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MhsAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with mahasiswa guard
        if (!Auth::guard('mahasiswa')->check()) {
            return redirect()->route('auth.render-signin')->with('error', 'Silakan login terlebih dahulu');
        }

        $mahasiswa = Auth::guard('mahasiswa')->user();

        // Check if mahasiswa is valid (calon mahasiswa, aktif, or alumni)
        // Allow: type 0 (calon mahasiswa), type 1 (mahasiswa aktif), type 3 (alumni)
        // Block only: type 2 (mahasiswa non-aktif)
        if ($mahasiswa->raw_type == 2) {
            Auth::guard('mahasiswa')->logout();
            return redirect()->route('auth.render-signin')->with('error', 'Akun mahasiswa tidak aktif');
        }

        return $next($request);
    }
}
