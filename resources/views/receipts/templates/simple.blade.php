<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $receipt->receipt_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
            background: white;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .institution-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        
        .receipt-info {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .payment-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        
        .payment-table .amount {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
        }
        
        .amount-words {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-style: italic;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="institution-name">{{ $institution['name'] ?? 'Institusi Pendidikan' }}</div>
            <div>{{ $institution['address'] ?? 'Alamat Institusi' }}</div>
            <div>Telp: {{ $institution['phone'] ?? '021-12345678' }}</div>
            <div class="receipt-title">Kwitansi Pembayaran</div>
        </div>
        
        <!-- Receipt Information -->
        <div class="receipt-info">
            <div class="info-row">
                <div class="info-label">Nomor Kwitansi</div>
                <div class="info-value">: {{ $receipt->receipt_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">: {{ $receipt->created_at->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Mahasiswa</div>
                <div class="info-value">: {{ $mahasiswa->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NIM</div>
                <div class="info-value">: {{ $mahasiswa->numb_nim ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kode Transaksi</div>
                <div class="info-value">: {{ $transaction->transaction_code ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Payment Details -->
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $tagihan->name ?? $receipt->description ?? 'Pembayaran Akademik' }}</td>
                    <td class="amount">Rp {{ number_format($transaction->amount ?? $receipt->amount, 0, ',', '.') }}</td>
                </tr>
                @if(isset($transaction->fee_amount) && $transaction->fee_amount > 0)
                <tr>
                    <td>Biaya Admin</td>
                    <td class="amount">Rp {{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="amount"><strong>Rp {{ number_format($transaction->total_amount ?? $receipt->amount, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Terbilang:</strong> {{ $receipt->getAmountInWords() }}
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div>Yang Menerima</div>
                <div class="signature-line">
                    <strong>Bagian Keuangan</strong>
                </div>
            </div>
            
            <div class="signature-box">
                <div>Yang Membayar</div>
                <div class="signature-line">
                    <strong>{{ $mahasiswa->name ?? 'Mahasiswa' }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ $generated_at->format('d F Y H:i:s') }}</p>
            <p>Kode Verifikasi: {{ $receipt->receipt_code }}</p>
        </div>
    </div>
</body>
</html>
