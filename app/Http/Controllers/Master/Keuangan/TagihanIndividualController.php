<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// Use Models
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Keuangan\PaymentMethod;
use App\Models\Keuangan\BankAccount;
use App\Models\Mahasantri;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\Pengaturan\WebSetting;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanIndividualExport;

class TagihanIndividualController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Tagihan Individual";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        // Get statistics
        $data['totalTagihan'] = TagihanKuliah::count();
        $data['pendingCount'] = TagihanKuliah::whereIn('status', ['Pending', 'Cicilan'])->count();
        $data['paidCount'] = TagihanKuliah::whereIn('status', ['Paid', 'Sukses'])->count();
        $data['totalPending'] = TagihanKuliah::whereIn('status', ['Pending', 'Cicilan'])
            ->sum(DB::raw('COALESCE(remaining_amount, amount)'));
        
        // Build query for mahasiswa with filters
        $query = Mahasantri::select('mahasiswas.*')
            ->with(['prodi', 'kelas.taka'])
            ->withCount([
                'tagihanKuliah as total_tagihan',
                'tagihanKuliah as pending_tagihan' => function($query) {
                    $query->whereIn('status', ['Pending', 'Cicilan']);
                },
                'tagihanKuliah as paid_tagihan' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ])
            ->withSum([
                'tagihanKuliah as total_amount' => function($query) {
                    $query->whereIn('status', ['Pending', 'Cicilan']);
                },
                'tagihanKuliah as paid_amount' => function($query) {
                    $query->whereIn('status', ['Paid', 'Sukses']);
                }
            ], 'amount');
        
        // Check if any filter is active
        $hasFilters = $request->filled('search') || 
                      $request->filled('prodi_filter') || 
                      $request->filled('kelas_filter') || 
                      $request->filled('semester_filter') || 
                      $request->filled('status_filter');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('numb_nim', 'LIKE', '%' . $searchTerm . '%');
            });
        } elseif (!$hasFilters) {
            // Default: Only show mahasiswa who have tagihan when no filters are set
            $query->whereHas('tagihanKuliah');
        }
        
        // Apply other filters
        if ($request->filled('prodi_filter')) {
            $query->where('prodi_id', $request->prodi_filter);
        }
        
        if ($request->filled('kelas_filter')) {
            $query->where('kelas_id', $request->kelas_filter);
        }
        
        if ($request->filled('semester_filter')) {
            $query->where('semester', $request->semester_filter);
        }
        
        // Status filter
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'Pending') {
                $query->whereHas('tagihanKuliah', function($q) {
                    $q->whereIn('status', ['Pending', 'Cicilan']);
                });
            } elseif ($request->status_filter === 'Cicilan') {
                $query->whereHas('tagihanKuliah', function($q) {
                    $q->where('status', 'Cicilan');
                });
            } elseif ($request->status_filter === 'Paid') {
                $query->whereHas('tagihanKuliah', function($q) {
                    $q->whereIn('status', ['Paid', 'Sukses']);
                })->whereDoesntHave('tagihanKuliah', function($q) {
                    $q->whereIn('status', ['Pending', 'Cicilan']);
                });
            } elseif ($request->status_filter === 'no_tagihan') {
                $query->whereDoesntHave('tagihanKuliah');
            } elseif ($request->status_filter === 'has_tagihan') {
                $query->whereHas('tagihanKuliah');
            }
        }
        
        // Sorting functionality
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Validate sort parameters
        $allowedSortFields = ['name', 'numb_nim', 'total_tagihan', 'pending_tagihan', 'paid_tagihan', 'total_amount'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        // Apply sorting
        if (in_array($sortBy, ['total_tagihan', 'pending_tagihan', 'paid_tagihan', 'total_amount'])) {
            // For aggregate columns, we need to order by the raw column
            $query->orderBy($sortBy, $sortDirection);
        } else {
            // For regular columns
            if ($sortBy === 'prodi') {
                $query->leftJoin('program_studis', 'mahasiswas.prodi_id', '=', 'program_studis.id')
                      ->orderBy('program_studis.name', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }
        
        // Add secondary sort by name if not already sorting by name
        if ($sortBy !== 'name') {
            $query->orderBy('name', 'asc');
        }
        
        $data['mahasiswas'] = $query->paginate(20);
        
        // Append all parameters to pagination links
        $data['mahasiswas']->appends($request->query());
        
        // Get filter data
        $data['allMahasiswas'] = Mahasantri::select('id', 'numb_nim', 'name')->orderBy('name')->get();
        $data['tahunAkademiks'] = TahunAkademik::all();
        $data['programStudis'] = ProgramStudi::all();
        $data['kelasList'] = Kelas::with('taka')->get();
        
        // Get unique semester values from mahasiswas table
        $data['semesterList'] = Mahasantri::select('semester')
            ->distinct()
            ->whereNotNull('semester')
            ->where('semester', '>', 0)
            ->orderBy('semester')
            ->get()
            ->map(function($item) {
                return (object)[
                    'id' => $item->semester,
                    'name' => 'Semester ' . $item->semester
                ];
            });
        
        // Current filter values
        $data['currentFilters'] = [
            'search' => $request->search,
            'prodi_filter' => $request->prodi_filter,
            'kelas_filter' => $request->kelas_filter,
            'semester_filter' => $request->semester_filter,
            'status_filter' => $request->status_filter,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ];
        
        // Get payment methods and bank accounts for modal
        $data['paymentMethods'] = PaymentMethod::where('is_active', true)->get();
        $data['bankAccounts'] = BankAccount::where('is_active', true)->get();
        
        return view('master.keuangan.tagihan-individual-index', $data, compact('user'));
    }

    public function getMahasiswaTagihan($mahasiswaId)
    {
        try {
            $tagihans = TagihanKuliah::with(['tahunAkademik', 'group'])
                ->where('mahasiswa_id', $mahasiswaId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'tagihans' => $tagihans->map(function($tagihan) {
                    $amount = (float) ($tagihan->amount ?? 0);
                    $paidAmount = (float) ($tagihan->paid_amount ?? 0);
                    $remainingAmount = $tagihan->remaining_amount !== null
                        ? (float) $tagihan->remaining_amount
                        : max($amount - $paidAmount, 0);

                    return [
                        'id' => $tagihan->id,
                        'code' => $tagihan->code,
                        'desc' => $tagihan->desc,
                        'amount' => $amount,
                        'paid_amount' => $paidAmount,
                        'remaining_amount' => $remainingAmount,
                        'formatted_amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
                        'formatted_remaining_amount' => 'Rp ' . number_format($remainingAmount, 0, ',', '.'),
                        'due_date' => $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') : 'Tidak ada tempo',
                        'status' => $tagihan->status,
                        'status_label' => $this->getStatusLabel($tagihan->status),
                        'tahun_akademik' => $tagihan->tahunAkademik->name ?? '-',
                        'created_at' => $tagihan->created_at->format('d/m/Y H:i')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat tagihan: ' . $e->getMessage()
            ]);
        }
    }

    private function getStatusLabel($status)
    {
        switch ($status) {
            case 'Pending':
                return 'Pending';
            case 'Cicilan':
                return 'Cicilan';
            case 'Paid':
            case 'Sukses':
                return 'Lunas';
            case 'Cancelled':
                return 'Dibatalkan';
            default:
                return $status;
        }
    }

    public function viewMahasiswaTagihan($mahasiswaId)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Detail Tagihan Mahasiswa";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        $data['mahasiswa'] = Mahasantri::with(['prodi', 'kelas'])->findOrFail($mahasiswaId);
        $data['tagihans'] = TagihanKuliah::with(['tahunAkademik', 'group'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest()
            ->get();
        $data['tahunAkademiks'] = TahunAkademik::all();
        
        return view('master.keuangan.tagihan-mahasiswa-detail', $data, compact('user'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'mahasiswa_id' => 'required|exists:mahasiswas,id',
                'taka_id' => 'required|exists:tahun_akademiks,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'desc' => 'required|string|max:255',
            ]);

            // Generate unique code
            $code = 'TGH-' . strtoupper(Str::random(6));

            TagihanKuliah::create([
                'code' => $code,
                'mahasiswa_id' => $request->mahasiswa_id,
                'taka_id' => $request->taka_id,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'desc' => $request->desc,
                'status' => 'Pending',
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan individual berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Validation rules - due_date is nullable if no_due_date is checked
            $validationRules = [
                'mahasiswa_id' => 'required|exists:mahasiswas,id',
                'taka_id' => 'required|exists:tahun_akademiks,id',
                'amount' => 'required|numeric|min:0',
                'desc' => 'required|string|max:255',
                'status' => 'required|in:Pending,Paid,Sukses,Cancelled',
                'no_due_date' => 'nullable|boolean',
            ];

            // Add due_date validation only if no_due_date is not checked
            if (!$request->has('no_due_date') || !$request->no_due_date) {
                $validationRules['due_date'] = 'required|date';
            } else {
                $validationRules['due_date'] = 'nullable|date';
            }

            $request->validate($validationRules);

            $tagihan = TagihanKuliah::findOrFail($id);

            // Determine due_date value
            $dueDate = null;
            if ($request->has('no_due_date') && $request->no_due_date) {
                // If "no due date" is checked, set due_date to null
                $dueDate = null;
            } else {
                // Otherwise use the provided due_date
                $dueDate = $request->due_date;
            }

            $tagihan->update([
                'mahasiswa_id' => $request->mahasiswa_id,
                'taka_id' => $request->taka_id,
                'amount' => $request->amount,
                'due_date' => $dueDate,
                'desc' => $request->desc,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            
            $message = 'Tagihan berhasil diperbarui';
            if ($dueDate === null) {
                $message .= ' (tanpa jatuh tempo)';
            }
            
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $tagihan = TagihanKuliah::findOrFail($id);
            
            // Only allow deletion if not paid
            if ($tagihan->status === 'Paid' || $tagihan->status === 'Sukses') {
                return redirect()->back()->with('error', 'Tidak dapat menghapus tagihan yang sudah dibayar');
            }

            $tagihan->update(['deleted_by' => Auth::id()]);
            $tagihan->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();

            $tagihan = TagihanKuliah::findOrFail($id);
            
            $tagihan->update([
                'status' => 'Paid',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tagihan berhasil ditandai sebagai lunas');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status tagihan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            // Get current filters from request
            $filters = [
                'search' => $request->get('search'),
                'prodi_filter' => $request->get('prodi_filter'),
                'kelas_filter' => $request->get('kelas_filter'),
                'semester_filter' => $request->get('semester_filter'),
                'status_filter' => $request->get('status_filter'),
            ];

            // Get export type (default to summary)
            $exportType = $request->get('type', 'summary');

            // Generate filename based on export type and current date
            $filename = 'tagihan-individual-' . $exportType . '-' . date('Y-m-d-H-i-s') . '.xlsx';

            // Clear output buffer to prevent corrupted Excel file warning
            if (ob_get_length()) ob_end_clean();
            ob_start();
            
            // Create and download the export
            return Excel::download(new TagihanIndividualExport($filters, $exportType), $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}
