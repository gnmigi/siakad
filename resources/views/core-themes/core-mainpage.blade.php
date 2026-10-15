<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @PwaHead
    
    @php
        // Set up SEO data
        $seoTitle = isset($seo_title) ? $seo_title : (isset($menus) ? $menus : (isset($pages) ? $pages : 'Beranda'));
        $seoDescription = isset($seo_description) ? $seo_description : null;
        $seoKeywords = isset($seo_keywords) ? $seo_keywords : null;
        $seoImage = isset($seo_image) ? $seo_image : null;
        
        // Configure SEO service
        if (isset($seo)) {
            $seo->setTitle($seoTitle);
            if ($seoDescription) $seo->setDescription($seoDescription);
            if ($seoKeywords) $seo->setKeywords($seoKeywords);
            if ($seoImage) $seo->setImage($seoImage);
        }
    @endphp
    
    <title>{{ isset($seo) ? $seo->getTitle() : ((isset($menus) ? $menus . ' - ' : '') . (isset($pages) ? $pages : 'Beranda') . ' - ' . (isset($academy) ? $academy : config('app.name'))) }}</title>
    
    @if(isset($seo))
        {!! $seo->generateMetaTags() !!}
        {!! $seo->generateStructuredData() !!}
    @else
        <meta name="description" content="Ma'had Aly Ma'al Amin - Perguruan Tinggi Islam yang menyelenggarakan pendidikan berkualitas dengan nilai-nilai keislaman.">
        <meta name="keywords" content="Ma'had Aly Ma'al Amin, perguruan tinggi islam, pendidikan islam, kuliah islam, universitas islam">
        <meta name="author" content="{{ config('app.name') }}">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="googlebot" content="index, follow">
        
        <meta property="og:title" content="{{ (isset($menus) ? $menus . ' - ' : '') . (isset($pages) ? $pages : 'Beranda') . ' - ' . config('app.name') }}">
        <meta property="og:description" content="Ma'had Aly Ma'al Amin - Perguruan Tinggi Islam yang menyelenggarakan pendidikan berkualitas dengan nilai-nilai keislaman.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:locale" content="id_ID">
        
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ (isset($menus) ? $menus . ' - ' : '') . (isset($pages) ? $pages : 'Beranda') . ' - ' . config('app.name') }}">
        <meta name="twitter:description" content="Ma'had Aly Ma'al Amin - Perguruan Tinggi Islam yang menyelenggarakan pendidikan berkualitas dengan nilai-nilai keislaman.">
        
        <meta name="theme-color" content="#0d9488">
        <meta name="msapplication-TileColor" content="#0d9488">
        
        <link rel="canonical" href="{{ url()->current() }}">
        
        <!-- Default Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Ma'had Aly Ma'al Amin",
            "url": "{{ config('app.url', 'https://maalamin.ac.id') }}",
            "description": "Ma'had Aly Ma'al Amin - Perguruan Tinggi Islam yang menyelenggarakan pendidikan berkualitas dengan nilai-nilai keislaman.",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "ID",
                "addressLocality": "Indonesia"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer service",
                "url": "{{ config('app.url', 'https://maalamin.ac.id') }}/kontak"
            },
            "sameAs": [
                "https://www.facebook.com/maalamin",
                "https://www.instagram.com/maalamin",
                "https://www.youtube.com/maalamin"
            ]
        }
        </script>
    @endif
    <!-- Dynamic Favicon from Web Settings -->
    @if(isset($webs) && $webs->school_logo)
        <link rel="icon" type="image/png" href="{{ $webs->school_logo }}">
        <link rel="shortcut icon" type="image/png" href="{{ $webs->school_logo }}">
        <link rel="apple-touch-icon" href="{{ $webs->school_logo }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @endif
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('dashboard') }}/dist/css/tabler.css" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    @yield('custom-css')
    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{ asset('dashboard') }}/dist/css/tabler-themes.css" rel="stylesheet" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    <link href="{{ asset('dashboard') }}/preview/css/demo.css" rel="stylesheet" />
    <style>
        /* Enhanced Teal Theme Integration */
        :root {
            /* Teal color palette */
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
            --teal-200: #99f6e4;
            --teal-300: #5eead4;
            --teal-400: #2dd4bf;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-700: #0f766e;
            --teal-800: #115e59;
            --teal-900: #134e4a;
            --teal-950: #042f2e;
        }

        /* Override default Tabler primary colors with teal */
        :root {
            --tblr-primary: #0d9488;
            --tblr-primary-rgb: 13, 148, 136;
            --tblr-primary-fg: #ffffff;
        }

        [data-bs-theme="dark"] {
            --tblr-primary: #2dd4bf;
            --tblr-primary-rgb: 45, 212, 191;
            --tblr-primary-fg: #042f2e;
        }

        /* Enhanced Chat Button Styles with Teal Theme */
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--tblr-primary), #14b8a6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(var(--tblr-primary-rgb), 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-button svg {
            color: #fff;
            width: 28px;
            height: 28px;
        }

        .chat-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(var(--tblr-primary-rgb), 0.4);
        }

        [data-bs-theme="dark"] .chat-button:hover {
            box-shadow: 0 6px 16px rgba(var(--tblr-primary-rgb), 0.5);
        }

        /* Enhanced Chat Popup Styles */
        .chat-popup {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            background: var(--tblr-bg-surface);
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border: 1px solid var(--tblr-border-color);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .chat-popup {
            background: var(--tblr-bg-surface);
            border-color: var(--tblr-border-color);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
        }

        .chat-popup.show {
            display: block;
            animation: slideUp 0.3s ease forwards;
        }

        .chat-popup-header {
            padding: 20px;
            border-bottom: 1px solid var(--tblr-border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, rgba(var(--tblr-primary-rgb), 0.1), rgba(var(--tblr-primary-rgb), 0.05));
        }

        .chat-popup-body {
            padding: 20px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced Navbar Styles */
        .navbar {
            width: 100vw !important;
            margin-left: calc((100vw - 100%) / -2);
            margin-right: calc((100vw - 100%) / -2);
            left: 0;
            right: 0;
            border-radius: 0 !important;
            transition: all 0.3s ease;
        }

        /* Enhanced Theme Toggle Buttons */
        .nav-link[title*="dark"], .nav-link[title*="light"] {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .nav-link[title*="dark"]:hover, .nav-link[title*="light"]:hover {
            background: rgba(var(--tblr-primary-rgb), 0.1);
            transform: scale(1.1);
        }

        /* Enhanced Dropdown Styles */
        .dropdown-menu {
            border: 1px solid var(--tblr-border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced Footer Styles */
        .footer {
            transition: all 0.3s ease;
        }

        /* Smooth transitions for all elements */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Enhanced Settings Panel */
        .settings .btn-floating {
            background: linear-gradient(135deg, var(--tblr-primary), #14b8a6);
            box-shadow: 0 4px 12px rgba(var(--tblr-primary-rgb), 0.3);
        }

        .settings .btn-floating:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(var(--tblr-primary-rgb), 0.4);
        }
    </style>

    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
</head>

<body>
    <!-- BEGIN GLOBAL THEME SCRIPT -->
    <script src="{{ asset('dashboard') }}/dist/js/tabler-theme.min.js"></script>
    <!-- END GLOBAL THEME SCRIPT -->
    <div class="page">
        <!-- BEGIN NAVBAR  -->
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                <!-- BEGIN NAVBAR TOGGLER -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- END NAVBAR TOGGLER -->
                <!-- BEGIN NAVBAR LOGO -->
                <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route('root.home-index') }}" aria-label="Tabler">
                        <img src="{{ $webs->school_logo_hori }}" style="max-height: 32px; max-width: 200px; height: auto; width: auto; object-fit: contain;" alt="Maal Amin">
                    </a>
                </div>
                <!-- END NAVBAR LOGO -->
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex">
                        <div class="nav-item">
                            <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                                </svg>
                            </a>
                            <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                                </svg>
                            </a>
                        </div>
                        <div class="nav-item dropdown d-none d-md-flex">
                            <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/bell -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                </svg>
                                <span class="badge bg-red"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                                <div class="card">
                                    <div class="card-header d-flex">
                                        <h3 class="card-title">Notifications</h3>
                                        <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                                    </div>
                                    <div class="list-group list-group-flush list-group-hoverable">
                                        @php
                                            // Get latest pengumuman and berita for notifications
                                            $notifications = collect();
                                            
                                            try {
                                                $pengumuman = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')
                                                    ->with('kategori')
                                                    ->latest()
                                                    ->take(3)
                                                    ->get();
                                                
                                                foreach($pengumuman as $item) {
                                                    $notifications->push([
                                                        'type' => 'pengumuman',
                                                        'title' => 'Pengumuman: ' . $item->name,
                                                        'description' => strip_tags($item->content),
                                                        'url' => '#',
                                                        'date' => $item->created_at,
                                                        'status' => 'red'
                                                    ]);
                                                }
                                                
                                                $berita = \App\Models\Publikasi\Berita::where('status', 'Publish')
                                                    ->with('kategori')
                                                    ->latest()
                                                    ->take(2)
                                                    ->get();
                                                
                                                foreach($berita as $item) {
                                                    $notifications->push([
                                                        'type' => 'berita',
                                                        'title' => 'Berita: ' . $item->name,
                                                        'description' => strip_tags($item->content),
                                                        'url' => '#',
                                                        'date' => $item->created_at,
                                                        'status' => 'green'
                                                    ]);
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
                                                        <span class="status-dot @if($notification['status'] == 'red') status-dot-animated bg-red @elseif($notification['status'] == 'green') status-dot-animated bg-green @else @endif d-block"></span>
                                                    </div>
                                                    <div class="col text-truncate">
                                                        <a href="{{ $notification['url'] }}" class="text-body d-block">{{ $notification['title'] }}</a>
                                                        <div class="d-block text-secondary text-truncate mt-n1">{{ Str::limit($notification['description'], 60) }}</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="{{ $notification['url'] }}" class="list-group-item-actions">
                                                            <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-muted icon-2">
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
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <a href="#" class="btn btn-2 w-100"> Archive all </a>
                                            </div>
                                            <div class="col">
                                                <a href="#" class="btn btn-2 w-100"> Mark all as read </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nav-item dropdown d-none d-md-flex me-3">
                            <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show app menu" data-bs-auto-close="outside" aria-expanded="false">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/apps -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                    <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M14 7l6 0" />
                                    <path d="M17 4l0 6" />
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Aplikasi Kampus</div>
                                        <div class="card-actions btn-actions">
                                            <a href="#" class="btn-action">
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/settings -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body scroll-y p-2" style="max-height: 50vh">
                                        <div class="row g-0">
                                            <div class="col-4">
                                                <a href="/siakad" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                                                        <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                                                    </svg>
                                                    <span class="h5">SIAKAD</span>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="#" onclick="showDevelopmentMessage(); return false;" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-laptop w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 19l18 0" />
                                                        <path d="M5 6m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                                    </svg>
                                                    <span class="h5">E-Learning</span>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="#" onclick="showDevelopmentMessage(); return false;" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-books w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M5 4m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
                                                        <path d="M9 4m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
                                                        <path d="M5 8h4" />
                                                        <path d="M9 16h4" />
                                                        <path d="M13.803 4.56l2.184 -.53c.562 -.135 1.133 .19 1.282 .732l3.695 13.418a1.02 1.02 0 0 1 -.634 1.219l-.133 .041l-2.184 .53c-.562 .135 -1.133 -.19 -1.282 -.732l-3.695 -13.418a1.02 1.02 0 0 1 .634 -1.219l.133 -.041z" />
                                                        <path d="M14 9l4 -1" />
                                                        <path d="M16 16l3.923 -.98" />
                                                    </svg>
                                                    <span class="h5">Perpustakaan</span>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="#" onclick="showDevelopmentMessage(); return false;" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-test-pipe w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M20 8.04l-12.122 12.124a2.857 2.857 0 1 1 -4.041 -4.04l12.122 -12.124" />
                                                        <path d="M7 13h8" />
                                                        <path d="M19 15l1.5 1.6a2 2 0 1 1 -3 0l1.5 -1.6z" />
                                                        <path d="M15 3l6 6" />
                                                    </svg>
                                                    <span class="h5">Laboratorium</span>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="#" onclick="showDevelopmentMessage(); return false;" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                    </svg>
                                                    <span class="h5">Kemahasiswaan</span>
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="#" onclick="showDevelopmentMessage(); return false;" class="d-flex flex-column flex-center text-center text-secondary py-2 px-2 link-hoverable">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet w-6 h-6 mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                                    </svg>
                                                    <span class="h5">Keuangan</span>
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(isset($user) && $user)

                    <div class="nav-item dropdown">

                        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">

                            <span class="avatar avatar-sm" style="background-image: url({{ $user->photo ?? asset('storage/images/profile/default.jpg') }})">

                            </span>

                            <div class="d-none d-xl-block ps-2">

                                <div>{{ $user->name }}</div>

                                <div class="mt-1 small text-secondary">{{ $user->type ?? 'User' }}</div>

                            </div>

                        </a>

                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                            @php
                                $prefix = $spref;
                                // Keep camaba prefix as is, don't change to mahasiswa
                            @endphp

                            <a href="{{ route($prefix.'profile-render') }}" class="dropdown-item">

                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">

                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />

                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />

                                </svg>
                                Profil
                            </a>
                            <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                    <path d="M9 12h12l-3 -3" />
                                    <path d="M18 15l3 -3" />
                                </svg>
                                Keluar
                            </a>

                            <form id="logout-form" action="{{ route('auth.handle-logout') }}" method="POST" style="display:none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="nav-item">
                        <a href="{{ route('auth.render-signin') }}" class="nav-link d-flex lh-1 p-0 px-2">
                            <span class="avatar avatar-sm" style="background-image: url({{ asset('storage/images/profile/default.jpg') }})">
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div>Login</div>
                                <div class="mt-1 small text-secondary">Masuk ke Akun</div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </header>
        <header class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar">
                    <div class="container-xl">
                        <div class="row flex-column flex-md-row flex-fill align-items-center">
                            <div class="col">
                                <!-- BEGIN NAVBAR MENU -->
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('root.home-index') }}">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Beranda</span>
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#navbar-akademik" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                                                    <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Akademik</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-menu-columns">
                                                <div class="dropdown-menu-column">
                                                    <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Program Studi</a>
                                                    <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Kalender Akademik</a>
                                                    <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Jadwal Kuliah</a>
                                                    <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Silabus</a>
                                                    <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">E-Learning</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#navbar-kemahasiswaan" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Kemahasiswaan</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Organisasi Mahasantri</a>
                                            <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Beasiswa</a>
                                            <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Prestasi Mahasantri</a>
                                            <a class="dropdown-item" href="#" onclick="showDevelopmentMessage(); return false;">Alumni</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#navbar-institusi" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M3 21h18" />
                                                    <path d="M19 21v-4" />
                                                    <path d="M19 17a2 2 0 0 0 2 -2v-2a2 2 0 1 0 -4 0v2a2 2 0 0 0 2 2z" />
                                                    <path d="M14 21v-14a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v14" />
                                                    <path d="M9 17v4" />
                                                    <path d="M8 13h2" />
                                                    <path d="M8 9h2" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Institusi</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('root.institution.infinite-scroll') }}#profile">Profil</a>
                                            <a class="dropdown-item" href="{{ route('root.institution.infinite-scroll') }}#vision-mission">Visi & Misi</a>
                                            <a class="dropdown-item" href="{{ route('root.institution.infinite-scroll') }}#structure">Struktur Organisasi</a>
                                            <a class="dropdown-item" href="{{ route('root.institution.infinite-scroll') }}#facilities">Fasilitas</a>
                                            <a class="dropdown-item" href="{{ route('root.institution.infinite-scroll') }}#gallery">Galeri</a>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/kontak">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                    <path d="M3 7l9 6l9 -6" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Kontak</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('pmb.registration.form') }}">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                    <path d="M9 9l1 0" />
                                                    <path d="M9 13l6 0" />
                                                    <path d="M9 17l6 0" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title">Pendaftaran</span>
                                        </a>
                                    </li>
                                </ul>
                                <!-- END NAVBAR MENU -->
                            </div>
                            <div class="col col-md-auto">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <!-- <a class="nav-link" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSettings"> -->
                                            <!-- <span class="badge badge-sm bg-red text-red-fg">New</span>
                                            <span class="nav-link-icon d-md-none d-lg-inline-block"> -->
                                                <!-- Download SVG icon from http://tabler.io/icons/icon/settings -->
                                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                </svg>
                                            </span>
                                            <span class="nav-link-title"> Settings </span> -->
                                        <!-- </a> -->
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- END NAVBAR  -->
        <div class="page-wrapper">
            <!-- BEGIN PAGE HEADER -->
            <!-- END PAGE HEADER -->
            <!-- BEGIN PAGE BODY -->
            <div class="page-body">
                <div class="container-xl my-auto">
                    @include('core-themes.components.alerts')
                    @yield('content')
                    @include('sweetalert::alert')
                </div>
            </div>

        </div>
    </div>
    <!-- Theme Builder - Commented Out -->
    <!--
    <div class="settings">
        <a href="#" class="btn btn-floating btn-icon btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSettings" aria-controls="offcanvasSettings" aria-label="Theme Builder">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                <path d="M3 21v-4a4 4 0 1 1 4 4h-4" />
                <path d="M21 3a16 16 0 0 0 -12.8 10.2" />
                <path d="M21 3a16 16 0 0 1 -10.2 12.8" />
                <path d="M10.6 9a9 9 0 0 1 4.4 4.4" />
            </svg>
        </a>
        <form class="offcanvas offcanvas-start offcanvas-narrow" tabindex="-1" id="offcanvasSettings">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title">Theme Builder</h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <div>
                    <div class="mb-4">
                        <label class="form-label">Color mode</label>
                        <p class="form-hint">Choose the color mode for your app.</p>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme" value="light" class="form-check-input" checked />
                                <div class="form-check-label">Light</div>
                            </div>
                        </label>
                        <label class="form-check">
                            <div class="form-selectgroup-item">
                                <input type="radio" name="theme" value="dark" class="form-check-input" />
                                <div class="form-check-label">Dark</div>
                            </div>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Color scheme</label>
                        <p class="form-hint">The perfect color mode for your app.</p>
                        <div class="row g-2">
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="teal" class="form-colorinput-input" checked />
                                    <span class="form-colorinput-color bg-teal"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="blue" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-blue"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="azure" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-azure"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="indigo" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-indigo"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="purple" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-purple"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="pink" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-pink"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="red" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-red"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="orange" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-orange"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="yellow" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-yellow"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="lime" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-lime"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="green" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-green"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="theme-primary" type="radio" value="cyan" class="form-colorinput-input" />
                                    <span class="form-colorinput-color bg-cyan"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Font family</label>
                        <p class="form-hint">Choose the font family that fits your app.</p>
                        <div>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-font" value="sans-serif" class="form-check-input" checked />
                                    <div class="form-check-label">Sans-serif</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-font" value="serif" class="form-check-input" />
                                    <div class="form-check-label">Serif</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-font" value="monospace" class="form-check-input" />
                                    <div class="form-check-label">Monospace</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-font" value="comic" class="form-check-input" />
                                    <div class="form-check-label">Comic</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Theme base</label>
                        <p class="form-hint">Choose the gray shade for your app.</p>
                        <div>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-base" value="slate" class="form-check-input" />
                                    <div class="form-check-label">Slate</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-base" value="gray" class="form-check-input" checked />
                                    <div class="form-check-label">Gray</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-base" value="zinc" class="form-check-input" />
                                    <div class="form-check-label">Zinc</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-base" value="neutral" class="form-check-input" />
                                    <div class="form-check-label">Neutral</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-base" value="stone" class="form-check-input" />
                                    <div class="form-check-label">Stone</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Corner Radius</label>
                        <p class="form-hint">Choose the border radius factor for your app.</p>
                        <div>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-radius" value="0" class="form-check-input" />
                                    <div class="form-check-label">0</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-radius" value="0.5" class="form-check-input" />
                                    <div class="form-check-label">0.5</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-radius" value="1" class="form-check-input" checked />
                                    <div class="form-check-label">1</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-radius" value="1.5" class="form-check-input" />
                                    <div class="form-check-label">1.5</div>
                                </div>
                            </label>
                            <label class="form-check">
                                <div class="form-selectgroup-item">
                                    <input type="radio" name="theme-radius" value="2" class="form-check-input" />
                                    <div class="form-check-label">2</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-auto space-y">
                    <button type="button" class="btn w-100" id="reset-changes">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                            <path d="M19.95 11a8 8 0 1 0 -.5 4m.5 5v-5h-5" />
                        </svg>
                        Reset changes
                    </button>
                    <a href="#" class="btn btn-primary w-100" data-bs-dismiss="offcanvas">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                            <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        </svg>
                        Save settings
                    </a>
                </div>
            </div>
        </form>
    </div>
    -->
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('dashboard') }}/dist/js/tabler.min.js?1747482943" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    <script src="./preview/js/demo.min.js?1747482943" defer></script>
    <!-- END DEMO SCRIPTS -->
    @yield('custom-js')
    <!-- BEGIN PAGE SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var themeConfig = {
                theme: "light",
                "theme-base": "gray",
                "theme-font": "sans-serif",
                "theme-primary": "teal",
                "theme-radius": "1",
            };
            var url = new URL(window.location);
            var form = document.getElementById("offcanvasSettings");
            var resetButton = document.getElementById("reset-changes");
            var checkItems = function() {
                for (var key in themeConfig) {
                    var value = window.localStorage["tabler-" + key] || themeConfig[key];
                    if (!!value) {
                        var radios = form.querySelectorAll(`[name="${key}"]`);
                        if (!!radios) {
                            radios.forEach((radio) => {
                                radio.checked = radio.value === value;
                            });
                        }
                    }
                }
            };
            form.addEventListener("change", function(event) {
                var target = event.target,
                    name = target.name,
                    value = target.value;
                for (var key in themeConfig) {
                    if (name === key) {
                        document.documentElement.setAttribute("data-bs-" + key, value);
                        window.localStorage.setItem("tabler-" + key, value);
                        url.searchParams.set(key, value);
                    }
                }
                window.history.pushState({}, "", url);
            });
            resetButton.addEventListener("click", function() {
                for (var key in themeConfig) {
                    var value = themeConfig[key];
                    document.documentElement.removeAttribute("data-bs-" + key);
                    window.localStorage.removeItem("tabler-" + key);
                    url.searchParams.delete(key);
                }
                checkItems();
                window.history.pushState({}, "", url);
            });
            checkItems();
            
            // Auto-apply teal theme on page load
            document.documentElement.setAttribute("data-bs-theme-primary", "teal");
            window.localStorage.setItem("tabler-theme-primary", "teal");
        });
    </script>
    <!-- END PAGE SCRIPTS -->

    <!-- BEGIN FLOATING CHAT BUTTON -->
    <div class="chat-button" onclick="toggleChatPopup()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M8 9h8"></path>
            <path d="M8 13h6"></path>
            <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path>
        </svg>
    </div>
    <div class="chat-popup" id="chatPopup">
        <div class="chat-popup-header">
            <h4 class="m-0">Hubungi Kami</h4>
            <button type="button" class="btn-close" onclick="toggleChatPopup()"></button>
        </div>
        <div class="chat-popup-body">
            <form id="whatsappForm" onsubmit="sendWhatsApp(event)">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. WhatsApp</label>
                    <input type="tel" class="form-control" id="whatsapp" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pesan</label>
                    <textarea class="form-control" id="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-whatsapp" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"></path>
                        <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"></path>
                    </svg>
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- BEGIN FOOTER -->
    <footer class="footer footer-transparent pt-5 mt-auto border-top">
        <div class="container pb-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="h4 mb-4">{{ $webs->school_name }}</h3>
                    <p class="text-muted">{{ $webs->school_desc }}</p>
                    <div class="social-links mt-4 d-flex gap-2">
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-facebook" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-instagram" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M16.5 7.5l0 .01" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-youtube" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M3 5m0 4a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v6a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4z" />
                                <path d="M10 9l5 3l-5 3z" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                                <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h4 class="h5 mb-4">Link Cepat</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Tentang Kami</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Program Studi</a></li>
                                        <li class="mb-2"><a href="{{ route('pmb.registration.form') }}" class="link-secondary">Pendaftaran</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Beasiswa</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Kalender Akademik</a></li>
                                    </ul>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h4 class="h5 mb-4">Mahasantri</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><a href="/siakad" class="link-secondary">SIAKAD</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">E-Learning</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Perpustakaan</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Laboratorium</a></li>
                                        <li class="mb-2"><a href="#" onclick="showDevelopmentMessage(); return false;" class="link-secondary">Kemahasiswaan</a></li>
                                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4 class="h5 mb-4">Kontak</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_address }}</span>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_phone }}</span>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                                <path d="M3 7l9 6l9 -6"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_email }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-top">
            <div class="container py-4">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-lg-start text-center">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">Copyright © {{ \Carbon\Carbon::now()->translatedFormat('F Y') }} <a href="." class="link-secondary">{{ $webs->school_apps }} - {{ $webs->school_name }} </a>. All rights reserved.</li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Kebijakan Privasi</a></li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end text-center">
                        <img src="{{ $webs->school_logo_hori }}" style="max-width: 200px; max-height: 128px" alt="Logo" class="h-8">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- END FOOTER -->

    @RegisterServiceWorkerScript

    <script>
        function toggleChatPopup() {
            const popup = document.getElementById('chatPopup');
            popup.classList.toggle('show');
        }

        function sendWhatsApp(event) {
            event.preventDefault();
            const name = document.getElementById('name').value;
            const whatsapp = document.getElementById('whatsapp').value;
            const message = document.getElementById('message').value;

            // Format pesan
            const formattedMessage = `Halo, saya ${name}\n\n${message}`;

            // Buat URL WhatsApp dengan nomor dan pesan
            const whatsappUrl = `https://wa.me/${whatsapp}?text=${encodeURIComponent(formattedMessage)}`;

            // Buka WhatsApp di tab baru
            window.open(whatsappUrl, '_blank');

            // Reset form
            event.target.reset();
            toggleChatPopup();
        }

        function showDevelopmentMessage() {
            // Check if SweetAlert is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Fitur Sedang Dalam Pengembangan',
                    text: 'Maaf, fitur ini sedang dalam tahap pengembangan dan akan segera tersedia.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488'
                });
            } else {
                // Fallback to native alert if SweetAlert is not available
                alert('Fitur sedang dalam pengembangan.\n\nMaaf, fitur ini sedang dalam tahap pengembangan dan akan segera tersedia.');
            }
        }
    </script>

</body>

</html>
