<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Pengaturan\WebSetting;
use App\Models\Publikasi\Pengumuman;
use App\Models\Publikasi\Berita;
use App\Models\Publikasi\ProgramStudi;
use App\Models\Publikasi\Galeri;
use App\Models\Institusi\InstitutionPage;

class RootController extends Controller
{
    public function renderWelcome()
    {
        return view('welcome');
    }

    public function renderHomePage()
    {
        // 1. Cek keberadaan file 'installed' di folder storage
        if (!file_exists(storage_path('installed'))) {
            try {
                // Pastikan tabel benar-benar ada dan tidak kosong
                if (Schema::hasTable('web_settings') && \App\Models\Pengaturan\WebSetting::count() > 0) {
                    // Jika data ada (sudah setup), buat file installed agar tidak perlu cek DB lagi kedepannya
                    file_put_contents(storage_path('installed'), 'Setup recovered on ' . now());
                } else {
                    return redirect()->route('root.welcome');
                }
            } catch (\Exception $e) {
                // Jika terjadi error (misal tabel belum di-migrate), lemparkan ke Setup
                return redirect()->route('root.welcome');
            }
        }

        // Lanjutkan normal karena setup dipastikan sudah selesai
        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
            // Instead of redirecting calon mahasiswa to dashboard, render main-content view for them
            if ($user->raw_type == 0) {
                // Just continue to render main-content view as normal
            }
        } elseif (Auth::guard('pendaftar')->check()) {
            // Pendaftar guard (Calon Mahasiswa)
            $user = Auth::guard('pendaftar')->user();
            $spref = 'camaba.';
        }

        $data['webs'] = WebSetting::first();
        $data['spref'] = $spref;
        $data['menus'] = null;
        $data['pages'] = "Beranda";
        $data['academy'] = $data['webs']->school_apps;

        // Load published announcements for homepage - limited to 2 items
        $pengumuman = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(2)
            ->get();

        $berita = Berita::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(2)
            ->get();

        // Pass pengumuman and berita separately to the view
        $data['pengumuman'] = $pengumuman;
        $data['berita'] = $berita;

        // Load homepage settings from database
        $data['hero_title'] = $data['webs']->hero_title ?? 'Universitas Masa Depan';
        $data['hero_subtitle'] = $data['webs']->hero_subtitle ?? 'Membentuk Pemimpin Digital untuk Era Transformasi Global';
        $data['hero_background'] = $data['webs']->hero_background_image ? asset('storage/images/homepage/' . $data['webs']->hero_background_image) : 'https://images.unsplash.com/photo-1576495199011-eb94736d05d6?q=80&w=2072&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';

        // Statistics data
        $data['stat_programs'] = $data['webs']->stat_programs_count ?? '50+';
        $data['stat_programs_label'] = $data['webs']->stat_programs_label ?? 'Program Studi';
        $data['stat_students'] = $data['webs']->stat_students_count ?? '15K+';
        $data['stat_students_label'] = $data['webs']->stat_students_label ?? 'Mahasantri Aktif';
        $data['stat_lecturers'] = $data['webs']->stat_lecturers_count ?? '500+';
        $data['stat_lecturers_label'] = $data['webs']->stat_lecturers_label ?? 'Dosen Berkualitas';
        $data['stat_partners'] = $data['webs']->stat_partners_count ?? '100+';
        $data['stat_partners_label'] = $data['webs']->stat_partners_label ?? 'Mitra Industri';

        // Programs section - Load from database with error handling - limited to 3 items for slider
        try {
            $programStudis = ProgramStudi::where('status', 'active')
                ->with('kategori')
                ->latest()
                ->take(3)
                ->get();

            // Convert to array format for compatibility with existing view
            $data['programs'] = $programStudis->map(function($program) {
                return [
                    'name' => $program->name,
                    'description' => $program->description,
                    'image' => $program->photo,
                    'code' => $program->code
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Fallback to empty array if table doesn't exist
            $data['programs'] = [];
        }

        // News section
        $data['news_main_title'] = $data['webs']->news_main_title;
        $data['news_main_content'] = $data['webs']->news_main_content;
        $data['news_main_image'] = $data['webs']->news_main_image ? asset('storage/images/homepage/' . $data['webs']->news_main_image) : null;
        $data['news_main_category'] = $data['webs']->news_main_category ?? 'Event';
        $data['news_main_date'] = $data['webs']->news_main_date;

        // Features section
        $data['features'] = $data['webs']->features_section ? json_decode($data['webs']->features_section, true) : null;

        // Gallery section - Load from database with error handling
        try {
            $galeriData = Galeri::where('status', 'Publish')
                ->with('kategori')
                ->latest()
                ->take(6)
                ->get();

            // Convert to array format for compatibility with existing view
            $data['gallery'] = $galeriData->map(function($galeri) {
                return [
                    'title' => $galeri->name,
                    'description' => strip_tags($galeri->content),
                    'image' => $galeri->photo,
                    'code' => $galeri->code
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Fallback to empty array if table doesn't exist
            $data['gallery'] = [];
        }

        // FAQ section
        $data['faq'] = $data['webs']->faq_section ? json_decode($data['webs']->faq_section, true) : null;

        // CTA section
        $data['cta_title'] = $data['webs']->cta_title ?? 'Siap Bergabung dengan Kami?';
        $data['cta_subtitle'] = $data['webs']->cta_subtitle ?? 'Daftar sekarang dan mulai perjalanan akademik Anda bersama kami.';
        $data['cta_button_text'] = $data['webs']->cta_button_text ?? 'Daftar Sekarang';
        $data['cta_button_link'] = $data['webs']->cta_button_link ?? '#';

        return view('central.main-content', $data, compact('user'));
    }

    public function viewBerita($slug)
    {
        $berita = Berita::where('slug', $slug)->where('status', 'Publish')->firstOrFail();
        $menus = null;
        $pages = 'Berita';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        } elseif (Auth::guard('pendaftar')->check()) {
            // Pendaftar guard (Calon Mahasiswa)
            $user = Auth::guard('pendaftar')->user();
            $spref = 'camaba.';
        }

        // Get other berita for sidebar (exclude current)
        $otherBerita = Berita::where('status', 'Publish')
            ->where('slug', '!=', $slug)
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        // Get latest pengumuman for sidebar
        $otherPengumuman = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        return view('public.berita-view', compact('berita', 'menus', 'pages', 'academy', 'webs', 'otherBerita', 'otherPengumuman', 'user', 'spref'));
    }

    public function viewPengumuman($slug)
    {
        $pengumuman = Pengumuman::where('slug', $slug)->where('status', 'Publish')->firstOrFail();
        $menus = null;
        $pages = 'Pengumuman';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        } elseif (Auth::guard('pendaftar')->check()) {
            // Pendaftar guard (Calon Mahasiswa)
            $user = Auth::guard('pendaftar')->user();
            $spref = 'camaba.';
        }

        // Get other pengumuman for sidebar (exclude current)
        $otherPengumuman = Pengumuman::where('status', 'Publish')
            ->where('slug', '!=', $slug)
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        // Get latest berita for sidebar
        $otherBerita = Berita::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        return view('public.pengumuman-view', compact('pengumuman', 'menus', 'pages', 'academy', 'webs', 'otherPengumuman', 'otherBerita', 'user', 'spref'));
    }

    public function viewProgramStudi($code)
    {
        $programStudi = ProgramStudi::where('code', $code)->where('status', 'active')->firstOrFail();
        $menus = null;
        $pages = 'Program Studi';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        // Get other program studi for sidebar (exclude current)
        $otherProgramStudis = ProgramStudi::where('status', 'active')
            ->where('code', '!=', $code)
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        // Get latest berita for sidebar
        $otherBerita = Berita::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        // Get latest pengumuman for sidebar
        $otherPengumuman = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(5)
            ->get();

        return view('public.program-studi-view', compact('programStudi', 'menus', 'pages', 'academy', 'webs', 'otherProgramStudis', 'otherBerita', 'otherPengumuman', 'user', 'spref'));
    }

    public function allPengumuman()
    {
        $pengumuman = Pengumuman::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->paginate(6);

        $menus = null;
        $pages = 'Semua Pengumuman';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.pengumuman-all', compact('pengumuman', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function allBerita()
    {
        $berita = Berita::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->paginate(6);

        $menus = null;
        $pages = 'Semua Berita';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            // Default guard (users table)
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            // Dosen guard
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            // Mahasantri guard
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.berita-all', compact('berita', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function institutionProfile()
    {
        $page = InstitutionPage::where('page_type', 'profile')->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Halaman profil tidak ditemukan');
        }

        $menus = null;
        $pages = 'Profil Institusi';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.institusi.profile', compact('page', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function institutionVisionMission()
    {
        $page = InstitutionPage::where('page_type', 'vision_mission')->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Halaman visi misi tidak ditemukan');
        }

        $menus = null;
        $pages = 'Visi Misi';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.institusi.vision-mission', compact('page', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function institutionStructure()
    {
        $page = InstitutionPage::where('page_type', 'structure')->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Halaman struktur tidak ditemukan');
        }

        $menus = null;
        $pages = 'Struktur Organisasi';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.institusi.structure', compact('page', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function institutionFacilities()
    {
        $page = InstitutionPage::where('page_type', 'facilities')->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Halaman fasilitas tidak ditemukan');
        }

        $menus = null;
        $pages = 'Fasilitas';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.institusi.facilities', compact('page', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    public function institutionGallery()
    {
        $galeri = Galeri::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->paginate(12);

        $menus = null;
        $pages = 'Galeri';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        return view('public.gallery.index', compact('galeri', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    /**
     * Infinite Scroll Institution Pages
     */
    public function institutionInfiniteScroll()
    {
        $menus = null;
        $pages = 'Profil Institusi';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        }

        // Get all institution pages data
        $profilePage = InstitutionPage::where('page_type', 'profile')->where('is_active', true)->first();
        $visionPage = InstitutionPage::where('page_type', 'vision_mission')->where('is_active', true)->first();
        $structurePage = InstitutionPage::where('page_type', 'structure')->where('is_active', true)->first();
        $facilitiesPage = InstitutionPage::where('page_type', 'facilities')->where('is_active', true)->first();

        // Get gallery data
        $galeri = Galeri::where('status', 'Publish')
            ->with('kategori')
            ->latest()
            ->take(12)
            ->get();

        return view('public.institusi.infinite-scroll', compact(
            'profilePage', 'visionPage', 'structurePage', 'facilitiesPage', 'galeri',
            'menus', 'pages', 'academy', 'webs', 'user', 'spref'
        ));
    }

    /**
     * AJAX method to get institution page content
     */
    public function getInstitutionPageContent($pageType)
    {
        try {
            $data = [];

            switch ($pageType) {
                case 'profile':
                    $page = InstitutionPage::where('page_type', 'profile')->where('is_active', true)->first();
                    if (!$page) {
                        return response()->json(['error' => 'Page not found'], 404);
                    }
                    $data = [
                        'type' => 'profile',
                        'title' => 'Profil Institusi',
                        'page' => $page,
                        'nextPage' => 'vision-mission'
                    ];
                    break;

                case 'vision-mission':
                    $page = InstitutionPage::where('page_type', 'vision_mission')->where('is_active', true)->first();
                    if (!$page) {
                        return response()->json(['error' => 'Page not found'], 404);
                    }
                    $data = [
                        'type' => 'vision-mission',
                        'title' => 'Visi & Misi',
                        'page' => $page,
                        'nextPage' => 'structure'
                    ];
                    break;

                case 'structure':
                    $page = InstitutionPage::where('page_type', 'structure')->where('is_active', true)->first();
                    if (!$page) {
                        return response()->json(['error' => 'Page not found'], 404);
                    }
                    $data = [
                        'type' => 'structure',
                        'title' => 'Struktur Organisasi',
                        'page' => $page,
                        'nextPage' => 'facilities'
                    ];
                    break;

                case 'facilities':
                    $page = InstitutionPage::where('page_type', 'facilities')->where('is_active', true)->first();
                    if (!$page) {
                        return response()->json(['error' => 'Page not found'], 404);
                    }
                    $data = [
                        'type' => 'facilities',
                        'title' => 'Fasilitas',
                        'page' => $page,
                        'nextPage' => 'gallery'
                    ];
                    break;

                case 'gallery':
                    $galeri = Galeri::where('status', 'Publish')
                        ->with('kategori')
                        ->latest()
                        ->take(12)
                        ->get();

                    $data = [
                        'type' => 'gallery',
                        'title' => 'Galeri',
                        'galeri' => $galeri,
                        'nextPage' => null
                    ];
                    break;

                default:
                    return response()->json(['error' => 'Invalid page type'], 400);
            }

            // Get web settings for the view
            $webs = WebSetting::first();

            // Render the content based on page type
            $html = view('public.institusi.partials.' . str_replace('-', '_', $pageType), compact('data', 'webs'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'title' => $data['title'],
                'nextPage' => $data['nextPage'] ?? null,
                'pageType' => $pageType
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load content: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AJAX method to load more gallery items for homepage
     */
    public function loadMoreHomepageGallery(Request $request)
    {
        try {
            $offset = $request->get('offset', 0);
            $limit = $request->get('limit', 6);

            $galeriData = Galeri::where('status', 'Publish')
                ->with('kategori')
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get();

            // Convert to array format for compatibility with existing view
            $gallery = $galeriData->map(function($galeri) {
                return [
                    'title' => $galeri->name,
                    'description' => strip_tags($galeri->content),
                    'image' => $galeri->photo,
                    'code' => $galeri->code
                ];
            })->toArray();

            // Check if there are more items
            $totalCount = Galeri::where('status', 'Publish')->count();
            $hasMore = ($offset + $limit) < $totalCount;

            return response()->json([
                'success' => true,
                'gallery' => $gallery,
                'hasMore' => $hasMore,
                'nextOffset' => $offset + $limit,
                'totalCount' => $totalCount
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load gallery: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AJAX method to load more gallery items for institution page
     */
    public function loadMoreInstitutionGallery(Request $request)
    {
        try {
            $offset = $request->get('offset', 0);
            $limit = $request->get('limit', 6);

            $galeri = Galeri::where('status', 'Publish')
                ->with('kategori')
                ->latest()
                ->skip($offset)
                ->take($limit)
                ->get();

            // Check if there are more items
            $totalCount = Galeri::where('status', 'Publish')->count();
            $hasMore = ($offset + $limit) < $totalCount;

            // Render the gallery items HTML
            $html = '';
            foreach ($galeri as $item) {
                $html .= view('public.institusi.partials.gallery-item', compact('item'))->render();
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'hasMore' => $hasMore,
                'nextOffset' => $offset + $limit,
                'totalCount' => $totalCount,
                'loadedCount' => $galeri->count()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load gallery: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View gallery detail page
     */
    public function viewGalleryDetail($code)
    {
        $galeri = Galeri::where('code', $code)->where('status', 'Publish')->firstOrFail();
        $menus = null;
        $pages = 'Detail Galeri';
        $academy = WebSetting::first()->school_apps . ' oleh ' . WebSetting::first()->school_name;
        $webs = WebSetting::first();

        // Check authentication from all guards
        $user = null;
        $spref = '';

        if (Auth::check()) {
            $user = Auth::user();
            $spref = $user->prefix ?? 'web-admin.';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $spref = $user->prefix ?? 'dosen.';
        } elseif (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $spref = $user->prefix ?? 'mahasiswa.';
        } elseif (Auth::guard('pendaftar')->check()) {
            $user = Auth::guard('pendaftar')->user();
            $spref = 'camaba.';
        }

        // Get other gallery items for sidebar (exclude current)
        $otherGaleri = Galeri::where('status', 'Publish')
            ->where('code', '!=', $code)
            ->with('kategori')
            ->latest()
            ->take(6)
            ->get();

        return view('public.gallery.detail', compact('galeri', 'otherGaleri', 'menus', 'pages', 'academy', 'webs', 'user', 'spref'));
    }

    /**
     * AJAX method to get gallery detail
     */
    public function getGalleryDetail($code)
    {
        try {
            $galeri = Galeri::where('code', $code)
                ->where('status', 'Publish')
                ->with('kategori')
                ->first();

            if (!$galeri) {
                return response()->json(['error' => 'Gallery not found'], 404);
            }

            return response()->json([
                'success' => true,
                'title' => $galeri->name,
                'description' => strip_tags($galeri->content),
                'image' => $galeri->photo ? asset('storage/images/galeri/' . $galeri->photo) : null,
                'category' => $galeri->kategori ? $galeri->kategori->name : null,
                'date' => $galeri->created_at->format('d M Y'),
                'code' => $galeri->code
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load gallery detail: ' . $e->getMessage()], 500);
        }
    }
}
