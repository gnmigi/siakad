<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran - {{ $receipt->receipt_code }}</title>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #000;
        }
        
        .container { 
            width: 100%; 
            max-width: 210mm;
            margin: 0 auto; 
        }
        
        .receipt-header { 
            text-align: center; 
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .receipt-header img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0 20px 0;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .receipt-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .info-section:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
        
        .info-section h5 {
            color: #000;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            font-size: 12px;
        }
        
        .info-item {
            margin-bottom: 6px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 110px;
            color: #000;
            font-size: 10px;
        }
        
        .info-value {
            color: #000;
            font-size: 10px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #000;
        }
        
        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        
        .payment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .payment-table .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .payment-table .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .amount-words {
            background: #f9f9f9;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            font-style: italic;
            font-size: 10px;
        }
        
        .payment-method-info {
            background: #f9f9f9;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .payment-method-info h5 {
            color: #000;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
            font-size: 10px;
        }
        
        .signature-date {
            font-size: 9px;
            color: #000;
            margin-bottom: 50px;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            color: #000;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #000;
            border-top: 2px solid #000;
            padding-top: 12px;
        }
        
        .footer p {
            margin: 4px 0;
            color: #000 !important;
            font-weight: normal;
        }
        
        .footer strong {
            color: #000 !important;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            background: #000;
            color: white;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- HEADER WITH KOP SURAT --}}
        <div class="receipt-header">
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
                        <h2 style="margin:5px 0;font-size:16px;font-weight:bold;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                        <p style="margin:2px 0;font-size:10px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                        <p style="margin:2px 0;font-size:10px;">
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
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo" style="height:60px;margin-bottom:10px;">
                    @endif

                    <h2 style="margin:5px 0;font-size:16px;font-weight:bold;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                    <p style="margin:2px 0;font-size:10px;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                    <p style="margin:2px 0;font-size:10px;">
                        @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                        @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                    </p>
                </div>
            @endif
        </div>

        <div class="receipt-title">KWITANSI PEMBAYARAN</div>

        {{-- RECEIPT INFO --}}
        <div class="receipt-info">
            <div class="info-section">
                <h5>Informasi Kwitansi</h5>
                <div class="info-item">
                    <span class="info-label">Nomor Kwitansi:</span>
                    <span class="info-value">{{ $receipt->receipt_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kode Kwitansi:</span>
                    <span class="info-value">{{ $receipt->receipt_code }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $receipt->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge">LUNAS</span>
                    </span>
                </div>
            </div>
            
            <div class="info-section">
                <h5>Data Mahasiswa</h5>
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $mahasiswa->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIM:</span>
                    <span class="info-value">{{ $mahasiswa->numb_nim ?? 'N/A' }}</span>
                </div>
                @if(isset($mahasiswa->prodi))
                <div class="info-item">
                    <span class="info-label">Program Studi:</span>
                    <span class="info-value">{{ $mahasiswa->prodi->name ?? 'N/A' }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Kode Transaksi:</span>
                    <span class="info-value">{{ $transaction->transaction_code ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- PAYMENT TABLE --}}
        <table class="payment-table">
            <thead>
                <tr>
                    <th style="width: 50%">Keterangan</th>
                    <th style="width: 25%">Jenis Tagihan</th>
                    <th style="width: 25%">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; padding-left: 8px;">
                        @php
                            // Get the proper payment description using dynamic naming
                            $paymentDescription = 'Pembayaran Akademik';
                            if (isset($tagihan->desc) && !empty($tagihan->desc)) {
                                $paymentDescription = 'Pembayaran ' . $tagihan->desc;
                            } elseif (isset($tagihan->group->name)) {
                                // Fallback to group name if desc is not available
                                $groupName = $tagihan->group->name;
                                $paymentDescription = 'Pembayaran ' . explode(' - ', $groupName)[0];
                            } elseif (isset($receipt->description)) {
                                $paymentDescription = $receipt->description;
                            }
                        @endphp
                        {{ $paymentDescription }}
                    </td>
                    <td>
                        @php
                            // Extract only the main category for jenis tagihan
                            $jenisTagihan = 'Akademik';
                            if (isset($tagihan->desc) && !empty($tagihan->desc)) {
                                $fullDesc = $tagihan->desc;
                                // Extract main category (e.g., "SPP Semester" from "SPP-Semester 5 - 2026/2027 Ganjil")
                                if (strpos($fullDesc, '-') !== false) {
                                    $parts = explode('-', $fullDesc);
                                    $jenisTagihan = trim($parts[0]);
                                    // Add the type if it exists (e.g., "Semester" from "SPP-Semester")
                                    if (count($parts) > 1) {
                                        $secondPart = trim($parts[1]);
                                        // Extract just the type without number/year (e.g., "Semester" from "Semester 5 - 2026/2027 Ganjil")
                                        if (strpos($secondPart, ' ') !== false) {
                                            $typeParts = explode(' ', $secondPart);
                                            $jenisTagihan .= ' ' . $typeParts[0];
                                        } else {
                                            $jenisTagihan .= ' ' . $secondPart;
                                        }
                                    }
                                } else {
                                    // If no dash, try to extract first two words
                                    $words = explode(' ', $fullDesc);
                                    if (count($words) >= 2) {
                                        $jenisTagihan = $words[0] . ' ' . $words[1];
                                    } else {
                                        $jenisTagihan = $words[0];
                                    }
                                }
                            } elseif (isset($tagihan->group->name)) {
                                // Fallback to group name if desc is not available
                                $groupName = $tagihan->group->name;
                                $jenisTagihan = explode(' - ', $groupName)[0];
                                $jenisTagihan = str_replace('-', ' ', $jenisTagihan);
                            }
                        @endphp
                        {{ $jenisTagihan }}
                    </td>
                    <td class="amount">{{ number_format(($is_accumulated_installment ?? false) ? ($receipt->amount ?? 0) : ($transaction->amount ?? $receipt->amount), 0, ',', '.') }}</td>
                </tr>
                @if(isset($transaction->fee_amount) && $transaction->fee_amount > 0)
                <tr>
                    <td style="text-align: left; padding-left: 8px;">Biaya Administrasi</td>
                    <td>-</td>
                    <td class="amount">{{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2" style="text-align: center;"><strong>TOTAL PEMBAYARAN</strong></td>
                    <td class="amount"><strong>{{ number_format(($is_accumulated_installment ?? false) ? ($receipt->amount ?? 0) : ($transaction->total_amount ?? $receipt->amount), 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- AMOUNT IN WORDS --}}
        <div class="amount-words">
            <strong>Terbilang:</strong><br>
            {{ $receipt->getAmountInWords() }}
        </div>

        {{-- PAYMENT METHOD INFO --}}
        @if(isset($transaction->paymentMethod))
        <div class="payment-method-info">
            <h5>Informasi Pembayaran</h5>
            <div class="info-item">
                <span class="info-label">Metode Pembayaran:</span>
                <span class="info-value">{{ $transaction->paymentMethod->name }}</span>
            </div>
            @if(isset($transaction->bankAccount))
            <div class="info-item">
                <span class="info-label">Bank Tujuan:</span>
                <span class="info-value">{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</span>
            </div>
            @endif
            @if(isset($transaction->paid_at))
            <div class="info-item">
                <span class="info-label">Tanggal Pembayaran:</span>
                <span class="info-value">
                    @php
                        $paidAt = \Carbon\Carbon::parse($transaction->paid_at);
                        $hasTime = $paidAt->format('H:i:s') !== '00:00:00';
                        $formattedPaidAt = $paidAt->locale('id')->translatedFormat($hasTime ? 'd F Y H:i' : 'd F Y');
                    @endphp
                    {{ $formattedPaidAt }}
                </span>
            </div>
            @endif
        </div>
        @endif

        {{-- SIGNATURES --}}
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Yang Menerima</div>
                <div class="signature-date">Telaga Langsat, {{ $receipt->created_at->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="signature-name">Bagian Keuangan</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-title">Yang Membayar</div>
                <div class="signature-date">Telaga Langsat, {{ $receipt->created_at->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="signature-name">{{ $mahasiswa->name ?? 'Mahasiswa' }}</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <p><strong>KWITANSI RESMI</strong></p>
            <p>Kwitansi ini dicetak secara otomatis pada {{ $generated_at->locale('id')->translatedFormat('d F Y H:i:s') }}</p>
            <p>Dokumen ini sah dan dapat digunakan untuk keperluan administrasi</p>
            <p><strong>Kode Verifikasi:</strong> {{ $receipt->receipt_code }}</p>
        </div>
    </div>
</body>
</html>
