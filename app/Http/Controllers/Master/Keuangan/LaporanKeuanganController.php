<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Keuangan\Saldo;
use App\Models\Mahasantri;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\ProgramStudi;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $data['menus'] = 'Laporan Keuangan';
        $data['pages'] = 'Dashboard Laporan';
        $data['academy'] = config('app.name', 'SIAKAD');
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'finance');
        $data['spref'] = $prefix . '.';
        // Get real web settings to use consistent logo
        try {
            $data['webs'] = \App\Models\Pengaturan\WebSetting::first();
        } catch (\Exception $e) {
            // Fallback if WebSetting doesn't exist
            $data['webs'] = (object)[
                'school_name' => 'SIAKAD', 
                'school_apps' => 'Finance System',
                'school_logo_hori' => asset('logo.png')
            ];
        }
        
        // Get filter parameters - use wider default range to include existing data
        $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d'));
        $takaId = $request->get('taka_id');
        $prodiId = $request->get('prodi_id');
        
        // Real data from database - Using Saldo table (where the actual data exists)
        try {
            // Total Pemasukan (dari Saldo dengan type Pemasukan dan status Sukses)
            $totalPemasukanQuery = Saldo::where('type', 'Pemasukan')
                ->where('status', 'Sukses')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($takaId) {
                $totalPemasukanQuery->whereHas('tagihan', function($q) use ($takaId) {
                    $q->where('taka_id', $takaId);
                });
            }
            
            if ($prodiId) {
                $totalPemasukanQuery->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
            }
            
            $data['totalPemasukan'] = $totalPemasukanQuery->sum('amount') ?? 0;
            
            // Total Transaksi Pemasukan
            $data['totalTransaksi'] = $totalPemasukanQuery->count();
            
            // Rata-rata Transaksi
            $data['rataRataTransaksi'] = $data['totalTransaksi'] > 0 ? 
                round($data['totalPemasukan'] / $data['totalTransaksi'], 0) : 0;
            
            // Total Pending (dari Saldo dengan status Pending)
            $totalPendingQuery = Saldo::where('status', 'Pending')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($takaId) {
                $totalPendingQuery->whereHas('tagihan', function($q) use ($takaId) {
                    $q->where('taka_id', $takaId);
                });
            }
            
            if ($prodiId) {
                $totalPendingQuery->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
            }
            
            $data['totalPending'] = $totalPendingQuery->sum('amount') ?? 0;
            $data['jumlahTagihanPending'] = $totalPendingQuery->count();
            
            $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
            $dailySummaries = Saldo::selectRaw('DATE(created_at) as date, SUM(amount) as amount, COUNT(*) as count')
                ->where('type', 'Pemasukan')
                ->where('status', 'Sukses')
                ->where('created_at', '>=', $sevenDaysAgo)
                ->groupByRaw('DATE(created_at)')
                ->get()
                ->keyBy('date');
            
            $chartData = collect();
            for ($i = 6; $i >= 0; $i--) {
                $dateObj = Carbon::now()->subDays($i);
                $dateStr = $dateObj->format('Y-m-d');
                $summary = $dailySummaries->get($dateStr);
                
                $chartData->push((object)[
                    'date' => $dateStr,
                    'total' => $summary ? (float)$summary->amount : 0.0,
                    'count' => $summary ? (int)$summary->count : 0
                ]);
            }
            
            $data['chartData'] = $chartData;
            
            // Payment Methods Analysis - Auto-detect payment methods from source field
            $paymentMethodsRaw = Saldo::select('source')
                ->selectRaw('SUM(amount) as total')
                ->selectRaw('COUNT(*) as count')
                ->where('type', 'Pemasukan')
                ->where('status', 'Sukses')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('source')
                ->get();
            
            // Group by detected payment method (same logic as AnalisisPembayaranController)
            $paymentMethodGroups = [];
            foreach ($paymentMethodsRaw as $item) {
                $methodName = $this->detectPaymentMethod($item->source);
                
                if (!isset($paymentMethodGroups[$methodName])) {
                    $paymentMethodGroups[$methodName] = [
                        'total' => 0,
                        'count' => 0
                    ];
                }
                
                $paymentMethodGroups[$methodName]['total'] += $item->total;
                $paymentMethodGroups[$methodName]['count'] += $item->count;
            }
            
            // Convert to collection with proper formatting for JavaScript chart
            $paymentMethods = collect($paymentMethodGroups)->map(function($item, $methodName) {
                return (object)[
                    'name' => $methodName,
                    'total' => $item['total'],
                    'count' => $item['count']
                ];
            })->values(); // IMPORTANT: Use values() to convert to indexed array for JavaScript
            
            $data['paymentMethods'] = $paymentMethods->isNotEmpty() ? $paymentMethods : collect([
                (object)['name' => 'Belum ada data', 'total' => 0, 'count' => 0],
            ]);
            
            // Top Students (mahasiswa dengan pembayaran terbanyak) - dari Saldo yang terhubung ke tagihan
            $topStudents = collect();
            
            // Get students with payments from Saldo table grouped by student ID
            $studentPaymentsQuery = Saldo::join('tagihan_kuliahs', 'saldos.tagihan_id', '=', 'tagihan_kuliahs.id')
                ->join('mahasiswas', 'tagihan_kuliahs.mahasiswa_id', '=', 'mahasiswas.id')
                ->select('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim')
                ->selectRaw('SUM(saldos.amount) as total_paid')
                ->selectRaw('COUNT(saldos.id) as payment_count')
                ->where('saldos.type', 'Pemasukan')
                ->where('saldos.status', 'Sukses')
                ->whereNotNull('saldos.tagihan_id')
                ->whereBetween('saldos.created_at', [$startDate, $endDate]);

            if ($takaId) {
                $studentPaymentsQuery->where('tagihan_kuliahs.taka_id', $takaId);
            }
            if ($prodiId) {
                $studentPaymentsQuery->where('mahasiswas.prodi_id', $prodiId);
            }

            $studentPayments = $studentPaymentsQuery->groupBy('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim')
                ->orderByDesc('total_paid')
                ->limit(10)
                ->get();
            
            foreach ($studentPayments as $payment) {
                $topStudents->push((object)[
                    'name' => $payment->name,
                    'numb_nim' => $payment->numb_nim ?? '-',
                    'total_paid' => $payment->total_paid,
                    'payment_count' => $payment->payment_count
                ]);
            }
            
            if ($topStudents->isEmpty()) {
                $topStudents = collect([
                    (object)['name' => 'Belum ada data', 'numb_nim' => '-', 'total_paid' => 0, 'payment_count' => 0],
                ]);
            }
            
            $data['topStudents'] = $topStudents;
            
        } catch (\Exception $e) {
            // Fallback to zero values if database error
            $data['totalPemasukan'] = 0;
            $data['totalTransaksi'] = 0;
            $data['rataRataTransaksi'] = 0;
            $data['totalPending'] = 0;
            $data['jumlahTagihanPending'] = 0;
            $data['chartData'] = collect();
            $data['paymentMethods'] = collect([
                (object)['name' => 'Transfer Bank', 'total' => 0, 'count' => 0],
                (object)['name' => 'Cash', 'total' => 0, 'count' => 0],
            ]);
            $data['topStudents'] = collect([
                (object)['name' => 'Belum ada data', 'numb_nim' => '-', 'total_paid' => 0, 'payment_count' => 0],
            ]);
        }
        
        // Get real data for dropdowns
        try {
            $data['tahunAkademiks'] = TahunAkademik::orderBy('name', 'desc')->get();
            $data['programStudis'] = ProgramStudi::orderBy('name', 'asc')->get();
        } catch (\Exception $e) {
            // Fallback data
            $data['tahunAkademiks'] = collect([
                (object)['id' => 1, 'name' => '2024/2025 Ganjil'],
                (object)['id' => 2, 'name' => '2023/2024 Genap'],
            ]);
            $data['programStudis'] = collect([
                (object)['id' => 1, 'name' => 'Teknik Informatika'],
                (object)['id' => 2, 'name' => 'Sistem Informasi'],
            ]);
        }
        
        // Current filters
        $data['currentFilters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'taka_id' => $takaId,
            'prodi_id' => $prodiId
        ];
        
        return view('master.keuangan.laporan-keuangan-index', $data);
    }
    
    public function detail(Request $request)
    {
        $data['menus'] = 'Laporan Keuangan';
        $data['pages'] = 'Detail Transaksi';
        $data['academy'] = config('app.name', 'SIAKAD');
        
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'finance');
        $data['spref'] = $prefix . '.';
        
        try {
            $data['webs'] = \App\Models\Pengaturan\WebSetting::first();
        } catch (\Exception $e) {
            $data['webs'] = (object)[
                'school_name' => 'SIAKAD', 
                'school_apps' => 'Finance System',
                'school_logo_hori' => asset('logo.png')
            ];
        }

        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d'));
        $takaId = $request->get('taka_id');
        $prodiId = $request->get('prodi_id');
        $status = $request->get('status');
        $type = $request->get('type', 'Pemasukan');
        $search = $request->get('search');

        // Query transactions from Saldo
        $query = Saldo::with([
                'tagihan.mahasiswa.prodi',
                'createdBy'
            ])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            $query->where('status', $status);
        }

        if ($takaId) {
            $query->whereHas('tagihan', function($q) use ($takaId) {
                $q->where('taka_id', $takaId);
            });
        }

        if ($prodiId) {
            $query->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('transaction_code', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('desc', 'like', "%{$search}%")
                  ->orWhereHas('tagihan.mahasiswa', function($m) use ($search) {
                      $m->where('name', 'like', "%{$search}%")
                        ->orWhere('numb_nim', 'like', "%{$search}%");
                  });
            });
        }

        $data['transactions'] = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get filter dropdown options
        try {
            $data['tahunAkademiks'] = TahunAkademik::orderBy('name', 'desc')->get();
            $data['programStudis'] = ProgramStudi::orderBy('name', 'asc')->get();
        } catch (\Exception $e) {
            $data['tahunAkademiks'] = collect();
            $data['programStudis'] = collect();
        }

        $data['currentFilters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'taka_id' => $takaId,
            'prodi_id' => $prodiId,
            'status' => $status,
            'type' => $type,
            'search' => $search
        ];

        return view('master.keuangan.laporan-keuangan-detail', $data);
    }
    
    public function export(Request $request)
    {
        $exportFormat = $request->get('export_format');
        // Backward compatibility: check 'type' if export_format is not set
        if (!$exportFormat) {
            $typeParam = $request->get('type', 'summary');
            if (in_array($typeParam, ['summary', 'detail'])) {
                $exportFormat = $typeParam;
            } else {
                $exportFormat = 'summary'; // default fallback
            }
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d'));
        $takaId = $request->get('taka_id');
        $prodiId = $request->get('prodi_id');

        if ($exportFormat === 'detail') {
            $status = $request->get('status');
            $txType = $request->get('type');
            if (!in_array($txType, ['Pemasukan', 'Pengeluaran'])) {
                $txType = 'Pemasukan';
            }
            $search = $request->get('search');

            // Export detailed transactions with full filtering
            $query = Saldo::with([
                    'tagihan.mahasiswa.prodi',
                    'createdBy'
                ])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($txType) {
                $query->where('type', $txType);
            }
            
            if ($status) {
                $query->where('status', $status);
            }

            if ($takaId) {
                $query->whereHas('tagihan', function($q) use ($takaId) {
                    $q->where('taka_id', $takaId);
                });
            }

            if ($prodiId) {
                $query->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('transaction_code', 'like', "%{$search}%")
                      ->orWhere('source', 'like', "%{$search}%")
                      ->orWhere('desc', 'like', "%{$search}%")
                      ->orWhereHas('tagihan.mahasiswa', function($m) use ($search) {
                          $m->where('name', 'like', "%{$search}%")
                            ->orWhere('numb_nim', 'like', "%{$search}%");
                      });
                });
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            $exportData = [];
            foreach ($transactions as $tx) {
                $exportData[] = [
                    'Tanggal' => $tx->created_at->format('d/m/Y H:i'),
                    'Kode Transaksi' => $tx->code ?? $tx->transaction_code,
                    'NIM' => $tx->tagihan->mahasiswa->numb_nim ?? '-',
                    'Mahasantri' => $tx->tagihan->mahasiswa->name ?? '-',
                    'Program Studi' => $tx->tagihan->mahasiswa->prodi->name ?? '-',
                    'Deskripsi' => $tx->desc,
                    'Tipe' => $tx->type,
                    'Sumber / Metode' => $tx->source ?? '-',
                    'Nominal (Rp)' => (float)$tx->amount,
                    'Status' => $tx->status
                ];
            }

            return (new \Rap2hpoutre\FastExcel\FastExcel(collect($exportData)))
                ->download('Laporan_Keuangan_Detail_' . date('Ymd_His') . '.xlsx');
        } else {
            // Export summary report
            // 1. General Metrics
            $totalPemasukanQuery = Saldo::where('type', 'Pemasukan')
                ->where('status', 'Sukses')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($takaId) {
                $totalPemasukanQuery->whereHas('tagihan', function($q) use ($takaId) {
                    $q->where('taka_id', $takaId);
                });
            }
            if ($prodiId) {
                $totalPemasukanQuery->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
            }
            
            $totalPemasukan = $totalPemasukanQuery->sum('amount') ?? 0;
            $totalTransaksi = $totalPemasukanQuery->count();
            $rataRata = $totalTransaksi > 0 ? round($totalPemasukan / $totalTransaksi, 0) : 0;

            $totalPendingQuery = Saldo::where('status', 'Pending')
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($takaId) {
                $totalPendingQuery->whereHas('tagihan', function($q) use ($takaId) {
                    $q->where('taka_id', $takaId);
                });
            }
            if ($prodiId) {
                $totalPendingQuery->whereHas('tagihan.mahasiswa', function($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                });
            }
            $totalPending = $totalPendingQuery->sum('amount') ?? 0;

            $metrics = [
                ['Metrik Keuangan' => 'Total Pemasukan', 'Nilai' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.')],
                ['Metrik Keuangan' => 'Total Transaksi Sukses', 'Nilai' => $totalTransaksi . ' transaksi'],
                ['Metrik Keuangan' => 'Rata-rata Transaksi', 'Nilai' => 'Rp ' . number_format($rataRata, 0, ',', '.')],
                ['Metrik Keuangan' => 'Total Tagihan Pending', 'Nilai' => 'Rp ' . number_format($totalPending, 0, ',', '.')]
            ];

            return (new \Rap2hpoutre\FastExcel\FastExcel(collect($metrics)))
                ->download('Laporan_Keuangan_Ringkasan_' . date('Ymd_His') . '.xlsx');
        }
    }
    
    /**
     * 🚀 AUTO-DETECT PAYMENT METHOD FROM SOURCE FIELD
     * This method automatically detects payment method from Saldo source field
     * (Same logic as AnalisisPembayaranController for consistency)
     */
    private function detectPaymentMethod($source)
    {
        // Convert to lowercase for easier matching
        $sourceLower = strtolower($source);
        
        // Midtrans payments (from auto-sync)
        if (strpos($sourceLower, 'midtrans') !== false) {
            return 'Midtrans';
        }
        
        // Bank transfers
        if (strpos($sourceLower, 'transfer') !== false || 
            strpos($sourceLower, 'bank') !== false ||
            strpos($sourceLower, 'bca') !== false ||
            strpos($sourceLower, 'mandiri') !== false ||
            strpos($sourceLower, 'bni') !== false ||
            strpos($sourceLower, 'bri') !== false) {
            return 'Transfer Bank';
        }
        
        // E-wallets
        if (strpos($sourceLower, 'gopay') !== false ||
            strpos($sourceLower, 'ovo') !== false ||
            strpos($sourceLower, 'dana') !== false ||
            strpos($sourceLower, 'shopeepay') !== false ||
            strpos($sourceLower, 'linkaja') !== false ||
            strpos($sourceLower, 'e-wallet') !== false ||
            strpos($sourceLower, 'ewallet') !== false) {
            return 'E-Wallet';
        }
        
        // Cash payments
        if (strpos($sourceLower, 'cash') !== false ||
            strpos($sourceLower, 'tunai') !== false ||
            strpos($sourceLower, 'kas') !== false) {
            return 'Cash';
        }
        
        // Credit card
        if (strpos($sourceLower, 'credit') !== false ||
            strpos($sourceLower, 'kartu kredit') !== false ||
            strpos($sourceLower, 'visa') !== false ||
            strpos($sourceLower, 'mastercard') !== false) {
            return 'Kartu Kredit';
        }
        
        // Virtual Account
        if (strpos($sourceLower, 'virtual') !== false ||
            strpos($sourceLower, 'va') !== false) {
            return 'Virtual Account';
        }
        
        // QRIS
        if (strpos($sourceLower, 'qris') !== false ||
            strpos($sourceLower, 'qr') !== false) {
            return 'QRIS';
        }
        
        // Online banking
        if (strpos($sourceLower, 'internet banking') !== false ||
            strpos($sourceLower, 'mobile banking') !== false ||
            strpos($sourceLower, 'klikbca') !== false ||
            strpos($sourceLower, 'klikpay') !== false) {
            return 'Internet Banking';
        }
        
        // Retail payments
        if (strpos($sourceLower, 'indomaret') !== false ||
            strpos($sourceLower, 'alfamart') !== false ||
            strpos($sourceLower, 'retail') !== false) {
            return 'Retail';
        }
        
        // Default to manual payment for anything else
        return 'Pembayaran Manual';
    }
}
