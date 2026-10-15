<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $sitemaps = [
            [
                'loc' => url('sitemap.xml'),
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => url('sitemap-berita.xml'),
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => url('sitemap-pengumuman.xml'),
                'lastmod' => Carbon::now()->toISOString()
            ]
        ];

        $xml = view('sitemaps.index', compact('sitemaps'))->render();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate main sitemap with static pages
     */
    public function main()
    {
        $baseUrl = config('app.url', 'https://maalamin.ac.id');
        
        $urls = [
            // Homepage
            [
                'loc' => $baseUrl,
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            
            // Authentication Pages
            [
                'loc' => $baseUrl . '/signin',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            
            // Institution Pages
            [
                'loc' => $baseUrl . '/profil',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.9'
            ],
            [
                'loc' => $baseUrl . '/visi-misi',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => $baseUrl . '/struktur',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => $baseUrl . '/fasilitas',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => $baseUrl . '/gallery',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            
            // PMB (Admission) Pages
            [
                'loc' => $baseUrl . '/pmb/daftar',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.9'
            ],
            
            // News and Announcements
            [
                'loc' => $baseUrl . '/pengumuman',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ],
            [
                'loc' => $baseUrl . '/berita',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ],
            
            // Academic Information
            [
                'loc' => $baseUrl . '/program-studi',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.9'
            ],
            [
                'loc' => $baseUrl . '/kalender-akademik',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => $baseUrl . '/jadwal-kuliah',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            [
                'loc' => $baseUrl . '/silabus',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => $baseUrl . '/e-learning',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            
            // Student Services
            [
                'loc' => $baseUrl . '/organisasi',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => $baseUrl . '/beasiswa',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => $baseUrl . '/prestasi',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => $baseUrl . '/alumni',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            
            // Facilities
            [
                'loc' => $baseUrl . '/perpustakaan',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => $baseUrl . '/laboratorium',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => $baseUrl . '/kemahasiswaan',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            
            // Contact
            [
                'loc' => $baseUrl . '/kontak',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            
            // Portal
            [
                'loc' => $baseUrl . '/portal',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ]
        ];

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate berita sitemap
     */
    public function berita()
    {
        $baseUrl = config('app.url', 'https://maalamin.ac.id');
        $urls = [];

        try {
            $beritas = \App\Models\Publikasi\Berita::where('status', 'Publish')
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($beritas as $berita) {
                $urls[] = [
                    'loc' => $baseUrl . '/berita/' . $berita->slug,
                    'lastmod' => $berita->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7'
                ];
            }
        } catch (\Exception $e) {
            // Fallback if berita model doesn't exist or has issues
            Log::warning('Berita sitemap generation failed: ' . $e->getMessage());
        }

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate pengumuman sitemap
     */
    public function pengumuman()
    {
        $baseUrl = config('app.url', 'https://maalamin.ac.id');
        $urls = [];

        try {
            $pengumumans = \App\Models\Publikasi\Pengumuman::where('status', 'Publish')
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($pengumumans as $pengumuman) {
                $urls[] = [
                    'loc' => $baseUrl . '/pengumuman/' . $pengumuman->slug,
                    'lastmod' => $pengumuman->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7'
                ];
            }
        } catch (\Exception $e) {
            // Fallback if pengumuman model doesn't exist or has issues
            Log::warning('Pengumuman sitemap generation failed: ' . $e->getMessage());
        }

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
