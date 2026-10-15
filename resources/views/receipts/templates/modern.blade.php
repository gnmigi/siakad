<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt - {{ $receipt->receipt_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #2c3e50;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            margin-bottom: 15px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .institution-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .institution-tagline {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 30px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,184,148,0.3);
        }
        
        .status-badge::before {
            content: '✓';
            margin-right: 6px;
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #667eea;
        }
        
        .info-card h3 {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 500;
            color: #6c757d;
            font-size: 11px;
        }
        
        .info-value {
            font-weight: 600;
            color: #2c3e50;
            text-align: right;
        }
        
        .payment-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        
        .section-title {
            color: #495057;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .section-title::before {
            content: '💳';
            margin-right: 10px;
            font-size: 18px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .payment-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .payment-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .payment-table .amount {
            text-align: right;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        .total-row {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            font-weight: 700;
        }
        
        .total-row td {
            border-bottom: none;
        }
        
        .amount-words {
            background: linear-gradient(135deg, #fdcb6e, #e17055);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(253,203,110,0.3);
        }
        
        .amount-words-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        
        .amount-words-text {
            font-size: 14px;
            font-weight: 700;
            font-style: italic;
        }
        
        .verification-section {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .verification-section h3 {
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .verification-section h3::before {
            content: '🔒';
            margin-right: 8px;
        }
        
        .qr-code {
            float: right;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
            margin-left: 15px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        
        .signature-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 2px dashed #dee2e6;
        }
        
        .signature-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .signature-date {
            font-size: 10px;
            color: #6c757d;
            margin-bottom: 40px;
        }
        
        .signature-name {
            font-weight: 700;
            color: #2c3e50;
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 40px;
        }
        
        .signature-position {
            font-size: 10px;
            color: #6c757d;
            margin-top: 5px;
            font-style: italic;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            text-align: center;
            font-size: 10px;
        }
        
        .footer-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #74b9ff;
        }
        
        .footer p {
            margin-bottom: 4px;
            opacity: 0.8;
        }
        
        .digital-signature {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 10px;
            font-size: 10px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .signatures {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="digital-signature">
                <div>🔐 DIGITAL</div>
                <div>VERIFIED</div>
            </div>
            <div class="header-content">
                @if(isset($institution['logo']))
                    <img src="{{ $institution['logo'] }}" alt="Logo" class="logo">
                @endif
                <div class="institution-name">{{ $institution['name'] ?? 'Digital University' }}</div>
                <div class="institution-tagline">Excellence in Digital Education</div>
                <div class="receipt-title">Digital Receipt</div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Status -->
            <div class="status-badge">
                Payment Verified
            </div>
            
            <!-- Information Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>Student Information</h3>
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value">{{ $mahasiswa->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Student ID</span>
                        <span class="info-value">{{ $mahasiswa->numb_nim ?? 'N/A' }}</span>
                    </div>
                    @if(isset($mahasiswa->program_studi))
                    <div class="info-item">
                        <span class="info-label">Study Program</span>
                        <span class="info-value">{{ $mahasiswa->program_studi }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="info-card">
                    <h3>Receipt Information</h3>
                    <div class="info-item">
                        <span class="info-label">Receipt Number</span>
                        <span class="info-value">{{ $receipt->receipt_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Receipt Code</span>
                        <span class="info-value">{{ $receipt->receipt_code }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date Issued</span>
                        <span class="info-value">{{ $receipt->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Transaction ID</span>
                        <span class="info-value">{{ $transaction->transaction_code ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Details -->
            <div class="payment-section">
                <div class="section-title">Payment Details</div>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th style="width: 50%">Description</th>
                            <th style="width: 25%">Period</th>
                            <th style="width: 25%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $tagihan->name ?? $receipt->description ?? 'Academic Payment' }}</td>
                            <td>{{ $tagihan->semester ?? 'N/A' }} {{ $tagihan->tahun_akademik ?? '' }}</td>
                            <td class="amount">Rp {{ number_format($transaction->amount ?? $receipt->amount, 0, ',', '.') }}</td>
                        </tr>
                        @if(isset($transaction->fee_amount) && $transaction->fee_amount > 0)
                        <tr>
                            <td>Administration Fee</td>
                            <td>-</td>
                            <td class="amount">Rp {{ number_format($transaction->fee_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td><strong>TOTAL PAYMENT</strong></td>
                            <td></td>
                            <td class="amount"><strong>Rp {{ number_format($transaction->total_amount ?? $receipt->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Amount in Words -->
            <div class="amount-words">
                <div class="amount-words-label">Amount in Words</div>
                <div class="amount-words-text">{{ $receipt->getAmountInWords() }}</div>
            </div>
            
            <!-- Verification Section -->
            <div class="verification-section">
                <div class="qr-code">
                    <div>QR CODE<br>{{ $receipt->receipt_code }}</div>
                </div>
                <h3>Digital Verification</h3>
                <p>This payment has been digitally verified and recorded in our secure system.</p>
                @if(isset($transaction->paymentMethod))
                <p><strong>Payment Method:</strong> {{ $transaction->paymentMethod->name }}</p>
                @endif
                @if(isset($transaction->paid_at))
                <p><strong>Verified At:</strong> {{ \Carbon\Carbon::parse($transaction->paid_at)->format('d F Y H:i:s') }}</p>
                @endif
                <div style="clear: both;"></div>
            </div>
            
            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-card">
                    <div class="signature-title">Authorized By</div>
                    <div class="signature-date">{{ $institution['address'] ?? 'City' }}, {{ $receipt->created_at->format('d F Y') }}</div>
                    <div class="signature-name">Finance Department</div>
                    <div class="signature-position">Digital Signature Applied</div>
                </div>
                
                <div class="signature-card">
                    <div class="signature-title">Paid By</div>
                    <div class="signature-date">{{ $institution['address'] ?? 'City' }}, {{ $receipt->created_at->format('d F Y') }}</div>
                    <div class="signature-name">{{ $mahasiswa->name ?? 'Student' }}</div>
                    <div class="signature-position">ID: {{ $mahasiswa->numb_nim ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">🌐 DIGITAL RECEIPT SYSTEM</div>
            <p>Generated automatically on {{ $generated_at->format('d F Y H:i:s') }}</p>
            <p>This digital receipt is valid and can be used for administrative purposes</p>
            @if(!isset($is_preview) || !$is_preview)
            <p>Verification Code: {{ $receipt->receipt_code }} | Visit our website for verification</p>
            @endif
            <p>{{ $institution['website'] ?? 'www.university.ac.id' }} | {{ $institution['email'] ?? 'info@university.ac.id' }}</p>
        </div>
    </div>
</body>
</html>
