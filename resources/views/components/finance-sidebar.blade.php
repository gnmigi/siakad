<!-- Finance Department Sidebar Menu -->
<ul class="navbar-nav pt-lg-3">
    <li class="nav-item">
        <a class="nav-link {{ Route::is((isset($spref) ? $spref : 'root.') . 'dashboard-render', Request::path()) ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'dashboard-render') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
            </span>
            <span class="nav-link-title">Dashboard Keuangan</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ Route::is((isset($spref) ? $spref : 'root.') . 'profile-render', Request::path()) ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'profile-render') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-edit">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                    <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" />
                </svg>
            </span>
            <span class="nav-link-title">Profile</span>
        </a>
    </li>

    <li class="nav-item">
        <span class="nav-link" href="">
            <span class="nav-link-title">Manajemen Keuangan</span>
        </span>
    </li>

    <li class="nav-item dropdown" data-dropdown-group="keuangan">
        <a class="nav-link {{ Route::is('keuangan.*', Request::path()) ? 'active' : '' }} dropdown-toggle" href="#navbar-form" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-dollar">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M6 4v4" />
                    <path d="M6 12v8" />
                    <path d="M13.366 14.54a2 2 0 1 0 -.216 3.097" />
                    <path d="M12 4v10" />
                    <path d="M12 18v2" />
                    <path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M18 4v1" />
                    <path d="M18 9v1" />
                    <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                    <path d="M19 21v1m0 -8v1" />
                </svg>
            </span>
            <span class="nav-link-title">Master Keuangan</span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.saldo-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.saldo-render') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wallet me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                    </svg>
                </span>
                Saldo
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.tagihan-kuliah-group-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.tagihan-kuliah-group-render') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                        <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                        <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                    </svg>
                </span>
                Tagihan Kuliah Group
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.tagihan-individual-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.tagihan-individual-index') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </span>
                Tagihan Individual
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.payment-methods-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.payment-methods-index') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                        <path d="M3 10l18 0" />
                        <path d="M7 15l.01 0" />
                        <path d="M11 15l2 0" />
                    </svg>
                </span>
                Metode Pembayaran
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.bank-accounts-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.bank-accounts-index') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-bank me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l18 0" />
                        <path d="M3 10l18 0" />
                        <path d="M5 6l7 -3l7 3" />
                        <path d="M4 10l0 11" />
                        <path d="M20 10l0 11" />
                        <path d="M8 14l0 3" />
                        <path d="M12 14l0 3" />
                        <path d="M16 14l0 3" />
                    </svg>
                </span>
                Rekening Bank
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.billing-templates.*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.billing-templates.index') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 9l1 0" />
                        <path d="M9 13l6 0" />
                        <path d="M9 17l6 0" />
                    </svg>
                </span>
                Billing Templates
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.pembayaran-manual-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.pembayaran-manual-index') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
                    </svg>
                </span>
                Pembayaran Manual
            </a>
            <a class="dropdown-item {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.pembayaran-manual-verification-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.pembayaran-manual-verification-queue') }}">
                <span class="nav-link-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shield-check me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 12l2 2l4 -4" />
                        <path d="M12 3l8 4v5c0 5 -3.5 9.5 -8 11c-4.5 -1.5 -8 -6 -8 -11v-5l8 -4" />
                    </svg>
                </span>
                Verifikasi Transfer
            </a>
        </div>
    </li>


    <li class="nav-item">
        <span class="nav-link" href="">
            <span class="nav-link-title">Laporan & Analisis</span>
        </span>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.laporan-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.laporan-index') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M3 3v18h18" />
                    <path d="M18.7 8l-5.1 5.2l-2.8 -2.7l-4.8 4.8" />
                    <path d="M17.4 8.4l2.3 -.4l-.4 2.3" />
                </svg>
            </span>
            <span class="nav-link-title">Laporan Keuangan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::is((isset($spref) ? $spref : 'root.') . 'keuangan.analisis-*') ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'keuangan.analisis-index') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                </svg>
            </span>
            <span class="nav-link-title">Analisis Pembayaran</span>
        </a>
    </li>

    <li class="nav-item">
        <span class="nav-link" href="">
            <span class="nav-link-title">Pengaturan</span>
        </span>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::is((isset($spref) ? $spref : 'root.') . 'pengaturan.web-settings-*', Request::path()) ? 'active' : '' }}" href="{{ route((isset($spref) ? $spref : 'root.') . 'pengaturan.web-settings-render') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
            </span>
            <span class="nav-link-title">Pengaturan WhatsApp</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link text-danger" href="{{ route('auth.handle-logout') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                    <path d="M9 12h12l-3 -3m0 6l3 -3" />
                </svg>
            </span>
            <span class="nav-link-title">Logout</span>
        </a>
    </li>
</ul>