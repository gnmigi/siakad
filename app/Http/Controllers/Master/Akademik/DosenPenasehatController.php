<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan\WebSetting;
use App\Models\Mahasantri;
use App\Models\Dosen;
use App\Models\Akademik\ProgramStudi;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class DosenPenasehatController extends Controller
{
    public function index(Request $request)
    {
        $data['webs'] = WebSetting::first();
        
        $routeParts = explode('.', request()->route()->getName());
        $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
        $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
            ? $routeParts[0]
            : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
        $data['spref'] = $prefix . '.';
        
        $data['menus'] = "Data Akademik";
        $data['pages'] = "Dosen Penasehat";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Build query with filters
        $query = Mahasantri::with(['dosenPenasehat', 'prodi', 'kelas'])
                           ->where('type', 1); // Only active students

        // Filter by program studi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter by status (dengan/tanpa penasehat)
        if ($request->filled('status')) {
            if ($request->status === 'dengan_penasehat') {
                $query->whereNotNull('dosen_penasehat_id');
            } elseif ($request->status === 'tanpa_penasehat') {
                $query->whereNull('dosen_penasehat_id');
            }
        }

            // Search by name or NIM
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('numb_nim', 'LIKE', "%{$search}%");
                });
            }

        // Get paginated results
        $data['mahasiswa'] = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get available dosen for assignment
        $data['dosens'] = Dosen::where('type', 1) // Only active dosen
                              ->orderBy('name')
                              ->get();

        // Get program studi for filtering
        $data['prodis'] = ProgramStudi::orderBy('name')->get();

        // Statistics (always show total stats, not filtered)
        $data['stats'] = [
            'total_mahasiswa' => Mahasantri::where('type', 1)->count(),
            'mahasiswa_dengan_penasehat' => Mahasantri::where('type', 1)->whereNotNull('dosen_penasehat_id')->count(),
            'mahasiswa_tanpa_penasehat' => Mahasantri::where('type', 1)->whereNull('dosen_penasehat_id')->count(),
            'total_dosen_penasehat' => Dosen::where('type', 1)->whereHas('mahasiswaBimbingan')->distinct()->count(),
        ];

        // Pass current filter values to view
        $data['current_filters'] = [
            'prodi_id' => $request->prodi_id,
            'status' => $request->status,
            'search' => $request->search,
        ];
        
        return view('master.akademik.dosen-penasehat-index', $data);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'dosen_id' => 'required|exists:dosens,id',
        ]);

        try {
            $mahasiswa = Mahasantri::findOrFail($request->mahasiswa_id);
            $dosen = Dosen::findOrFail($request->dosen_id);

            $mahasiswa->dosen_penasehat_id = $request->dosen_id;
            $mahasiswa->save();

            Alert::success('Berhasil', "Dosen penasehat {$dosen->name} berhasil ditugaskan untuk {$mahasiswa->name}");
            
            // Determine the correct redirect route based on current route
            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            return redirect()->route($prefix . '.akademik.dosen-penasehat-render');

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat menugaskan dosen penasehat: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        try {
            $mahasiswa = Mahasantri::findOrFail($request->mahasiswa_id);
            $mahasiswa->dosen_penasehat_id = null;
            $mahasiswa->save();

            Alert::success('Berhasil', "Dosen penasehat untuk {$mahasiswa->name} berhasil dihapus");
            
            // Determine the correct redirect route based on current route
            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            return redirect()->route($prefix . '.akademik.dosen-penasehat-render');

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat menghapus dosen penasehat: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array',
            'mahasiswa_ids.*' => 'exists:mahasiswas,id',
            'dosen_id' => 'required|exists:dosens,id',
        ]);

        try {
            $dosen = Dosen::findOrFail($request->dosen_id);
            $count = Mahasantri::whereIn('id', $request->mahasiswa_ids)
                               ->update(['dosen_penasehat_id' => $request->dosen_id]);

            Alert::success('Berhasil', "Dosen penasehat {$dosen->name} berhasil ditugaskan untuk {$count} mahasiswa");
            
            // Determine the correct redirect route based on current route
            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            return redirect()->route($prefix . '.akademik.dosen-penasehat-render');

        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat menugaskan dosen penasehat: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function searchAjax(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('AJAX Search Request', [
                'search' => $request->search,
                'prodi_id' => $request->prodi_id,
                'status' => $request->status
            ]);

            // Build query with filters
            $query = Mahasantri::with(['dosenPenasehat', 'prodi', 'kelas'])
                               ->where('type', 1); // Only active students

            // Filter by program studi
            if ($request->filled('prodi_id')) {
                $query->where('prodi_id', $request->prodi_id);
            }

            // Filter by status (dengan/tanpa penasehat)
            if ($request->filled('status')) {
                if ($request->status === 'dengan_penasehat') {
                    $query->whereNotNull('dosen_penasehat_id');
                } elseif ($request->status === 'tanpa_penasehat') {
                    $query->whereNull('dosen_penasehat_id');
                }
            }

            // Search by name or NIM
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('numb_nim', 'LIKE', "%{$search}%");
                });
            }

            // Get paginated results
            $mahasiswa = $query->orderBy('name')->paginate(20)->withQueryString();

            // Log query results
            Log::info('Query Results', [
                'total' => $mahasiswa->total(),
                'count' => $mahasiswa->count()
            ]);

            $routeParts = explode('.', request()->route()->getName());
            $validPrefixes = ['web-admin', 'akademik', 'finance', 'kemahasiswaan', 'it', 'library', 'umum', 'admisi'];
            $prefix = (!empty($routeParts[0]) && in_array($routeParts[0], $validPrefixes))
                ? $routeParts[0]
                : (auth()->user() ? str_replace('.', '', auth()->user()->prefix) : 'web-admin');
            $spref = $prefix . '.';

            // Handle pagination display text
            $firstItem = $mahasiswa->firstItem() ?: 0;
            $lastItem = $mahasiswa->lastItem() ?: 0;
            $total = $mahasiswa->total();
            
            $showingText = $total > 0 
                ? "Showing {$firstItem} to {$lastItem} of {$total} results"
                : "No results found";

            // Try to render the view
            try {
                $html = view('master.akademik.partials.dosen-penasehat-table', [
                    'mahasiswa' => $mahasiswa,
                    'spref' => $spref
                ])->render();
                
                $pagination = $mahasiswa->links()->render();
                
                Log::info('View rendered successfully');
                
            } catch (\Exception $viewException) {
                Log::error('View rendering failed', [
                    'error' => $viewException->getMessage(),
                    'trace' => $viewException->getTraceAsString()
                ]);
                throw $viewException;
            }

            // Return JSON response with HTML content
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination,
                'total' => $total,
                'showing' => $showingText
            ]);

        } catch (\Exception $e) {
            Log::error('AJAX Search Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data: ' . $e->getMessage()
            ], 500);
        }
    }
}
