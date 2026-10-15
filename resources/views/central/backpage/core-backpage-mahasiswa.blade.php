<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @PwaHead

  <title>{{ (isset($menus) ? $menus . ' - ' : '') . $pages . ' - ' . $academy }}</title>
  <!-- PAGE STYLES & GLOBAL MANDATORY STYLES -->
  <link href="./libs/jsvectormap/dist/jsvectormap.css" rel="stylesheet" />
  <link href="{{ asset('dashboard') }}/dist/css/tabler.css" rel="stylesheet" />
  <!-- PLUGINS STYLES -->
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  @yield('custom-css')
</head>

<body class="layout-fluid">
  <!-- THEME SCRIPT -->
  <script src="{{ asset('dashboard') }}/dist/js/tabler-theme.min.js"></script>
  <div class="page">

    <!-- SIDEBAR -->
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <!-- BEGIN NAVBAR TOGGLER -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->

        <!-- BEGIN NAVBAR LOGO -->
        <div class="navbar-brand navbar-brand-autodark">
          <a href="{{ route('root.home-index') }}" aria-label="Tabler">
            <img src="{{ $webs->school_logo_hori }}" style="max-height: 64px; max-width: 200px; height: auto; width: auto; object-fit: contain;" alt="Logo">
          </a>
        </div>
        <!-- END NAVBAR LOGO -->

        <!-- SIDEBAR MENU -->
        <div class="collapse navbar-collapse" id="sidebar-menu">
          <ul class="navbar-nav pt-lg-3">
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.dashboard-render')?'active':'' }}"
                 href="{{ route('mahasiswa.dashboard-render') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Home -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                  </svg>
                </span>
                <span class="nav-link-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.profile-render')?'active':'' }}"
                 href="{{ route('mahasiswa.profile-render') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Profile -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                    <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42-3.39z" />
                  </svg>
                </span>
                <span class="nav-link-title">Profile</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.krs.*')?'active':'' }}"
                 href="{{ route('mahasiswa.krs.index') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG KRS -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M9 12l2 2l4 -4" />
                  </svg>
                </span>
                <span class="nav-link-title">KRS</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.khs.*')?'active':'' }}"
                 href="{{ route('mahasiswa.khs.index') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG KHS -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 12h6m-6 4h6m2 5l-8 0c-1.1 0 -2 -.9 -2 -2v-14c0 -1.1 .9 -2 2 -2h8c1.1 0 2 .9 2 2v14c0 1.1 -.9 2 -2 2z" />
                    <path d="M14 3v4a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-4" />
                  </svg>
                </span>
                <span class="nav-link-title">KHS</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.transkrip.*')?'active':'' }}"
                 href="{{ route('mahasiswa.transkrip.index') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Transkrip -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 9l1 0" />
                    <path d="M9 13l6 0" />
                    <path d="M9 17l6 0" />
                  </svg>
                </span>
                <span class="nav-link-title">Transkrip Nilai</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.kelulusan.*')?'active':'' }}"
                 href="{{ route('mahasiswa.kelulusan.index') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Status Kelulusan -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                    <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                  </svg>
                </span>
                <span class="nav-link-title">Status Kelulusan</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.payment.*')?'active':'' }}"
                 href="{{ route('mahasiswa.payment.index') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Pembayaran -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                    <path d="M3 10l18 0" />
                    <path d="M7 15l.01 0" />
                    <path d="M11 15l2 0" />
                  </svg>
                </span>
                <span class="nav-link-title">Pembayaran</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Route::is('mahasiswa.payment.history')?'active':'' }}"
                 href="{{ route('mahasiswa.payment.history') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Riwayat Pembayaran -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 9 -9a9.75 9.75 0 0 0 -6.74 2.74l-2.26 2.26" />
                    <path d="M3 3v6h6" />
                    <path d="M12 7v5l4 2" />
                  </svg>
                </span>
                <span class="nav-link-title">Riwayat Pembayaran</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <!-- SVG Logout -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                       viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                    <path d="M9 12h12l-3 -3" />
                    <path d="M18 15l3 -3" />
                  </svg>
                </span>
                <span class="nav-link-title">Logout</span>
              </a>
              <form id="logout-form" action="{{ route('mahasiswa.handle-logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </div>
      </div>
    </aside>

    <!-- PAGE WRAPPER -->
    <div class="page-wrapper">

      <!-- PRIMARY NAVBAR -->
      <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
        <div class="container-xl">

          <!-- toggler left -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                  aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="navbar-nav flex-row w-100">

            <!-- tools group: pushed right -->
            <div class="d-none d-md-flex align-items-center ms-auto me-3">
              <!-- dark mode -->
              <a href="?theme=dark" class="nav-link px-0 hide-theme-dark me-3" title="Enable dark mode"
                 data-bs-toggle="tooltip" data-bs-placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                     stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                </svg>
              </a>
              <!-- light mode -->
              <a href="?theme=light" class="nav-link px-0 hide-theme-light me-3" title="Enable light mode"
                 data-bs-toggle="tooltip" data-bs-placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                     stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                  <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                </svg>
              </a>
              <!-- notifications -->
              <div class="nav-item dropdown me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                  </svg>
                  @php
                    $notificationCount = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')->count();
                  @endphp
                  @if($notificationCount > 0)
                    <span class="badge bg-red"></span>
                  @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Pengumuman Terbaru</h3>
                    </div>
                    <div class="list-group list-group-flush list-group-hoverable">
                      @php
                        // Get latest pengumuman and berita for notifications
                        $notifications = collect();
                        
                        try {
                            $pengumuman = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')
                                ->latest()
                                ->take(2)
                                ->get();
                            
                            foreach($pengumuman as $item) {
                                $notifications->push([
                                    'type' => 'pengumuman',
                                    'title' => 'Pengumuman: ' . $item->name,
                                    'description' => strip_tags($item->content ?? $item->description ?? ''),
                                    'url' => '#',
                                    'date' => $item->created_at,
                                    'status' => 'red'
                                ]);
                            }
                            
                            $berita = \App\Models\Publikasi\Berita::where('status', 'Publish')
                                ->latest()
                                ->take(2)
                                ->get();
                            
                            foreach($berita as $item) {
                                $notifications->push([
                                    'type' => 'berita',
                                    'title' => 'Info: ' . $item->name,
                                    'description' => strip_tags($item->content ?? $item->description ?? ''),
                                    'url' => '#',
                                    'date' => $item->created_at,
                                    'status' => 'green'
                                ]);
                            }
                            
                            // Add KRS notification for mahasiswa
                            if (isset($mahasiswa) && $mahasiswa) {
                                // Check if mahasiswa has filled KRS for current semester
                                $hasKRS = false;
                                if (\Illuminate\Support\Facades\Schema::hasTable('krs_mahasiswa')) {
                                    $hasKRS = \Illuminate\Support\Facades\DB::table('krs_mahasiswa')
                                        ->where('mahasiswa_id', $mahasiswa->id)
                                        ->exists();
                                }
                                
                                if (!$hasKRS) {
                                    $notifications->push([
                                        'type' => 'krs_belum_isi',
                                        'title' => 'Penting: KRS Belum Diisi',
                                        'description' => 'Anda belum mengisi Kartu Rencana Studi (KRS) untuk semester ini. Segera isi KRS Anda untuk dapat mengikuti perkuliahan.',
                                        'url' => route('mahasiswa.krs.index'),
                                        'date' => now(),
                                        'status' => 'yellow'
                                    ]);
                                }
                                
                                // Check if mahasiswa has pending grades (KRS filled but no grades yet)
                                if ($hasKRS && \Illuminate\Support\Facades\Schema::hasTable('krs_detail_mahasiswa')) {
                                    $pendingGrades = \Illuminate\Support\Facades\DB::table('krs_detail_mahasiswa as kd')
                                        ->join('krs_mahasiswa as km', 'kd.krs_mahasiswa_id', '=', 'km.id')
                                        ->leftJoin('nilai_mahasiswa as nm', function($join) {
                                            $join->on('km.mahasiswa_id', '=', 'nm.mahasiswa_id')
                                                 ->on('kd.mata_kuliah_id', '=', 'nm.mata_kuliah_id');
                                        })
                                        ->where('km.mahasiswa_id', $mahasiswa->id)
                                        ->where(function($query) {
                                            $query->whereNull('nm.nilai_akhir')
                                                  ->orWhereNull('nm.id');
                                        })
                                        ->count();
                                    
                                    if ($pendingGrades > 0) {
                                        $notifications->push([
                                            'type' => 'nilai_pending',
                                            'title' => 'Info: Nilai Belum Keluar',
                                            'description' => $pendingGrades . ' mata kuliah Anda belum memiliki nilai akhir. Nilai sedang dalam proses input oleh dosen.',
                                            'url' => route('mahasiswa.khs.index'),
                                            'date' => now(),
                                            'status' => 'blue'
                                        ]);
                                    }
                                }
                            }
                            
                            $notifications = $notifications->sortByDesc('date')->take(4);
                        } catch (\Exception $e) {
                            // Fallback to empty collection if tables don't exist
                            $notifications = collect();
                        }
                      @endphp
                      
                      @forelse($notifications as $notification)
                        <div class="list-group-item">
                          <div class="row align-items-center">
                            <div class="col-auto">
                              <span class="status-dot @if($notification['status'] == 'red') status-dot-animated bg-red @elseif($notification['status'] == 'green') status-dot-animated bg-green @elseif($notification['status'] == 'yellow') status-dot-animated bg-yellow @elseif($notification['status'] == 'blue') status-dot-animated bg-blue @else @endif d-block"></span>
                            </div>
                            <div class="col text-truncate">
                              <a href="{{ $notification['url'] }}" class="text-body d-block">{{ $notification['title'] }}</a>
                              <div class="d-block text-secondary text-truncate mt-n1">{{ Str::limit($notification['description'], 60) }}</div>
                            </div>
                            <div class="col-auto">
                              <a href="{{ $notification['url'] }}" class="list-group-item-actions">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted icon-2" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                </svg>
                              </a>
                            </div>
                          </div>
                        </div>
                      @empty
                        <div class="list-group-item">
                          <div class="row align-items-center">
                            <div class="col text-center">
                              <div class="text-muted">Tidak ada notifikasi terbaru</div>
                            </div>
                          </div>
                        </div>
                      @endforelse
                    </div><!-- /.list-group -->
                  </div>
                </div>
              </div>

            </div> <!-- /.tools group -->

            <!-- avatar -->
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar avatar-sm" style="background-image:url({{ $mahasiswa?->photo }})"></span>
                <div class="d-none d-xl-block ps-2">
                    <div>{{ $mahasiswa == null ? '' : $mahasiswa->name }}</div>
                    <div class="mt-1 small text-secondary">{{ $mahasiswa == null ? '' : $mahasiswa->nim }}</div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <a href="{{ route('mahasiswa.profile-render') }}" class="dropdown-item">Profile</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
              </div>
            </div>

          </div><!-- /.navbar-nav -->

        </div><!-- /.container-xl -->
      </header>
      <!-- END NAVBAR -->

      <!-- MAIN CONTENT -->
      <div class="page-body">
        <div class="container-xl">
          @include('core-themes.components.alerts')
          @yield('content')
        </div>
      </div>

      <!-- FOOTER -->
      <footer class="footer footer-transparent d-print-none">
        <div class="container">
          <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item"><a href="#" class="link-secondary">Support</a></li>
                <li class="list-inline-item"><a href="#" class="link-secondary">Docs</a></li>
              </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
              &copy; {{ date('Y') }} {{ $webs->school_name }}.
            </div>
          </div>
        </div>
      </footer>

    </div><!-- /.page-wrapper -->
  </div><!-- /.page -->

  <!-- SCRIPTS -->
  <script src="{{ asset('dashboard') }}/dist/js/tabler.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @yield('custom-js')

  @if(
    session('error')
    && session('alert-title') === 'Akses Fitur Akademik Ditolak'
    && (
      request()->routeIs('mahasiswa.dashboard-render')
      || request()->routeIs('mahasiswa.krs.*')
      || request()->routeIs('mahasiswa.khs.*')
      || request()->routeIs('mahasiswa.transkrip.*')
    )
  )
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        icon: 'info',
        title: 'Informasi',
        text: @json(session('error')),
        confirmButtonText: 'OK'
      });
    });
  </script>
  @endif
</body>

</html>
