<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institusi\InstitutionPage;

class InstitutionStructureSeeder extends Seeder
{
    public function run()
    {
        // Data struktur organisasi
        InstitutionPage::create([
            'page_type' => 'structure',
            'title' => 'Struktur Organisasi',
            'content' => '<h2>Struktur Organisasi</h2>
            <p>Berikut adalah struktur organisasi kami:</p>
            <div class="text-center">
                <img src="/storage/images/structure/organizational-chart.jpg" alt="Struktur Organisasi" class="img-fluid">
            </div>
            <h3>Kepemimpinan</h3>
            <ul>
                <li><strong>Rektor:</strong> Dr. Ahmad Fauzi, M.Si</li>
                <li><strong>Wakil Rektor I:</strong> Dr. Siti Nurhaliza, M.Kom</li>
                <li><strong>Wakil Rektor II:</strong> Dr. Budi Santoso, M.T</li>
                <li><strong>Wakil Rektor III:</strong> Dr. Ratna Dewi, M.M</li>
            </ul>
            <h3>Struktur Organisasi</h3>
            <p>Universitas kami memiliki struktur organisasi yang terdiri dari:</p>
            <ol>
                <li>Sekretariat Universitas</li>
                <li>Fakultas-Fakultas</li>
                <li>Lembaga Penelitian dan Pengabdian Masyarakat</li>
                <li>Unit Pelaksana Teknis</li>
            </ol>',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
