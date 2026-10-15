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

class AnalisisPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $data['menus'] = 'Analisis Pembayaran';
        $data['pages'] = 'Dashboard Analisis';
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
        
        // Success Rate Analysis - using Saldo table
        $totalTransactions = Saldo::whereBetween('created_at', [$startDate, $endDate])->count();
        $successfulTransactions = Saldo::where('status', 'Sukses')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        
        $data['successRate'] = $totalTransactions > 0 ? 
            round(($successfulTransactions / $totalTransactions) * 100, 1) : 0;
        
        // Average Payment Time (days from tagihan created to payment) - using Saldo
        try {
            $avgPaymentTime = Saldo::select(DB::raw('AVG(DATEDIFF(saldos.created_at, tagihan_kuliahs.created_at)) as avg_days'))
                ->join('tagihan_kuliahs', 'saldos.tagihan_id', '=', 'tagihan_kuliahs.id')
                ->where('saldos.status', 'Sukses')
                ->where('saldos.type', 'Pemasukan')
                ->whereBetween('saldos.created_at', [$startDate, $endDate])
                ->first();
            
            $data['averagePaymentTime'] = $avgPaymentTime->avg_days ?? 0;
        } catch (\Exception $e) {
            $data['averagePaymentTime'] = 0;
        }
        
        // Peak Payment Hour - using Saldo
        $peakHour = Saldo::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->where('status', 'Sukses')
            ->where('type', 'Pemasukan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderByDesc('count')
            ->first();
        
        $data['peakPaymentHour'] = $peakHour->hour ?? 14;
        
        // Collection Efficiency - using Saldo
        try {
            $totalBilled = TagihanKuliah::whereBetween('created_at', [$startDate, $endDate])->sum('amount') ?? 0;
        } catch (\Exception $e) {
            $totalBilled = 0;
        }
        
        $totalCollected = Saldo::where('status', 'Sukses')
            ->where('type', 'Pemasukan')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('amount') ?? 0;
        
        // Store collection efficiency percentage for other uses
        $collectionEfficiencyPercentage = $totalBilled > 0 ? 
            round(($totalCollected / $totalBilled) * 100, 1) : 0;
        
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $monthlySummaries = Saldo::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as amount, COUNT(*) as count')
            ->where('status', 'Sukses')
            ->where('type', 'Pemasukan')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $trendData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $summary = $monthlySummaries->get($key);
            
            $amount = $summary ? $summary->amount : 0;
            $transactions = $summary ? $summary->count : 0;
            
            $trendData->push((object)[
                'month' => $month->translatedFormat('M'),
                'amount' => (float)$amount,
                'transactions' => (int)$transactions
            ]);
        }
        
        $data['trendData'] = $trendData;
        
        // Payment Method Analysis - Auto-detect payment methods from source field
        $paymentMethodsRaw = Saldo::select('source')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('AVG(amount) as avg_amount')
            ->selectRaw('MIN(created_at) as first_payment')
            ->selectRaw('MAX(created_at) as last_payment')
            ->where('status', 'Sukses')
            ->where('type', 'Pemasukan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('source')
            ->get();
        
        // Group by detected payment method
        $paymentMethodGroups = [];
        foreach ($paymentMethodsRaw as $item) {
            $methodName = $this->detectPaymentMethod($item->source);
            
            if (!isset($paymentMethodGroups[$methodName])) {
                $paymentMethodGroups[$methodName] = [
                    'transaction_count' => 0,
                    'total_amount' => 0,
                    'avg_amount' => 0,
                    'first_payment' => $item->first_payment,
                    'last_payment' => $item->last_payment,
                    'sources' => []
                ];
            }
            
            $paymentMethodGroups[$methodName]['transaction_count'] += $item->transaction_count;
            $paymentMethodGroups[$methodName]['total_amount'] += $item->total_amount;
            $paymentMethodGroups[$methodName]['sources'][] = $item->source;
            
            // Update date ranges
            if ($item->first_payment < $paymentMethodGroups[$methodName]['first_payment']) {
                $paymentMethodGroups[$methodName]['first_payment'] = $item->first_payment;
            }
            if ($item->last_payment > $paymentMethodGroups[$methodName]['last_payment']) {
                $paymentMethodGroups[$methodName]['last_payment'] = $item->last_payment;
            }
        }
        
        // Convert to collection with proper formatting
        $paymentMethodAnalysis = collect($paymentMethodGroups)->map(function($item, $methodName) use ($successfulTransactions) {
            $percentage = $successfulTransactions > 0 ? 
                round(($item['transaction_count'] / $successfulTransactions) * 100, 1) : 0;
            
            $avgAmount = $item['transaction_count'] > 0 ? 
                round($item['total_amount'] / $item['transaction_count'], 0) : 0;
            
            return (object)[
                'name' => $methodName,
                'total' => $item['total_amount'],
                'total_amount' => $item['total_amount'],
                'count' => $item['transaction_count'],
                'transaction_count' => $item['transaction_count'],
                'percentage' => $percentage,
                'avg_amount' => $avgAmount,
                'first_payment' => Carbon::parse($item['first_payment'])->format('Y-m-d'),
                'last_payment' => Carbon::parse($item['last_payment'])->format('Y-m-d'),
                'sources_count' => count($item['sources'])
            ];
        });
        
        $data['paymentMethodAnalysis'] = $paymentMethodAnalysis->isNotEmpty() ? $paymentMethodAnalysis : collect([
            (object)[
                'name' => 'Transfer Bank', 'total' => 0, 'total_amount' => 0, 'count' => 0, 
                'transaction_count' => 0, 'percentage' => 0, 'avg_amount' => 0,
                'first_payment' => Carbon::now()->format('Y-m-d'),
                'last_payment' => Carbon::now()->format('Y-m-d')
            ]
        ]);
        
        // Success Rate by Payment Method - Auto-detect and calculate for each method
        $successRateByMethod = [];
        foreach ($paymentMethodGroups as $methodName => $methodData) {
            // For Saldo table, all entries are already successful (status = 'Sukses')
            // So success rate is 100% for all methods in Saldo
            $successRateByMethod[] = (object)[
                'payment_method' => $methodName,
                'success_rate' => 100.0, // All Saldo entries are successful
                'successful_payments' => $methodData['transaction_count'],
                'total_attempts' => $methodData['transaction_count'],
                'total_amount' => $methodData['total_amount'],
                'avg_amount' => $methodData['transaction_count'] > 0 ? 
                    round($methodData['total_amount'] / $methodData['transaction_count'], 0) : 0
            ];
        }
        
        $data['successRateAnalysis'] = collect($successRateByMethod)->isNotEmpty() ? 
            collect($successRateByMethod) : collect([
                (object)[
                    'payment_method' => 'Pembayaran Manual', 
                    'success_rate' => 100, 
                    'successful_payments' => $successfulTransactions, 
                    'total_attempts' => $totalTransactions,
                    'total_amount' => $totalCollected,
                    'avg_amount' => $successfulTransactions > 0 ? round($totalCollected / $successfulTransactions, 0) : 0
                ]
            ]);
        
        // Student Payment Behavior - Real data from Saldo table grouped by student ID
        $studentBehaviorRaw = Saldo::join('tagihan_kuliahs', 'saldos.tagihan_id', '=', 'tagihan_kuliahs.id')
            ->join('mahasiswas', 'tagihan_kuliahs.mahasiswa_id', '=', 'mahasiswas.id')
            ->leftJoin('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
            ->select('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim', 'program_studis.name as prodi_name')
            ->selectRaw('SUM(saldos.amount) as total_paid')
            ->selectRaw('COUNT(saldos.id) as payment_frequency')
            ->selectRaw('AVG(saldos.amount) as avg_payment')
            ->selectRaw('DATEDIFF(MAX(saldos.created_at), MIN(saldos.created_at)) as payment_span_days')
            ->where('saldos.status', 'Sukses')
            ->where('saldos.type', 'Pemasukan')
            ->whereNotNull('saldos.tagihan_id')
            ->whereBetween('saldos.created_at', [$startDate, $endDate]);

        if ($takaId) {
            $studentBehaviorRaw->where('tagihan_kuliahs.taka_id', $takaId);
        }
        if ($prodiId) {
            $studentBehaviorRaw->where('mahasiswas.prodi_id', $prodiId);
        }

        $studentBehaviorRaw = $studentBehaviorRaw->groupBy('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim', 'program_studis.name')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();
        
        $studentBehavior = collect();
        foreach ($studentBehaviorRaw as $behavior) {
            $studentBehavior->push((object)[
                'numb_nim' => $behavior->numb_nim ?? '-',
                'name' => $behavior->name,
                'prodi_name' => $behavior->prodi_name ?? '-',
                'total_paid' => $behavior->total_paid,
                'payment_frequency' => $behavior->payment_frequency,
                'avg_payment' => $behavior->avg_payment,
                'payment_span_days' => $behavior->payment_span_days ?? 0
            ]);
        }
        
        // Fallback if no data
        if ($studentBehavior->isEmpty()) {
            $studentBehavior = collect([
                (object)['numb_nim' => '-', 'name' => 'Belum ada data', 'prodi_name' => '-', 'total_paid' => 0, 'payment_frequency' => 0, 'avg_payment' => 0, 'payment_span_days' => 0]
            ]);
        }
        
        $data['studentBehavior'] = $studentBehavior;
        
        // Outstanding Analysis by Program Studi - Real data from TagihanKuliah
        try {
            $outstandingTagihan = TagihanKuliah::where('status', '!=', 'Sukses')
                ->with('mahasiswa.prodi')
                ->get();
            
            $prodiGroups = [];
            foreach ($outstandingTagihan as $tagihan) {
                $prodiName = $tagihan->mahasiswa->prodi->name ?? 'Unknown';
                if (!isset($prodiGroups[$prodiName])) {
                    $prodiGroups[$prodiName] = [
                        'total_bills' => 0,
                        'total_amount' => 0,
                        'overdue_count' => 0,
                        'overdue_amount' => 0
                    ];
                }
                
                $prodiGroups[$prodiName]['total_bills']++;
                $prodiGroups[$prodiName]['total_amount'] += $tagihan->amount;
                
                // Check if overdue (created more than 30 days ago)
                if ($tagihan->created_at->diffInDays(Carbon::now()) > 30) {
                    $prodiGroups[$prodiName]['overdue_count']++;
                    $prodiGroups[$prodiName]['overdue_amount'] += $tagihan->amount;
                }
            }
            
            $outstandingAnalysis = collect($prodiGroups)->map(function($data, $prodiName) {
                return (object)[
                    'prodi_name' => $prodiName,
                    'total_bills' => $data['total_bills'],
                    'total_amount' => $data['total_amount'],
                    'overdue_count' => $data['overdue_count'],
                    'overdue_amount' => $data['overdue_amount']
                ];
            });
            
            $data['outstandingAnalysis'] = $outstandingAnalysis->isNotEmpty() ? $outstandingAnalysis : collect([
                (object)['prodi_name' => 'Semua tagihan sudah dibayar', 'total_bills' => 0, 'total_amount' => 0, 'overdue_count' => 0, 'overdue_amount' => 0]
            ]);
            
        } catch (\Exception $e) {
            $data['outstandingAnalysis'] = collect([
                (object)['prodi_name' => 'Error loading data', 'total_bills' => 0, 'total_amount' => 0, 'overdue_count' => 0, 'overdue_amount' => 0]
            ]);
        }
        
        // Collection Efficiency by Program Studi - Real data from database
        try {
            // Get all program studi with their students and payment data
            $collectionEfficiencyRaw = DB::table('program_studis')
                ->leftJoin('mahasiswas', 'program_studis.id', '=', 'mahasiswas.prodi_id')
                ->leftJoin('tagihan_kuliahs', 'mahasiswas.id', '=', 'tagihan_kuliahs.mahasiswa_id')
                ->leftJoin('saldos', function($join) use ($startDate, $endDate) {
                    $join->on('tagihan_kuliahs.id', '=', 'saldos.tagihan_id')
                         ->where('saldos.status', 'Sukses')
                         ->where('saldos.type', 'Pemasukan')
                         ->whereBetween('saldos.created_at', [$startDate, $endDate]);
                })
                ->select(
                    'program_studis.name as prodi_name',
                    DB::raw('COUNT(DISTINCT mahasiswas.id) as total_students'),
                    DB::raw('COUNT(DISTINCT CASE WHEN saldos.id IS NOT NULL THEN mahasiswas.id END) as paying_students'),
                    DB::raw('COALESCE(SUM(saldos.amount), 0) as collected_amount'),
                    DB::raw('COALESCE(SUM(tagihan_kuliahs.amount), 0) as total_billed_amount')
                )
                ->groupBy('program_studis.id', 'program_studis.name')
                ->havingRaw('COUNT(DISTINCT mahasiswas.id) > 0')
                ->get();
            
            $collectionEfficiency = collect();
            foreach ($collectionEfficiencyRaw as $efficiency) {
                $collectionRate = $efficiency->total_students > 0 ? 
                    round(($efficiency->paying_students / $efficiency->total_students) * 100, 1) : 0;
                
                $amountCollectionRate = $efficiency->total_billed_amount > 0 ? 
                    round(($efficiency->collected_amount / $efficiency->total_billed_amount) * 100, 1) : 0;
                
                $collectionEfficiency->push((object)[
                    'prodi_name' => $efficiency->prodi_name,
                    'total_students' => $efficiency->total_students,
                    'paying_students' => $efficiency->paying_students,
                    'collection_rate' => $collectionRate,
                    'collected_amount' => $efficiency->collected_amount,
                    'total_billed_amount' => $efficiency->total_billed_amount,
                    'amount_collection_rate' => $amountCollectionRate
                ]);
            }
            
            // Fallback if no data
            if ($collectionEfficiency->isEmpty()) {
                $collectionEfficiency = collect([
                    (object)[
                        'prodi_name' => 'Belum ada data program studi', 
                        'total_students' => 0, 
                        'paying_students' => 0, 
                        'collection_rate' => 0, 
                        'collected_amount' => 0, 
                        'total_billed_amount' => 0, 
                        'amount_collection_rate' => 0
                    ]
                ]);
            }
            
            $data['collectionEfficiency'] = $collectionEfficiency;
            
        } catch (\Exception $e) {
            // Fallback data in case of error
            $data['collectionEfficiency'] = collect([
                (object)[
                    'prodi_name' => 'Error loading data', 
                    'total_students' => 0, 
                    'paying_students' => 0, 
                    'collection_rate' => 0, 
                    'collected_amount' => 0, 
                    'total_billed_amount' => 0, 
                    'amount_collection_rate' => 0
                ]
            ]);
        }
        
        // Monthly Trend (detailed) - fallback data
        $data['monthlyTrend'] = collect([
            (object)['year' => Carbon::now()->year, 'month' => Carbon::now()->month, 'total_amount' => $totalCollected, 'transaction_count' => $successfulTransactions]
        ]);
        
        // Peak Times Analysis (hourly) - fallback data
        $data['peakTimesAnalysis'] = collect([
            (object)['hour' => 9, 'transaction_count' => 0],
            (object)['hour' => 14, 'transaction_count' => $successfulTransactions]
        ]);
        
        // Payment Timing Analysis - fallback data
        $data['paymentTimingAnalysis'] = collect([
            (object)[
                'name' => 'Pembayaran Tepat Waktu',
                'count' => $successfulTransactions,
                'percentage' => 100,
                'first_payment' => Carbon::parse($startDate)->format('Y-m-d'),
                'last_payment' => Carbon::parse($endDate)->format('Y-m-d'),
                'avg_days' => 2
            ],
            (object)[
                'name' => 'Pembayaran Terlambat',
                'count' => 0,
                'percentage' => 0,
                'first_payment' => Carbon::parse($startDate)->format('Y-m-d'),
                'last_payment' => Carbon::parse($endDate)->format('Y-m-d'),
                'avg_days' => 8
            ]
        ]);
        
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
            'prodi_id' => $prodiId,
            'type' => $request->get('type', 'summary')
        ];
        
        return view('master.keuangan.analisis-pembayaran-index', $data);
    }
    
    public function trends(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            
            // Daily trends (last 7 days) - using Saldo table
            $daily = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $amount = Saldo::where('status', 'Sukses')
                    ->where('type', 'Pemasukan')
                    ->whereDate('created_at', $date)
                    ->sum('amount') ?? 0;
                $daily[] = $amount;
            }
            
            // Weekly trends (last 4 weeks) - using Saldo table
            $weekly = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                $amount = Saldo::where('status', 'Sukses')
                    ->where('type', 'Pemasukan')
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('amount') ?? 0;
                $weekly[] = $amount;
            }
            
            // Monthly trends (last 5 months) - using Saldo table
            $monthly = [];
            for ($i = 4; $i >= 0; $i--) {
                $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
                $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
                $amount = Saldo::where('status', 'Sukses')
                    ->where('type', 'Pemasukan')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount') ?? 0;
                $monthly[] = $amount;
            }
            
            return response()->json([
                'success' => true,
                'trends' => [
                    'daily' => $daily,
                    'weekly' => $weekly,
                    'monthly' => $monthly
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trends data',
                'trends' => [
                    'daily' => [0, 0, 0, 0, 0, 0, 0],
                    'weekly' => [0, 0, 0, 0],
                    'monthly' => [0, 0, 0, 0, 0]
                ]
            ]);
        }
    }
    
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d'));
        $takaId = $request->get('taka_id');
        $prodiId = $request->get('prodi_id');
        $type = $request->get('type', 'summary');

        if ($type === 'behavior') {
            // Export student behavior analysis
            $studentBehaviorRaw = Saldo::join('tagihan_kuliahs', 'saldos.tagihan_id', '=', 'tagihan_kuliahs.id')
                ->join('mahasiswas', 'tagihan_kuliahs.mahasiswa_id', '=', 'mahasiswas.id')
                ->leftJoin('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
                ->select('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim', 'program_studis.name as prodi_name')
                ->selectRaw('SUM(saldos.amount) as total_paid')
                ->selectRaw('COUNT(saldos.id) as payment_frequency')
                ->selectRaw('AVG(saldos.amount) as avg_payment')
                ->selectRaw('DATEDIFF(MAX(saldos.created_at), MIN(saldos.created_at)) as payment_span_days')
                ->where('saldos.status', 'Sukses')
                ->where('saldos.type', 'Pemasukan')
                ->whereNotNull('saldos.tagihan_id')
                ->whereBetween('saldos.created_at', [$startDate, $endDate]);

            if ($takaId) {
                $studentBehaviorRaw->where('tagihan_kuliahs.taka_id', $takaId);
            }
            if ($prodiId) {
                $studentBehaviorRaw->where('mahasiswas.prodi_id', $prodiId);
            }

            $studentBehaviorRaw = $studentBehaviorRaw->groupBy('tagihan_kuliahs.mahasiswa_id', 'mahasiswas.name', 'mahasiswas.numb_nim', 'program_studis.name')
                ->orderByDesc('total_paid')
                ->get();

            $exportData = [];
            foreach ($studentBehaviorRaw as $student) {
                $behaviorScore = min(100, ($student->payment_frequency * 10) + ($student->payment_span_days > 0 ? (365 / max(1, $student->payment_span_days)) * 20 : 0));
                $exportData[] = [
                    'NIM' => $student->numb_nim ?? '-',
                    'Nama' => $student->name,
                    'Program Studi' => $student->prodi_name ?? '-',
                    'Total Pembayaran (Rp)' => (float)$student->total_paid,
                    'Frekuensi Transaksi' => $student->payment_frequency,
                    'Rata-rata Pembayaran (Rp)' => (float)$student->avg_payment,
                    'Rentang Waktu (Hari)' => $student->payment_span_days ?? 0,
                    'Behavior Score' => round($behaviorScore, 1)
                ];
            }

            return (new \Rap2hpoutre\FastExcel\FastExcel(collect($exportData)))
                ->download('Analisis_Perilaku_Mahasiswa_' . date('Ymd_His') . '.xlsx');

        } elseif ($type === 'trends') {
            // Export monthly trend numbers
            $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
            $monthlySummaries = Saldo::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as amount, COUNT(*) as count')
                ->where('status', 'Sukses')
                ->where('type', 'Pemasukan')
                ->where('created_at', '>=', $sixMonthsAgo)
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get()
                ->keyBy(function($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            $exportData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $key = $month->format('Y-m');
                $summary = $monthlySummaries->get($key);
                
                $amount = $summary ? $summary->amount : 0;
                $transactions = $summary ? $summary->count : 0;
                
                $exportData[] = [
                    'Bulan' => $month->translatedFormat('F Y'),
                    'Total Nominal (Rp)' => (float)$amount,
                    'Jumlah Transaksi' => $transactions
                ];
            }

            return (new \Rap2hpoutre\FastExcel\FastExcel(collect($exportData)))
                ->download('Analisis_Tren_Bulanan_' . date('Ymd_His') . '.xlsx');

        } else {
            // Export general summary metrics
            $totalTransactions = Saldo::whereBetween('created_at', [$startDate, $endDate])->count();
            $successfulTransactions = Saldo::where('status', 'Sukses')
                ->whereBetween('created_at', [$startDate, $endDate])->count();
            
            $successRate = $totalTransactions > 0 ? 
                round(($successfulTransactions / $totalTransactions) * 100, 1) : 0;

            // Average Payment Time
            try {
                $avgPaymentTime = Saldo::select(DB::raw('AVG(DATEDIFF(saldos.created_at, tagihan_kuliahs.created_at)) as avg_days'))
                    ->join('tagihan_kuliahs', 'saldos.tagihan_id', '=', 'tagihan_kuliahs.id')
                    ->where('saldos.status', 'Sukses')
                    ->where('saldos.type', 'Pemasukan')
                    ->whereBetween('saldos.created_at', [$startDate, $endDate])
                    ->first();
                $avgDays = $avgPaymentTime->avg_days ?? 0;
            } catch (\Exception $e) {
                $avgDays = 0;
            }

            // Peak Hour
            $peakHour = Saldo::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
                ->where('status', 'Sukses')
                ->where('type', 'Pemasukan')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderByDesc('count')
                ->first();
            $hour = $peakHour->hour ?? 14;

            $exportData = [
                ['Metrik Analisis' => 'Success Rate Transaksi', 'Nilai' => $successRate . '%'],
                ['Metrik Analisis' => 'Rata-rata Waktu Pembayaran', 'Nilai' => round($avgDays, 1) . ' Hari'],
                ['Metrik Analisis' => 'Jam Puncak Pembayaran', 'Nilai' => $hour . ':00'],
                ['Metrik Analisis' => 'Jumlah Transaksi Sukses', 'Nilai' => $successfulTransactions . ' Transaksi']
            ];

            return (new \Rap2hpoutre\FastExcel\FastExcel(collect($exportData)))
                ->download('Analisis_Ringkasan_Pembayaran_' . date('Ymd_His') . '.xlsx');
        }
    }
    
    /**
     * 🚀 AUTO-DETECT PAYMENT METHOD FROM SOURCE FIELD
     * This method automatically detects payment method from Saldo source field
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
