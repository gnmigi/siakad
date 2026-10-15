<!DOCTYPE html>
<html>
<head>
    <title>Kartu Rencana Studi - {{ $krs->mahasiswa->name }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        .container { 
            width: 100%; 
            max-width: 210mm;
            margin: 0 auto; 
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        
        .header img {
            max-width: 100%;
            height: auto;
            margin-bottom: 0;
        }
        
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0 20px 0;
            text-decoration: underline;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border: none;
            margin-bottom: 20px;
        }
        
        .info-table td {
            border: none;
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 150px;
            font-weight: normal;
        }
        
        .info-table .colon {
            width: 20px;
            text-align: center;
        }
        
        .mata-kuliah-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .mata-kuliah-table th,
        .mata-kuliah-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        .mata-kuliah-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .mata-kuliah-table .mata-kuliah-name {
            text-align: left;
            padding-left: 10px;
        }
        
        .mata-kuliah-table .dosen-name {
            text-align: left;
            padding-left: 10px;
        }
        
        .signature-section {
            margin-top: 50px;
            text-align: right;
            padding-right: 50px;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 250px;
        }
        
        .signature-title {
            margin-bottom: 80px;
            font-weight: bold;
        }
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .footer-notes {
            margin-top: 30px;
            font-size: 10px;
        }
        
        .footer-notes ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .footer-notes li {
            margin-bottom: 5px;
        }
        
        @media print {
            body { 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .container {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- HEADER WITH KOP SURAT --}}
        <div class="header">
            @if($webs && $webs->kop_surat)
            @php


                $relPath = 'images/kop-surat/'.$webs->kop_surat;          // sesuaikan folder yang kamu pakai
                $absPath = Storage::disk('public')->path($relPath);       // /storage/app/public/...

                if (file_exists($absPath)) {
                    $kopSuratData = base64_encode(file_get_contents($absPath));
                    $kopSuratMime = mime_content_type($absPath);
                }
            @endphp
                {{-- Jika file kop surat ada, tampilkan --}}
                @if(isset($kopSuratData))
                    <img src="data:{{ $kopSuratMime }};base64,{{ $kopSuratData }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
                @else
                    {{-- Fallback jika file kop surat tidak ditemukan --}}
                    <div style="text-align: center;">
                        <h2 style="margin: 5px 0; font-size: 18px;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                        <p style="margin: 2px 0; font-size: 12px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                        <p style="margin: 2px 0; font-size: 12px;">
                            @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                            @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                        </p>
                    </div>
                @endif
            @else
                {{-- Default header jika tidak ada kop surat --}}
                <div style="text-align: center;">
                    @if($webs && $webs->school_logo_vert)
                        @php
                            $logoPath = public_path(str_replace(url('/'), '', $webs->school_logo_vert));
                            if (file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            }
                        @endphp
                        @if(isset($logoData))
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" 
                                 alt="Logo" style="height: 80px; margin-bottom: 10px;">
                        @endif
                    @endif
                    <h2 style="margin: 5px 0; font-size: 18px;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                    <p style="margin: 2px 0; font-size: 12px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                    <p style="margin: 2px 0; font-size: 12px;">
                        @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                        @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                    </p>
                </div>
            @endif
        </div>

        {{-- TITLE --}}
        <div class="title">KARTU RENCANA STUDI</div>

        {{-- STUDENT INFO --}}
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td>{{ $krs->mahasiswa->name }}</td>
                </tr>
                <tr>
                    <td class="label">NIM</td>
                    <td class="colon">:</td>
                    <td>{{ $krs->mahasiswa->numb_nim }}</td>
                </tr>
                <tr>
                    <td class="label">Tahun Akademik/Semester</td>
                    <td class="colon">:</td>
                    <td>{{ $krs->tahunAkademik->name ?? '-' }} / {{ $krs->semester }}</td>
                </tr>
                <tr>
                    <td class="label">Takhasus</td>
                    <td class="colon">:</td>
                    <td>{{ $krs->mahasiswa->takhasus ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Konsentrasi</td>
                    <td class="colon">:</td>
                    <td>{{ $krs->mahasiswa->prodi->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- MATA KULIAH TABLE --}}
        <table class="mata-kuliah-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 300px;">Nama Mata Kuliah</th>
                    <th style="width: 60px;">SKS</th>
                    <th style="width: 200px;">Nama Dosen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($krs->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="mata-kuliah-name">{{ $detail->mataKuliah->name ?? '-' }}</td>
                        <td>{{ $detail->sks }}</td>
                        <td class="dosen-name">
                            @if($detail->mataKuliah->dosen1)
                                {{ $detail->mataKuliah->dosen1->title_front ? $detail->mataKuliah->dosen1->title_front . ' ' : '' }}{{ $detail->mataKuliah->dosen1->name }}{{ $detail->mataKuliah->dosen1->title_behind ? ', ' . $detail->mataKuliah->dosen1->title_behind : '' }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; font-style: italic;">
                            Tidak ada mata kuliah pada KRS ini
                        </td>
                    </tr>
                @endforelse
                
                {{-- Total SKS Row --}}
                <tr style="font-weight: bold;">
                    <td colspan="2" style="text-align: right; padding-right: 15px;">TOTAL SKS</td>
                    <td>{{ $krs->details->sum('sks') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        {{-- SIGNATURE SECTION --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">{{ $webs->signature_title ?? 'Wakil Mudir Bidang Akademik' }}</div>
                <div class="signature-name">
                    @if($webs->signature_name)
                        ({{ $webs->signature_name }})
                    @elseif($krs->approvedBy)
                        ({{ $krs->approvedBy->name }})
                    @else
                        ( _________________________ )
                    @endif
                </div>
            </div>
        </div>

        {{-- FOOTER NOTES --}}
        <div class="footer-notes">
            <!--<strong>Lembar</strong>-->
            <!--<ul>-->
            <!--    <li>Wakil Mudir Bidang Akademik Bagian Kurikulum</li>-->
            <!--    <li>Kepala Tata Usaha</li>-->
            <!--    <li>Mahasantri</li>-->
            <!--</ul>-->
        </div>
    </div>
</body>
</html>
