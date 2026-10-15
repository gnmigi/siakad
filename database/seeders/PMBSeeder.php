<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PMB\PeriodePendaftaran;
use App\Models\PMB\JalurPendaftaran;
use Carbon\Carbon;

class PMBSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create sample periode pendaftaran
        $periode = PeriodePendaftaran::create([
            'taka_id' => 2, // Use existing tahun akademik ID
            'name' => 'Pendaftaran Mahasiswa Baru ' . date('Y'),
            'code' => 'PMB' . date('Y'),
            'desc' => 'Periode pendaftaran mahasiswa baru tahun ' . date('Y'),
            'start_date' => Carbon::now()->subDays(30),
            'ended_date' => Carbon::now()->addDays(60),
        ]);

        // Create sample jalur pendaftaran
        $jalurs = [
            [
                'name' => 'Jalur Reguler',
                'code' => 'REG',
                'desc' => 'Jalur pendaftaran reguler untuk calon mahasiswa',
            ],
            [
                'name' => 'Jalur Beasiswa',
                'code' => 'BEA',
                'desc' => 'Jalur pendaftaran khusus penerima beasiswa',
            ],
            [
                'name' => 'Jalur Prestasi',
                'code' => 'PRE',
                'desc' => 'Jalur pendaftaran untuk calon mahasiswa berprestasi',
            ],
        ];

        foreach ($jalurs as $jalur) {
            JalurPendaftaran::create(array_merge($jalur, [
                'periode_id' => $periode->id
            ]));
        }
    }
}
