<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;
use Hash;
use Carbon\Carbon;
// DEFAULT AUTHENTIKASI
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasantri;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        User::create([
            'name' => 'Administrator',
            'code' => Str::random(6),
            'username' => 'admin',
            'email' => 'mjaya69703@gmail.com',
            'phone' => '080012345660',
            'password' => Hash::make('Admin123'),
        ]);

        // Create Staff Users
        $staffUsers = [
            [
                'name' => 'Staff Finance',
                'username' => 'finance',
                'type' => 1,
                'email' => 'finance@example.com',
                'phone' => '080012345661',
            ],
            [
                'name' => 'Staff Officer',
                'username' => 'officer',
                'type' => 2,
                'email' => 'officer@example.com',
                'phone' => '080012345662',
            ],
            [
                'name' => 'Staff Akademik',
                'username' => 'academic',
                'type' => 3,
                'email' => 'academic@example.com',
                'phone' => '080012345663',
            ],
            [
                'name' => 'Staff Admin',
                'username' => 'admin2',
                'type' => 4,
                'email' => 'admin@example.com',
                'phone' => '080012345664',
            ],
            [
                'name' => 'Staff Support',
                'username' => 'support',
                'type' => 5,
                'email' => 'support@example.com',
                'phone' => '080012345665',
            ],
        ];

        foreach ($staffUsers as $staff) {
            User::create([
                'name' => $staff['name'],
                'code' => Str::random(6),
                'username' => $staff['username'],
                'type' => $staff['type'],
                'email' => $staff['email'],
                'phone' => $staff['phone'],
                'password' => Hash::make('Admin123'),
            ]);
        }

        // Create Dosen Users
        $dosenUsers = [
            [
                'name' => 'Ustadz Dr. Dini Mahdany, M.Pd',
                'username' => 'dosen1',
                'email' => 'dosen1@gmail.com',
                'phone' => '080012345671',
            ],
            [
                'name' => 'Ustadz Muhammad Zainal Ilmi, M.Pd.',
                'username' => 'dosen2',
                'email' => 'dosen2@gmail.com',
                'phone' => '080012345672',
            ],
            [
                'name' => 'Ustadz Muhammad Zainal Ilmi, M.Pd.',
                'username' => 'dosen3',
                'email' => 'dosen3@gmail.com',
                'phone' => '080012345673',
            ],
            [
                'name' => 'Ustadz H. Ahmad Fauzi Jailani, Lc.',
                'username' => 'dosen4',
                'email' => 'dosen4@gmail.com',
                'phone' => '080012345674',
            ],
            [
                'name' => 'Ustadz Abdul Qadir Jailani',
                'username' => 'dosen5',
                'email' => 'dosen5@gmail.com',
                'phone' => '080012345675',
            ],
            [
                'name' => 'Ustadz Ahmad Ramadhan',
                'username' => 'dosen6',
                'email' => 'dosen6@gmail.com',
                'phone' => '080012345676',
            ],
        ];

        foreach ($dosenUsers as $dosen) {
            Dosen::create([
                'name' => $dosen['name'],
                'code' => Str::random(6),
                'username' => $dosen['username'],
                'type' => '1',
                'email' => $dosen['email'],
                'phone' => $dosen['phone'],
                'password' => Hash::make('dosen123'),
            ]);
        }

        // Create Mahasantri Users
        $mahasiswaUsers = [
            [
                'name' => 'Mahasantri A',
                'username' => 'mahasiswa.a',
                'email' => 'mahasiswa.a@example.com',
                'phone' => '080012345670',
                'type'  => 1,
                'numb_nim' => 24213241,
                'semester' => 1,
                'prodi_id' => 1,
                'kelas_id' => 1,
            ],
            [
                'name' => 'Mahasantri B',
                'username' => 'mahasiswa.b',
                'email' => 'mahasiswa.b@example.com',
                'phone' => '080012345671',
                'type'  => 1,
                'numb_nim' => 24213242,
                'semester' => 1,
                'prodi_id' => 2,
                'kelas_id' => 3,
            ],
            [
                'name' => 'Mahasantri C',
                'username' => 'mahasiswa.c',
                'email' => 'mahasiswa.c@example.com',
                'phone' => '080012345672',
                'type'  => 0,
            ],
            [
                'name' => 'Mahasantri D',
                'username' => 'mahasiswa.d',
                'email' => 'mahasiswa.d@example.com',
                'phone' => '080012345673',
                'type'  => 0,
            ],
        ];

        foreach ($mahasiswaUsers as $mahasiswa) {
            Mahasantri::create([
                'name' => $mahasiswa['name'],
                'code' => Str::random(6),
                'username' => $mahasiswa['username'],
                'email' => $mahasiswa['email'],
                'phone' => $mahasiswa['phone'],
                'type' => $mahasiswa['type'],
                'semester' => $mahasiswa['semester'] ?? 0,
                'numb_nim' => $mahasiswa['numb_nim'] ?? null,
                'prodi_id' => $mahasiswa['prodi_id'] ?? 0,
                'kelas_id' => $mahasiswa['kelas_id'] ?? 0,
                'password' => Hash::make('Mahasiswa123'),
            ]);
        }
    }
}
