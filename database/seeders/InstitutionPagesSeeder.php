<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institusi\InstitutionPage;

class InstitutionPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'page_type' => 'profile',
                'title' => 'Profil Institusi',
                'content' => 'Ma\'had Aly Maal Amin adalah institusi pendidikan tinggi Islam yang berkomitmen untuk menghasilkan lulusan yang berakhlak mulia, berpengetahuan luas, dan siap menghadapi tantangan zaman.',
                'is_active' => true,
            ],
            [
                'page_type' => 'vision_mission',
                'title' => 'Visi Misi',
                'content' => 'Visi dan Misi Ma\'had Aly Maal Amin dalam membangun generasi Islam yang unggul.',
                'additional_data' => json_encode([
                    'vision' => 'Menjadi institusi pendidikan tinggi Islam terdepan yang menghasilkan lulusan berakhlak mulia, berpengetahuan luas, dan berdaya saing global.',
                    'mission' => [
                        'Menyelenggarakan pendidikan tinggi Islam yang berkualitas dan relevan dengan kebutuhan zaman',
                        'Mengembangkan penelitian dan pengabdian masyarakat yang bermanfaat',
                        'Membentuk karakter mahasiswa yang berakhlak mulia dan berjiwa kepemimpinan',
                        'Membangun kerjasama strategis dengan berbagai pihak untuk kemajuan institusi'
                    ]
                ]),
                'is_active' => true,
            ],
            [
                'page_type' => 'structure',
                'title' => 'Struktur Organisasi',
                'content' => 'Struktur organisasi Ma\'had Aly Maal Amin dirancang untuk mendukung efektivitas pengelolaan institusi dan pencapaian visi misi.',
                'is_active' => true,
            ],
            [
                'page_type' => 'facilities',
                'title' => 'Fasilitas Institusi',
                'content' => 'Ma\'had Aly Maal Amin menyediakan berbagai fasilitas modern untuk mendukung kegiatan akademik dan non-akademik mahasiswa.',
                'additional_data' => json_encode([
                    'facilities' => [
                        [
                            'name' => 'Perpustakaan',
                            'description' => 'Perpustakaan modern dengan koleksi buku dan jurnal yang lengkap serta akses digital.'
                        ],
                        [
                            'name' => 'Laboratorium Komputer',
                            'description' => 'Laboratorium komputer dengan perangkat terkini untuk mendukung pembelajaran teknologi.'
                        ],
                        [
                            'name' => 'Masjid Kampus',
                            'description' => 'Masjid yang nyaman untuk kegiatan ibadah dan kajian keislaman.'
                        ],
                        [
                            'name' => 'Asrama Mahasiswa',
                            'description' => 'Asrama yang nyaman dan aman untuk mahasiswa dari luar daerah.'
                        ]
                    ]
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            InstitutionPage::updateOrCreate(
                ['page_type' => $page['page_type']],
                $page
            );
        }
    }
}
