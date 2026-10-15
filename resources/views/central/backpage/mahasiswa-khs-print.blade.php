<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kartu Hasil Studi - {{ $mahasiswa->name }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
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
            max-width: 297mm;
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
        
        .nilai-table .dosen-name {
            text-align: left;
            padding-left: 8px;
        }
        
        .nilai-table .kode-mk {
            text-align: center;
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
    @if(!isset($isPdf) || !$isPdf)
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print KHS
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Tutup
            </button>
        </div>
    @endif

        {{-- HEADER WITH KOP SURAT --}}
        <div class="header">
            @php use Illuminate\Support\Facades\Storage; @endphp

            @if($webs && $webs->kop_surat)
                @php
                    $relPath = 'images/kop-surat/'.$webs->kop_surat;   // UBAH jika foldermu beda
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
                            // Jika logo disimpan di disk public (url() -> storage/...)
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

{{-- STUDENT INFO --}}
<div class="info-section">
    <table class="info-table">
        <tr>
            <td class="label">Nim</td>
            <td class="colon">:</td>
            <td style="width: 200px;">{{ $mahasiswa->numb_nim ?? $mahasiswa->nim }}</td>
            <td class="label" style="padding-left: 100px;">Tahun Akademik</td>
            <td class="colon">:</td>
            <td>{{ $tahunAkademikName ?? 'Semua Semester' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Mahasantri</td>
            <td class="colon">:</td>
            <td>{{ $mahasiswa->name }}</td>
            <td class="label" style="padding-left: 100px;">Lokal/Semester</td>
            <td class="colon">:</td>
            <td>
                @if($semesterInfo)
                    {{ ($mahasiswa->kelas->name ?? '-') }} / {{ $semesterInfo }}
                @else
                    {{ $mahasiswa->kelas->name ?? '-' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td class="colon">:</td>
            <td>{{ $mahasiswa->prodi->name ?? '-' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

        {{-- NILAI TABLE --}}
        @if($grades->count() > 0)
            <table class="nilai-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 40px;">NO</th>
                        <th rowspan="2" style="width: 200px;">MATA KULIAH</th>
                        <th rowspan="2" style="width: 80px;">KODE MK</th>
                        <th rowspan="2" style="width: 40px;">SKS</th>
                        <th colspan="3" style="width: 120px;">NILAI</th>
                        <th rowspan="2" style="width: 150px;">DOSEN</th>
                    </tr>
                    <tr>
                        <th style="width: 40px;">ANGKA</th>
                        <th style="width: 40px;">HURUF</th>
                        <th style="width: 40px;">SKS X A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $index => $grade)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="mata-kuliah-name">{{ $grade->mataKuliah->name ?? '-' }}</td>
                            <td class="kode-mk">{{ $grade->mataKuliah->code ?? '-' }}</td>
                            <td>{{ $grade->mataKuliah->bsks ?? 0 }}</td>
                            <td>{{ number_format($grade->nilai_akhir, 0) }}</td>
                            <td><strong>{{ \App\Models\NilaiMahasiswa::convertToLetterGrade($grade->nilai_akhir) }}</strong></td>
                            <td>{{ number_format(($grade->mataKuliah->bsks ?? 0) * \App\Models\NilaiMahasiswa::convertToGradePoint($grade->nilai_akhir), 2) }}</td>
                            <td class="dosen-name">
                                @if($grade->dosen)
                                    {{ $grade->dosen->title_front ? $grade->dosen->title_front . ' ' : '' }}{{ $grade->dosen->name }}{{ $grade->dosen->title_behind ? ', ' . $grade->dosen->title_behind : '' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    {{-- Total Row --}}
                    <tr class="total-row">
                        <td colspan="3" style="text-align: center;">
                            @if($selectedSemester)
                                IP Semester
                            @else
                                IPK
                            @endif
                        </td>
                        <td colspan="3"></td>
                        <td><strong>
                            @if($selectedSemester)
                                {{ number_format($semesterGPA, 2) }}
                            @else
                                {{ number_format($cumulativeGPA, 2) }}
                            @endif
                        </strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 50px;">
                <p><strong>Tidak ada data nilai untuk ditampilkan</strong></p>
            </div>
        @endif

        {{-- SIGNATURE SECTION --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">{{ $webs->signature_title ?? 'Wakil Mudir Bidang Akademik Bagian Kurikulum' }}</div>
                <div class="signature-name">
                    @if($webs->signature_name)
                        {!! $webs->signature_name !!} @else
                        Ust. Syamsul Mu'arif, M.Pd.Gr
                    @endif
                </div>
            </div>
        </div>
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