<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Resmi - {{ $receipt->receipt_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            background: white;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: white;
        }
        
        .letterhead {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            margin-bottom: 15px;
        }
        
        .institution-name {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .institution-details {
            font-size: 11px;
            color: #333;
            margin-bottom: 3px;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: underline;
        }
        
        .receipt-number {
            font-size: 12px;
            margin-top: 10px;
            font-style: italic;
        }
        
        .content-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .info-label {
            width: 180px;
            font-weight: bold;
            color: #000;
        }
        
        .info-separator {
            width: 20px;
            text-align: center;
        }
        
        .info-value {
            color: #333;
        }
        
        .payment-details {
            border: 2px solid #000;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        
        .payment-table th {
            background: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        .payment-table .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 13px;
        }
        
        .amount-words {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 30px;
            text-align: center;
            background: #f9f9f9;
        }
        
        .amount-words-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .amount-words-text {
            font-style: italic;
            font-size: 13px;
        }
        
        .verification-info {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 11px;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .signature-box {
            text-align: center;
            width: 250px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .signature-location {
            margin-bottom: 60px;
            font-size: 11px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .signature-position {
            font-size: 10px;
            margin-top: 3px;
            font-style: italic;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        
        .official-stamp {
            position: absolute;
            right: 50px;
            top: 400px;
            width: 120px;
            height: 120px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
            .receipt-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Letterhead -->
        <div class="letterhead">
            @if(isset($institution['logo']))
                <img src="{{ $institution['logo'] }}" alt="Logo Institusi" class="logo">
            @endif
            <div class="institution-name">{{ $institution['name'] ?? 'Institusi Pendidikan Tinggi' }}</div>
            <div class="institution-details">{{ $institution['address'] ?? 'Alamat Lengkap Institusi' }}</div>
            <div class="institution-details">Telepon: {{ $institution['phone'] ?? '021-12345678' }} | Email: {{ $institution['email'] ?? 'info@institusi.ac.id' }}</div>
            @if(isset($institution['website']))
                <div class="institution-details">Website: {{ $institution['website'] }}</div>
            @endif
            <div class="receipt-title">Kwitansi Pembayaran</div>
            <div class="receipt-number">Nomor: {{ $receipt->receipt_number }}</div>
        </div>
        
        <!-- Student Information -->
        <div class="content-section">
            <div class="section-title">Data Mahasiswa</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Lengkap</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $mahasiswa->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Nomor Induk Mahasiswa</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $mahasiswa->numb_nim ?? 'N/A' }}</td>
                </tr>
                @if(isset($mahasiswa->program_studi))
                <tr>
                    <td class="info-label">Program Studi</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $mahasiswa->program_studi }}</td>
                </tr>
                @endif
                @if(isset($mahasiswa->fakultas))
                <tr>
                    <td class="info-label">Fakultas</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $mahasiswa->fakultas }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Transaction Information -->
        <div class="content-section">
            <div class="section-title">Informasi Transaksi</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Kode Kwitansi</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $receipt->receipt_code }}</td>
                </tr>
                <tr>
                    <td class="info-label">Kode Transaksi</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $transaction->transaction_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Tanggal Pembayaran</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">{{ $receipt->created_at->format('d F Y') }}</td>
                </tr>
                @if(isset($transaction->paid_at))
                <tr>
                    <td class="info-label">Waktu Verifikasi</td>
                    <td class="info-separator">:</td>
                    <td class="info-value">
                        @php
                            $paidAt = \Carbon\Carbon::parse($transaction->paid_at);
                            $hasTime = $paidAt->format('H:i:s') !== '00:00:00';
                            $formattedPaidAt = $paidAt->locale('id')->translatedFormat($hasTime ? 'd F Y H:i:s' : 'd F Y');
                        @endphp
                        {{ $formattedPaidAt }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Payment Details -->
        <div class="payment-details">
            <div class="section-title">Rincian Pembayaran</div>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th style="width: 50%">Keterangan</th>
                        <th style="width: 25%">Periode</th>
                        <th style="width: 25%">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $tagihan->name ?? $receipt->description ?? 'Pembayaran Akademik' }}</td>
                        <td>{{ $tagihan->semester ?? 'N/A' }} {{ $tagihan->tahun_akademik ?? '' }}</td>
                        <td class="amount">{{ number_format($transaction->amount ?? $receipt->amount, 0, ',', '.') }}</td>
                    </tr>
                    @if(isset($transaction->fee_amount) && $transaction->fee_amount > 0)
                    <tr>
                        <td>Biaya Administrasi</td>
                        <td>-</td>
                        <td class="amount">{{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="2"><strong>JUMLAH TOTAL PEMBAYARAN</strong></td>
                        <td class="amount"><strong>{{ number_format($transaction->total_amount ?? $receipt->amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-label">Terbilang:</div>
            <div class="amount-words-text">{{ $receipt->getAmountInWords() }}</div>
        </div>
        
        <!-- Verification Information -->
        <div class="verification-info">
            <strong>Informasi Verifikasi:</strong><br>
            Pembayaran ini telah diverifikasi dan dicatat dalam sistem keuangan institusi.
            @if(isset($transaction->paymentMethod))
            <br>Metode Pembayaran: {{ $transaction->paymentMethod->name }}
            @endif
            @if(isset($transaction->bankAccount))
            <br>Bank Tujuan: {{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}
            @endif
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Yang Menerima</div>
                <div class="signature-location">{{ $institution['address'] ?? 'Kota' }}, {{ $receipt->created_at->format('d F Y') }}</div>
                <div class="signature-line">
                    <strong>Kepala Bagian Keuangan</strong>
                    <div class="signature-position">NIP. _______________</div>
                </div>
            </div>
            
            <div class="signature-box">
                <div class="signature-title">Yang Membayar</div>
                <div class="signature-location">{{ $institution['address'] ?? 'Kota' }}, {{ $receipt->created_at->format('d F Y') }}</div>
                <div class="signature-line">
                    <strong>{{ $mahasiswa->name ?? 'Mahasiswa' }}</strong>
                    <div class="signature-position">NIM. {{ $mahasiswa->numb_nim ?? '_______________' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Official Stamp -->
        <div class="official-stamp">
            <div>
                <strong>STEMPEL<br>RESMI<br>INSTITUSI</strong>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>KWITANSI RESMI</strong></p>
            <p>Dokumen ini dicetak secara otomatis pada {{ $generated_at->format('d F Y H:i:s') }}</p>
            <p>Kwitansi ini sah dan dapat digunakan untuk keperluan administrasi</p>
            @if(!isset($is_preview) || !$is_preview)
            <p>Untuk verifikasi keaslian, hubungi Bagian Keuangan dengan kode: {{ $receipt->receipt_code }}</p>
            @endif
        </div>
    </div>
</body>
</html>
