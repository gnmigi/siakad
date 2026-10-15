<!DOCTYPE html>
<html>
<head>
    <title>Transkrip Nilai - {{ $mahasiswa->name }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11px;
            line-height: 1.3;
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
        
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }
        
        .nilai-table th {
            background-color: #90EE90;
            font-weight: bold;
        }
        
        .nilai-table .mata-kuliah-name {
            text-align: left;
            padding-left: 8px;
        }
        
        .nilai-table .total-row {
            background-color: #90EE90;
            font-weight: bold;
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
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button - Only show when not PDF -->
    @if(!isset($isPdf) || !$isPdf)
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print Transkrip
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Tutup
            </button>
        </div>
    @endif

    <div class="container">
        {{-- HEADER WITH KOP SURAT --}}
        <div class="header">
            @php use Illuminate\Support\Facades\Storage; @endphp

            @if($webs && $webs->kop_surat)
                @php
                    $relPath = 'images/kop-surat/'.$webs->kop_surat;
                    $absPath = Storage::disk('public')->path($relPath);

                    $kopSuratData = null;
                    $kopSuratMime = null;
                    if (file_exists($absPath)) {
                        $kopSuratData = base64_encode(file_get_contents($absPath));
                        $kopSuratMime = mime_content_type($absPath);
                    }
                @endphp

                @if($kopSuratData)
                    <img src="data:{{ $kopSuratMime }};base64,{{ $kopSuratData }}" alt="Kop Surat" style="max-width:100%;height:auto;">
                @else
                    {{-- Fallback: teks --}}
                    <div style="text-align:center;">
                        <h2 style="margin:5px 0;font-size:18px;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                        <p style="margin:2px 0;font-size:12px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                        <p style="margin:2px 0;font-size:12px;">
                            @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                            @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                        </p>
                    </div>
                @endif
            @else
                {{-- Default header jika tidak ada kop surat --}}
                <div style="text-align:center;">
                    @php
                        $logoData = null; $logoMime = null;
                        if($webs && $webs->school_logo_vert){
                            $logoRel = ltrim(str_replace(Storage::url(''), '', $webs->school_logo_vert), '/');
                            $logoAbs = Storage::disk('public')->path($logoRel);
                            if(file_exists($logoAbs)){
                                $logoData = base64_encode(file_get_contents($logoAbs));
                                $logoMime = mime_content_type($logoAbs);
                            }
                        }
                    @endphp

                    @if($logoData)
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo" style="height:80px;margin-bottom:10px;">
                    @endif

                    <h2 style="margin:5px 0;font-size:18px;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                    <p style="margin:2px 0;font-size:12px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                    <p style="margin:2px 0;font-size:12px;">
                        @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                        @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                    </p>
                </div>
            @endif
        </div>

        {{-- TITLE --}}
        <div class="title">TRANSKRIP NILAI AKADEMIK</div>

        {{-- STUDENT INFO --}}
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td>{{ $mahasiswa->name }}</td>
                </tr>
                <tr>
                    <td class="label">NIM</td>
                    <td class="colon">:</td>
                    <td>{{ $mahasiswa->numb_nim ?? $mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td class="label">Program Studi</td>
                    <td class="colon">:</td>
                    <td>{{ $mahasiswa->prodi->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Takhasus</td>
                    <td class="colon">:</td>
                    <td>{{ $mahasiswa->takhasusOption->name ?? $mahasiswa->takhasus ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td class="colon">:</td>
                    <td>{{ $mahasiswa->kelas->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- NILAI TABLE --}}
        @if($grades->count() > 0)
            <table class="nilai-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 300px;">Mata Kuliah</th>
                        <th style="width: 50px;">SKS</th>
                        <th style="width: 80px;">Nilai Angka</th>
                        <th style="width: 60px;">Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $index => $grade)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="mata-kuliah-name">{{ $grade->mataKuliah->name ?? '-' }}</td>
                            <td>{{ $grade->mataKuliah->bsks ?? 0 }}</td>
                            <td><strong>{{ number_format($grade->nilai_akhir ?? 0, 1) }}</strong></td>
                            <td>
                                @php
                                    $letterGrade = \App\Models\NilaiMahasiswa::convertToLetterGrade($grade->nilai_akhir ?? 0);
                                @endphp
                                <strong>{{ $letterGrade }}</strong>
                            </td>
                        </tr>
                    @endforeach
                    
                    {{-- Total SKS Row --}}
                    <tr class="total-row">
                        <td colspan="2" style="text-align: center;">TOTAL SKS</td>
                        <td><strong>{{ $totalCredits ?? $grades->sum(function($g) { return $g->mataKuliah->bsks ?? 0; }) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            {{-- IPK SECTION --}}
            <div style="text-align: center; margin: 20px 0;">
                <table style="margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px 15px; font-weight: bold; background-color: #90EE90;">
                            IPK (Indeks Prestasi Kumulatif)
                        </td>
                        <td style="border: 1px solid #000; padding: 8px 15px; font-weight: bold; background-color: #90EE90;">
                            {{ number_format($cumulativeGPA ?? 0, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 50px;">
                <p><strong>Belum ada data nilai untuk ditampilkan</strong></p>
            </div>
        @endif

        {{-- SIGNATURE SECTION --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">{{ $webs->signature_title ?? 'Wakil Mudir Bidang Akademik' }}</div>
                <div class="signature-name">
                    @if($webs->signature_name)
                        ({{ $webs->signature_name }})
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

        {{-- PRINT DATE --}}
        @if(isset($printDate))
        <div style="font-size: 10px; text-align: right; margin-top: 20px; color: #666;">
            Dicetak pada: {{ $printDate }}
        </div>
        @endif
    </div>

    <script>
        // Auto print when page loads (for PDF generation)
        window.onload = function() {
            // Only auto-print if this is a PDF export request
            if (window.location.href.includes('export-pdf')) {
                window.print();
            }
        }
    </script>
</body>
</html>
