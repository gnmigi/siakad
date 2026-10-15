@extends('central.backpage.core-backpage-mahasiswa')

@section('title')
    Kwitansi Pembayaran - {{ config('app.name') }}
@endsection

@section('content')
<div class="page-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-receipt text-primary"></i> Kwitansi Pembayaran
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('mahasiswa.payment.show', $transaction->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('mahasiswa.payment.receipt', $transaction->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Receipt Content -->
                    <div id="receipt-content" class="receipt-web-view">
                        <style>
                            .receipt-web-view {
                                background: white;
                                padding: 20px;
                                font-family: 'Times New Roman', serif;
                                color: #000;
                                line-height: 1.4;
                                font-size: 12px;
                                max-width: 210mm;
                                margin: 0 auto;
                            }
                            
                            .receipt-header {
                                text-align: center;
                                border-bottom: 2px solid #000;
                                padding-bottom: 15px;
                                margin-bottom: 20px;
                            }
                            
                            .institution-name {
                                font-size: 18px;
                                font-weight: bold;
                                color: #000;
                                margin-bottom: 5px;
                            }
                            
                            .institution-address {
                                font-size: 11px;
                                color: #000;
                                margin-bottom: 3px;
                            }
                            
                            .receipt-title {
                                font-size: 16px;
                                font-weight: bold;
                                color: #000;
                                margin-top: 15px;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                                text-decoration: underline;
                            }
                            
                            .receipt-info {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 20px;
                                margin-bottom: 20px;
                            }
                            
                            .info-section h5 {
                                color: #000;
                                font-weight: bold;
                                margin-bottom: 10px;
                                border-bottom: 1px solid #000;
                                padding-bottom: 3px;
                                font-size: 13px;
                            }
                            
                            .info-item {
                                display: flex;
                                margin-bottom: 6px;
                            }
                            
                            .info-label {
                                font-weight: bold;
                                width: 120px;
                                color: #000;
                                font-size: 11px;
                            }
                            
                            .info-value {
                                flex: 1;
                                color: #000;
                                font-size: 11px;
                            }
                            
                            .payment-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 15px;
                                border: 1px solid #000;
                            }
                            
                            .payment-table th {
                                background: #f0f0f0;
                                color: #000;
                                padding: 8px;
                                text-align: center;
                                font-weight: bold;
                                border: 1px solid #000;
                                font-size: 11px;
                            }
                            
                            .payment-table td {
                                padding: 8px;
                                border: 1px solid #000;
                                font-size: 11px;
                            }
                            
                            .payment-table .amount {
                                text-align: right;
                                font-weight: bold;
                                font-family: 'Times New Roman', serif;
                            }
                            
                            .total-row {
                                background: #f0f0f0;
                                font-weight: bold;
                                font-size: 12px;
                            }
                            
                            .amount-words {
                                background: #f9f9f9;
                                border: 1px solid #000;
                                padding: 10px;
                                text-align: center;
                                margin-bottom: 15px;
                                font-style: italic;
                                font-size: 11px;
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
                                font-size: 12px;
                            }
                            
                            .signatures {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 30px;
                                margin-top: 15px;
                            }
                            
                            .signature-box {
                                text-align: center;
                            }
                            
                            .signature-title {
                                font-weight: bold;
                                margin-bottom: 5px;
                                color: #000;
                                font-size: 11px;
                            }
                            
                            .signature-date {
                                font-size: 10px;
                                color: #000;
                                margin-bottom: 30px;
                            }
                            
                            .signature-name {
                                font-weight: bold;
                                border-top: 1px solid #000;
                                padding-top: 5px;
                                color: #000;
                                font-size: 11px;
                            }
                            
                            .footer {
                                margin-top: 15px;
                                text-align: center;
                                font-size: 9px;
                                color: #888;
                                border-top: 1px solid #ccc;
                                padding-top: 10px;
                            }
                            
                            .footer p {
                                margin: 2px 0;
                                color: #888;
                                font-weight: normal;
                            }
                            
                            .footer strong {
                                color: #555;
                                font-weight: bold;
                            }
                            
                            .status-badge {
                                display: inline-block;
                                background: #000;
                                color: white;
                                padding: 4px 8px;
                                border-radius: 3px;
                                font-size: 10px;
                                font-weight: bold;
                                text-transform: uppercase;
                            }
                            
                            @media (max-width: 768px) {
                                .receipt-info {
                                    grid-template-columns: 1fr;
                                    gap: 15px;
                                }
                                .signatures {
                                    grid-template-columns: 1fr;
                                    gap: 20px;
                                }
                            }

                            /* Dark Mode Styling */
                            [data-bs-theme="dark"] .receipt-web-view {
                                background: var(--tblr-card-bg, #182235) !important;
                                color: var(--tblr-body-color, #f4f6fa) !important;
                                border: 1px solid var(--tblr-border-color, #2d3d5a) !important;
                            }

                            [data-bs-theme="dark"] .receipt-header {
                                border-bottom: 2px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .institution-name,
                            [data-bs-theme="dark"] .receipt-title,
                            [data-bs-theme="dark"] .signature-title,
                            [data-bs-theme="dark"] .signature-name,
                            [data-bs-theme="dark"] .payment-method-info h5 {
                                color: var(--tblr-body-color, #ffffff) !important;
                            }

                            [data-bs-theme="dark"] .institution-address,
                            [data-bs-theme="dark"] .signature-date,
                            [data-bs-theme="dark"] .footer p,
                            [data-bs-theme="dark"] .info-label {
                                color: var(--tblr-muted, #94a3b8) !important;
                            }

                            [data-bs-theme="dark"] .info-value,
                            [data-bs-theme="dark"] .payment-table td {
                                color: var(--tblr-body-color, #e2e8f0) !important;
                            }

                            [data-bs-theme="dark"] .info-section h5 {
                                color: #6366f1 !important;
                                border-bottom: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .payment-table {
                                border: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .payment-table th {
                                background: rgba(255, 255, 255, 0.03) !important;
                                color: var(--tblr-body-color, #ffffff) !important;
                                border: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .payment-table td {
                                border: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .total-row {
                                background: rgba(255, 255, 255, 0.05) !important;
                                color: var(--tblr-body-color, #ffffff) !important;
                            }

                            [data-bs-theme="dark"] .amount-words {
                                background: rgba(255, 255, 255, 0.02) !important;
                                border: 1px solid var(--tblr-border-color, #2d3d5a);
                                color: var(--tblr-body-color, #e2e8f0) !important;
                            }

                            [data-bs-theme="dark"] .payment-method-info {
                                background: rgba(255, 255, 255, 0.02) !important;
                                border: 1px solid var(--tblr-border-color, #2d3d5a);
                                color: var(--tblr-body-color, #e2e8f0) !important;
                            }

                            [data-bs-theme="dark"] .signature-name {
                                border-top: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .footer {
                                color: var(--tblr-muted, #64748b);
                                border-top: 1px solid var(--tblr-border-color, #2d3d5a);
                            }

                            [data-bs-theme="dark"] .footer p {
                                color: var(--tblr-muted, #64748b) !important;
                            }

                            [data-bs-theme="dark"] .footer strong {
                                color: var(--tblr-body-color, #94a3b8) !important;
                            }

                            [data-bs-theme="dark"] .status-badge {
                                background: rgba(16, 185, 129, 0.2) !important;
                                color: #10b981 !important;
                                border: 1px solid rgba(16, 185, 129, 0.3) !important;
                            }

                            /* Handle image background on dark mode */
                            [data-bs-theme="dark"] .receipt-web-view img {
                                background-color: #ffffff;
                                padding: 10px;
                                border-radius: 8px;
                                display: block;
                                margin: 0 auto 20px auto;
                            }

                            /* Override inline styles in fallback logo text */
                            [data-bs-theme="dark"] .receipt-header h2 {
                                color: #6366f1 !important;
                            }
                            [data-bs-theme="dark"] .receipt-header p {
                                color: var(--tblr-muted, #cbd5e1) !important;
                            }

                            /* Print Styles */
                            @media print {
                                .receipt-web-view, .receipt-web-view * {
                                    background: transparent !important;
                                    color: #000 !important;
                                    border-color: #000 !important;
                                }
                                .receipt-web-view img {
                                    background-color: transparent !important;
                                    padding: 0 !important;
                                }
                                .status-badge {
                                    background: #000 !important;
                                    color: #fff !important;
                                    border: none !important;
                                }
                            }
                        </style>
                        
                        <!-- Header with Official Letterhead -->
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
                                    <img src="data:{{ $kopSuratMime }};base64,{{ $kopSuratData }}" alt="Kop Surat" style="max-width:100%;height:auto;margin-bottom:20px;">
                                @else
                                    {{-- Fallback: teks --}}
                                    <div style="text-align:center;margin-bottom:20px;">
                                        <h2 style="margin:5px 0;font-size:24px;color:#007bff;font-weight:bold;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                                        <p style="margin:2px 0;font-size:14px;color:#666;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                                        <p style="margin:2px 0;font-size:14px;color:#666;">
                                            @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                                            @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                                        </p>
                                    </div>
                                @endif
                            @else
                                {{-- Default header jika tidak ada kop surat --}}
                                <div style="text-align:center;margin-bottom:20px;">
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

                                    <h2 style="margin:5px 0;font-size:24px;color:#007bff;font-weight:bold;">{{ $webs->school_name ?? 'NAMA INSTITUSI' }}</h2>
                                    <p style="margin:2px 0;font-size:14px;color:#666;">{{ $webs->school_address ?? 'Alamat Institusi' }}</p>
                                    <p style="margin:2px 0;font-size:14px;color:#666;">
                                        @if($webs->school_phone)Telp: {{ $webs->school_phone }}@endif
                                        @if($webs->school_email) | Email: {{ $webs->school_email }}@endif
                                    </p>
                                </div>
                            @endif
                            
                            <div class="receipt-title">KWITANSI PEMBAYARAN</div>
                        </div>
                        
                        <!-- Receipt Information -->
                        <div class="receipt-info">
                            <div class="info-section">
                                <h5><i class="fas fa-receipt"></i> Informasi Kwitansi</h5>
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
                                <h5><i class="fas fa-user-graduate"></i> Data Mahasiswa</h5>
                                <div class="info-item">
                                    <span class="info-label">Nama:</span>
                                    <span class="info-value">{{ $mahasiswa->name ?? '-' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">NIM:</span>
                                    <span class="info-value">{{ $mahasiswa->numb_nim ?? '-' }}</span>
                                </div>
                                @if(isset($mahasiswa->program_studi) && $mahasiswa->program_studi)
                                <div class="info-item">
                                    <span class="info-label">Program Studi:</span>
                                    <span class="info-value">{{ $mahasiswa->program_studi }}</span>
                                </div>
                                @endif
                                @if(isset($mahasiswa->fakultas) && $mahasiswa->fakultas)
                                <div class="info-item">
                                    <span class="info-label">Fakultas:</span>
                                    <span class="info-value">{{ $mahasiswa->fakultas }}</span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <span class="info-label">Kode Transaksi:</span>
                                    <span class="info-value">{{ $transaction->transaction_code ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Details -->
                        <table class="payment-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%"><i class="fas fa-list"></i> Keterangan</th>
                                    <th style="width: 25%"><i class="fas fa-tag"></i> Jenis Tagihan</th>
                                    <th style="width: 25%"><i class="fas fa-money-bill"></i> Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
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
                                    <td>Biaya Administrasi</td>
                                    <td>-</td>
                                    <td class="amount">{{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="total-row">
                                    <td colspan="2"><strong><i class="fas fa-calculator"></i> TOTAL PEMBAYARAN</strong></td>
                                    <td class="amount"><strong>{{ number_format(($is_accumulated_installment ?? false) ? ($receipt->amount ?? 0) : ($transaction->total_amount ?? $receipt->amount), 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Amount in Words -->
                        <div class="amount-words">
                            <strong><i class="fas fa-spell-check"></i> Terbilang:</strong><br>
                            {{ $receipt->getAmountInWords() }}
                        </div>
                        
                        <!-- Payment Method Information -->
                        @if(isset($transaction->paymentMethod))
                        <div class="payment-method-info">
                            <h5><i class="fas fa-credit-card"></i> Informasi Pembayaran</h5>
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
                        
                        <!-- Signatures -->
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
                        
                        <!-- Footer -->
                        <div class="footer">
                            <p><strong><i class="fas fa-shield-alt"></i> KWITANSI RESMI</strong> - Dokumen ini sah untuk keperluan administrasi</p>
                            <p><strong>Kode Verifikasi:</strong> {{ $receipt->receipt_code }} | Dicetak: {{ $generated_at->locale('id')->translatedFormat('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
