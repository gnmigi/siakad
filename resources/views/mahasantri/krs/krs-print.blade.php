<!DOCTYPE html>
<html>
<head>
    <title>Kartu Rencana Studi - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 700px; margin: 0 auto; padding: 1px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        .info-table td { border: none; padding: 4px 8px; text-align: left; }
        .signature { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="container">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ asset('storage/images/website/kop-maalamin.png') }}"
                 alt="Kop Surat"
                 style="width:100%; max-width:1000px; height:auto; margin:0 auto;">
        </div>

        {{-- JUDUL --}}
        <h2 style="text-align:center; margin:0 0 20px;">Kartu Rencana Studi</h2>

        {{-- INFO MAHASISWA & SEMESTER --}}
        <table class="info-table">
            <tr>
                <td><strong>Nama</strong></td>
                <td>:</td>
                <td>{{ $user->name }}</td>
                <td><strong>Program Studi</strong></td>
                <td>:</td>
                <td>{{ $pstudi->name }}</td>
            </tr>
            <tr>
                <td><strong>NIM</strong></td>
                <td>:</td>
                <td>{{ $user->nim }}</td>
                <td><strong>Semester</strong></td>
                <td>:</td>
                <td>{{ $taka->name }} - {{ $taka->semester }}</td>
            </tr>
        </table>

        {{-- DAFTAR MATA KULIAH --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th style="text-align:left;">Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen Pengampu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matkul as $index => $mk)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align:left;">{{ $mk->name }}</td>
                        <td>{{ $mk->bsks }}</td>
                        <td>{{ optional($mk->dosen1)->dsn_name ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">Tidak ada mata kuliah pada semester aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="signature">
            <p>Telaga Langsat, {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
            <p>Mengetahui,</p>
            <p style="margin-top:60px;"><strong>{{ $pstudi->head->dsn_name }}</strong><br>Wakil Mudir Bidang Akademik</p>
        </div>

    </div>
</body>
</html>
