<table>
    <thead>
        <tr>
            <th colspan="18" style="text-align: center; font-weight: bold; font-size: 14px;">Absen Perkuliahan MahaSantri Ma'had Aly Al Baladul Amin</th>
        </tr>
        <tr>
            <th colspan="18" style="text-align: center; font-weight: bold; font-size: 12px;">Tahun Ajaran {{ \App\Models\Akademik\TahunAkademik::where('status', 'Aktif')->first()->name ?? \App\Models\Akademik\TahunAkademik::latest()->first()->name ?? '2026/2027' }}</th>
        </tr>
        <tr>
            <th colspan="3">Nama Dosen:</th>
            <th colspan="15" style="text-align: left;">{{ $dosen->name ?? 'Belum Ditentukan' }}</th>
        </tr>
        <tr>
            <th colspan="3">Mata Kuliah:</th>
            <th colspan="15" style="text-align: left;">{{ $matkul->name }}</th>
        </tr>
        <tr>
            <th colspan="3">Kelas:</th>
            <th colspan="15" style="text-align: left;">{{ $kelasName }} (Semester Aktif {{ $semester }})</th>
        </tr>
        <tr>
            <th colspan="18"></th>
        </tr>
        <tr>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 5px;">No</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 35px;">Nama Mahasantri</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 15px;">NIM</th>
            @for($i=1; $i<=14; $i++)
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 4px;">{{ $i }}</th>
            @endfor
            <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 10px;">% Hadir</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($rekap as $data)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000;">{{ $data['mhs']->name }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $data['mhs']->nim }}</td>
            @for($i=1; $i<=14; $i++)
            <td style="border: 1px solid #000; text-align: center;">{{ $data['pertemuan'][$i] }}</td>
            @endfor
            <td style="border: 1px solid #000; text-align: center;">{{ $data['persentase'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>