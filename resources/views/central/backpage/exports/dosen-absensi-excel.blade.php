<table>
    <tr>
        <td colspan="18" align="center" style="font-size: 14px;"><b>Absen Perkuliahan MahaSantri Ma'had Aly Al Baladul Amin</b></td>
    </tr>
    <tr>
        <td colspan="18" align="center" style="font-size: 12px;"><b>Tahun Ajaran {{ date('Y') }}/{{ date('Y', strtotime('+1 year')) }}</b></td>
    </tr>
    <tr>
        <td colspan="3"><b>Nama Dosen:</b></td>
        <td colspan="15">{{ $dosen->name }}</td>
    </tr>
    <tr>
        <td colspan="3"><b>Mata Kuliah:</b></td>
        <td colspan="15">{{ $matkul->name }}</td>
    </tr>
    <tr>
        <td colspan="3"><b>Kelas:</b></td>
        <td colspan="15">{{ $kelompok }}</td>
    </tr>
    <tr></tr> <tr>
        <th align="center" style="border: 1px solid #000000; background-color: #f3f4f6;">No</th>
        <th style="border: 1px solid #000000; width: 300px; background-color: #f3f4f6;">Nama Mahasantri</th>
        <th align="center" style="border: 1px solid #000000; width: 150px; background-color: #f3f4f6;">NIM</th>
        @for($i = 1; $i <= 14; $i++)
            <th align="center" style="border: 1px solid #000000; background-color: #f3f4f6;">{{ $i }}</th>
        @endfor
        <th align="center" style="border: 1px solid #000000; background-color: #f3f4f6;">% Hadir</th>
    </tr>

    @php $no = 1; @endphp
    @foreach($rekapData as $data)
        <tr>
            <td align="center" style="border: 1px solid #000000;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000;">{{ $data['mhs']->name }}</td>
            <td align="center" style="border: 1px solid #000000;">{{ $data['mhs']->nim }}</td>
            
            @for($i = 1; $i <= 14; $i++)
                @php 
                    $status = $data['pertemuan'][$i]; 
                @endphp
                <td align="center" style="border: 1px solid #000000;">{{ $status }}</td>
            @endfor
            
            <td align="center" style="border: 1px solid #000000;">{{ $data['persentase'] }}%</td>
        </tr>
    @endforeach
</table>