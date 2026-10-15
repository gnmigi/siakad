<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MahasiswaGroupedExport implements WithMultipleSheets
{
    use Exportable;

    protected $groupedData;

    public function __construct($groupedData)
    {
        $this->groupedData = $groupedData;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->groupedData as $kelasName => $mahasiswas) {
            $sheets[] = new MahasiswaSheetExport($mahasiswas, $kelasName);
        }

        return $sheets;
    }
}

class MahasiswaSheetExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithHeadings
{
    protected $mahasiswas;
    protected $kelasName;

    public function __construct($mahasiswas, $kelasName)
    {
        $this->mahasiswas = $mahasiswas;
        $this->kelasName = $kelasName;
    }

    public function collection()
    {
        return $this->mahasiswas->map(function ($item) {
            return [
                'Nama' => $item->name,
                'Email' => $item->email,
                'Password' => $item->password, // Export encrypted password
                'No Telepon' => $item->phone,
                'NIM' => $item->numb_nim,
                'Program Studi' => $item->prodi->name ?? '',
                'Kelas' => $item->kelas ? $item->kelas->name . ' - ' . ($item->kelas->jenisKelas->name ?? 'Reguler') : '',
                'Semester' => $item->semester,
                'Takhasus' => $item->takhasus,
                'Type Mahasantri' => $item->type, // Export mahasiswa type
                'Jenis Kelamin' => $item->bio_gender,
                'Tempat Lahir' => $item->bio_placebirth,
                'Tanggal Lahir' => $item->bio_datebirth,
                'Agama' => $item->bio_religion,
                'Kewarganegaraan' => $item->bio_nationality,
                'No KTP/NIK' => $item->numb_ktp,
                'No Kartu Keluarga' => $item->numb_kk,
                'NISN' => $item->numb_nisn,
                'Alamat KTP' => $item->ktp_addres,
                'RT' => $item->ktp_rt,
                'RW' => $item->ktp_rw,
                'Kelurahan/Desa' => $item->ktp_village,
                'Kecamatan' => $item->ktp_subdistrict,
                'Kota/Kabupaten' => $item->ktp_city,
                'Provinsi' => $item->ktp_province,
                'Kode Pos' => $item->ktp_poscode,
                'Nama Ayah' => $item->father_name,
                'No Telepon Ayah' => $item->father_phone,
                'Pekerjaan Ayah' => $item->father_occupation,
                'Nama Ibu' => $item->mother_name,
                'No Telepon Ibu' => $item->mother_phone,
                'Pekerjaan Ibu' => $item->mother_occupation,
                'Jenjang Pendidikan Terakhir' => $item->edu1_type,
                'Nama Sekolah/Universitas' => $item->edu1_place,
                'Jurusan Pendidikan Terakhir' => $item->edu1_major,
                'Tahun Lulus Pendidikan Terakhir' => $item->edu1_graduate_year,
            ];
        });
    }

    public function title(): string
    {
        // Get the first mahasiswa to access kelas information
        $firstMahasiswa = $this->mahasiswas->first();
        
        if ($firstMahasiswa && $firstMahasiswa->kelas) {
            $kelas = $firstMahasiswa->kelas;
            $jenisKelas = $kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler';
            
            // Format: "NamaKelas - JenisKelas"
            $fullTitle = $kelas->name . ' - ' . $jenisKelas;
            
            // Excel sheet title max length 31 characters
            return substr($fullTitle, 0, 31);
        }
        
        // Fallback to original kelasName if no kelas data
        return substr($this->kelasName, 0, 31);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Password',
            'No Telepon',
            'NIM',
            'Program Studi',
            'Kelas',
            'Semester',
            'Takhasus',
            'Type Mahasantri',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Kewarganegaraan',
            'No KTP/NIK',
            'No Kartu Keluarga',
            'NISN',
            'Alamat KTP',
            'RT',
            'RW',
            'Kelurahan/Desa',
            'Kecamatan',
            'Kota/Kabupaten',
            'Provinsi',
            'Kode Pos',
            'Nama Ayah',
            'No Telepon Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'No Telepon Ibu',
            'Pekerjaan Ibu',
            'Jenjang Pendidikan Terakhir',
            'Nama Sekolah/Universitas',
            'Jurusan Pendidikan Terakhir',
            'Tahun Lulus Pendidikan Terakhir',
        ];
    }
}
