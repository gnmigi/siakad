<!DOCTYPE html>
<html>
<head>
    <title>Data Akademik Mahasantri</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin-top: 40px; page-break-before: always; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Data Akademik Mahasantri</h1>
    @foreach ($grouped as $kelasName => $mahasiswas)
        <h2>{{ $kelasName }}</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIM</th>
                    <th>Program Studi</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Takhasus</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswas as $index => $mhs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $mhs->name }}</td>
                        <td>{{ $mhs->email }}</td>
                        <td>{{ $mhs->numb_nim }}</td>
                        <td>{{ $mhs->prodi->name ?? '' }}</td>
                        <td>{{ $mhs->kelas ? $mhs->kelas->name . ' - ' . ($mhs->kelas->jenisKelas->name ?? 'Reguler') : '' }}</td>
                        <td>{{ $mhs->semester }}</td>
                        <td>{{ $mhs->takhasus }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
