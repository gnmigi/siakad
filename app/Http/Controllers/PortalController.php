<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function __invoke()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route(Auth::user()->prefix.'dashboard-render');
        }
        if (Auth::guard('dosen')->check()) {
            return redirect()->route('dosen.dashboard-render');
        }
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('mahasiswa.dashboard-render');
        }
        if (Auth::guard('pendaftar')->check()) {
            return redirect()->route('camaba.dashboard-render');
        }
        return redirect()->route('auth.render-signin');
    }
}

