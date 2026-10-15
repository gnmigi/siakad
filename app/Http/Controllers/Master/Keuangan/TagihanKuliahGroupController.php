<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Use Models
use App\Models\Keuangan\TagihanKuliahGroup;
use App\Models\Keuangan\TagihanKuliah;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\PMB\GelombangPendaftaran;
use App\Models\PMB\JalurPendaftaran;
use App\Models\Akademik\TahunAkademik;
use App\Models\Mahasantri;
use App\Models\Pengaturan\WebSetting;
// Use Plugins

class TagihanKuliahGroupController extends Controller
{
    public function renderTagihanKuliahGroup()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Tagihan Kuliah Group";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['tahunAkademiks'] = TahunAkademik::all();
        $data['gelombangs'] = GelombangPendaftaran::all();
        $data['jalurs'] = JalurPendaftaran::all();
        $data['prodis'] = ProgramStudi::all();
        $data['kelas'] = Kelas::all();
        $data['groups'] = TagihanKuliahGroup::with(['prodi', 'kelas', 'gelombang', 'jalur', 'tahunAkademik'])
            ->latest()
            ->get();
        
        return view('master.keuangan.tagihan-kuliah-group-index', $data, compact('user'));
    }

    public function handleTagihanKuliahGroup(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'taka_id' => 'required|exists:tahun_akademiks,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'nullable|date',
                'no_due_date' => 'nullable|boolean',
                'desc' => 'nullable|string',
                'prodi_id' => 'nullable|exists:program_studis,id',
                'kelas_id' => 'nullable|exists:kelas,id',
                'gelombang_id' => 'nullable|exists:gelombang_pendaftarans,id',
                'jalur_id' => 'nullable|exists:jalur_pendaftarans,id',
                'semester' => 'nullable|integer|min:1|max:14',
            ]);

            // Handle no_due_date checkbox
            $dueDate = $request->no_due_date ? null : $request->due_date;

            // Generate unique code
            $code = 'TKG-' . Str::random(8);

            // Create new TagihanKuliahGroup
            $group = TagihanKuliahGroup::create([
                'name' => $request->name,
                'code' => $code,
                'slug' => Str::slug($request->name),
                'taka_id' => $request->taka_id,
                'amount' => $request->amount,
                'amount_per_student' => $request->amount, // Treat input as per-student amount
                'total_amount' => 0, // Will be calculated when published
                'expected_student_count' => 0, // Will be calculated when published
                'amount_type' => 'per_student',
                'due_date' => $dueDate,
                'desc' => $request->desc,
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
                'gelombang_id' => $request->gelombang_id,
                'jalur_id' => $request->jalur_id,
                'semester' => $request->semester,
                'status' => 'Draft',
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Group tagihan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Group tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateTagihanKuliahGroup(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'taka_id' => 'required|exists:tahun_akademiks,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'nullable|date',
                'no_due_date' => 'nullable|boolean',
                'desc' => 'nullable|string',
                'prodi_id' => 'nullable|exists:program_studis,id',
                'kelas_id' => 'nullable|exists:kelas,id',
                'gelombang_id' => 'nullable|exists:gelombang_pendaftarans,id',
                'jalur_id' => 'nullable|exists:jalur_pendaftarans,id',
                'semester' => 'nullable|integer|min:1|max:14',
                'status' => 'required|in:Draft,Published,Archived',
            ]);

            // Handle no_due_date checkbox
            $dueDate = $request->no_due_date ? null : $request->due_date;

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            $group->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'taka_id' => $request->taka_id,
                'amount' => $request->amount,
                'amount_per_student' => $request->amount, // Treat input as per-student amount
                'due_date' => $dueDate,
                'desc' => $request->desc,
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
                'gelombang_id' => $request->gelombang_id,
                'jalur_id' => $request->jalur_id,
                'semester' => $request->semester,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            // Jika status diubah ke Published, buat tagihan untuk setiap mahasiswa yang memenuhi kriteria
            if ($request->status === 'Published' && $group->tagihanKuliahs()->count() === 0) {
                $this->createTagihanForGroup($group);
                // Recalculate amounts after creating tagihan
                $group->recalculateAmounts();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Group tagihan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Group tagihan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteTagihanKuliahGroup($code)
    {
        try {
            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            // Hapus semua tagihan yang terkait dengan group ini
            TagihanKuliah::where('group_id', $group->id)->delete();
            
            $group->update([
                'deleted_by' => Auth::id()
            ]);
            $group->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Group tagihan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Group tagihan: ' . $e->getMessage());
        }
    }

    public function publishTagihanKuliahGroup($code)
    {
        try {
            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            // Update status group menjadi Published
            $group->update([
                'status' => 'Published',
                'updated_by' => Auth::id(),
            ]);

            // Buat tagihan untuk setiap mahasiswa yang memenuhi kriteria
            $this->createTagihanForGroup($group);
            
            // Recalculate amounts after creating tagihan
            $group->recalculateAmounts();

            DB::commit();
            return redirect()->back()->with('success', 'Group tagihan berhasil dipublish');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mempublish Group tagihan: ' . $e->getMessage());
        }
    }

    public function archiveTagihanKuliahGroup($code)
    {
        try {
            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            $group->update([
                'status' => 'Archived',
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Group tagihan berhasil diarsipkan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengarsipkan Group tagihan: ' . $e->getMessage());
        }
    }

    public function viewTagihanDetail($code)
    {
        try {
            $user = Auth::user();
            $data['webs'] = WebSetting::first();
            $data['spref'] = $user ? $user->prefix : '';
            $data['menus'] = "Master";
            $data['pages'] = "Detail Tagihan Kuliah Group";
            $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

            $group = TagihanKuliahGroup::with(['prodi', 'kelas', 'gelombang', 'jalur', 'tahunAkademik'])
                ->where('code', $code)
                ->firstOrFail();

            $data['group'] = $group;
            $data['tagihans'] = TagihanKuliah::with(['mahasiswa'])
                ->where('group_id', $group->id)
                ->latest()
                ->get();

            return view('master.keuangan.tagihan-kuliah-group-detail', $data, compact('user'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat detail tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete unpaid tagihan from a group
     */
    public function bulkDeleteTagihan(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            $request->validate([
                'tagihan_ids' => 'required|array|min:1',
                'tagihan_ids.*' => 'exists:tagihan_kuliahs,id'
            ]);

            // Check if any selected tagihan is already paid
            $paidTagihan = TagihanKuliah::whereIn('id', $request->tagihan_ids)
                ->whereIn('status', ['Paid', 'Sukses'])
                ->count();

            if ($paidTagihan > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus {$paidTagihan} tagihan yang sudah dibayar"
                ], 400);
            }

            // Delete only unpaid tagihan
            $deletedCount = TagihanKuliah::whereIn('id', $request->tagihan_ids)
                ->where('status', 'Pending')
                ->where('group_id', $group->id)
                ->delete();

            DB::commit();

            Log::info("Bulk deleted {$deletedCount} tagihan from group {$group->code}", [
                'group_id' => $group->id,
                'deleted_count' => $deletedCount,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} tagihan yang belum dibayar"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in bulk delete tagihan: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update amount for unpaid tagihan in a group
     */
    public function bulkUpdateAmount(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            $request->validate([
                'tagihan_ids' => 'required|array|min:1',
                'tagihan_ids.*' => 'exists:tagihan_kuliahs,id',
                'update_type' => 'required|in:fixed_amount,percentage_increase,percentage_decrease,add_amount,subtract_amount',
                'amount_value' => 'required|numeric|min:0'
            ]);

            // Check if any selected tagihan is already paid
            $paidTagihan = TagihanKuliah::whereIn('id', $request->tagihan_ids)
                ->whereIn('status', ['Paid', 'Sukses'])
                ->count();

            if ($paidTagihan > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat mengubah {$paidTagihan} tagihan yang sudah dibayar"
                ], 400);
            }

            // Get tagihan that will be updated
            $tagihans = TagihanKuliah::whereIn('id', $request->tagihan_ids)
                ->where('status', 'Pending')
                ->where('group_id', $group->id)
                ->get();

            $updatedCount = 0;

            foreach ($tagihans as $tagihan) {
                $newAmount = $this->calculateNewAmount($tagihan->amount, $request->update_type, $request->amount_value);
                
                $tagihan->update([
                    'amount' => $newAmount,
                    'updated_by' => Auth::id()
                ]);
                
                $updatedCount++;
            }

            DB::commit();

            Log::info("Bulk updated amount for {$updatedCount} tagihan in group {$group->code}", [
                'group_id' => $group->id,
                'update_type' => $request->update_type,
                'amount_value' => $request->amount_value,
                'updated_count' => $updatedCount,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah nominal {$updatedCount} tagihan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in bulk update amount: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah nominal tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recall/Withdraw a published group (delete all unpaid tagihan and set back to Draft)
     */
    public function recallGroup(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            if ($group->status !== 'Published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya group dengan status Published yang dapat di-recall'
                ], 400);
            }

            // Check if there are any paid tagihan
            $paidCount = TagihanKuliah::where('group_id', $group->id)
                ->whereIn('status', ['Paid', 'Sukses'])
                ->count();

            if ($paidCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat recall group karena ada {$paidCount} tagihan yang sudah dibayar"
                ], 400);
            }

            // Delete all unpaid tagihan
            $deletedCount = TagihanKuliah::where('group_id', $group->id)
                ->where('status', 'Pending')
                ->delete();

            // Set group back to Draft
            $group->update([
                'status' => 'Draft',
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            Log::info("Group recalled successfully", [
                'group_code' => $group->code,
                'group_id' => $group->id,
                'deleted_tagihan_count' => $deletedCount,
                'recalled_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Group berhasil di-recall. {$deletedCount} tagihan yang belum dibayar telah dihapus dan group dikembalikan ke status Draft"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in group recall: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan recall group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show bulk operations view
     */
    public function bulkOperationsView($code)
    {
        try {
            $user = Auth::user();
            $data['webs'] = WebSetting::first();
            $data['spref'] = $user ? $user->prefix : '';
            $data['menus'] = "Master";
            $data['pages'] = "Bulk Operations - Tagihan Kuliah Group";
            $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

            $group = TagihanKuliahGroup::with(['prodi', 'kelas', 'gelombang', 'jalur', 'tahunAkademik'])
                ->where('code', $code)
                ->firstOrFail();

            $data['group'] = $group;

            return view('master.keuangan.tagihan-kuliah-group-bulk-operations', $data, compact('user'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat halaman bulk operations: ' . $e->getMessage());
        }
    }

    /**
     * Get tagihan list for bulk operations
     */
    public function getGroupTagihan($code)
    {
        try {
            $group = TagihanKuliahGroup::where('code', $code)->firstOrFail();

            $tagihans = TagihanKuliah::with(['mahasiswa'])
                ->where('group_id', $group->id)
                ->get()
                ->map(function($tagihan) {
                    return [
                        'id' => $tagihan->id,
                        'code' => $tagihan->code,
                        'mahasiswa_name' => $tagihan->mahasiswa->name ?? 'N/A',
                        'mahasiswa_nim' => $tagihan->mahasiswa->numb_nim ?? 'N/A',
                        'amount' => $tagihan->amount,
                        'formatted_amount' => 'Rp ' . number_format($tagihan->amount, 0, ',', '.'),
                        'status' => $tagihan->status,
                        'due_date' => $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') : 'Tidak ada tempo',
                        'can_edit' => $tagihan->status === 'Pending',
                        'can_delete' => $tagihan->status === 'Pending'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'group' => [
                        'code' => $group->code,
                        'name' => $group->name,
                        'status' => $group->status
                    ],
                    'tagihans' => $tagihans,
                    'statistics' => [
                        'total_count' => $tagihans->count(),
                        'pending_count' => $tagihans->where('status', 'Pending')->count(),
                        'paid_count' => $tagihans->whereIn('status', ['Paid', 'Sukses'])->count(),
                        'total_amount' => $tagihans->sum('amount'),
                        'pending_amount' => $tagihans->where('status', 'Pending')->sum('amount')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate new amount based on update type
     */
    private function calculateNewAmount($currentAmount, $updateType, $value)
    {
        switch ($updateType) {
            case 'fixed_amount':
                return $value;
            
            case 'percentage_increase':
                return $currentAmount + ($currentAmount * $value / 100);
            
            case 'percentage_decrease':
                return max(0, $currentAmount - ($currentAmount * $value / 100));
            
            case 'add_amount':
                return $currentAmount + $value;
            
            case 'subtract_amount':
                return max(0, $currentAmount - $value);
            
            default:
                return $currentAmount;
        }
    }

    /**
     * Bulk update due date for selected tagihan
     */
    public function bulkUpdateDueDate(Request $request, $groupCode)
    {
        try {
            $request->validate([
                'tagihan_ids' => 'required|array|min:1',
                'tagihan_ids.*' => 'exists:tagihan_kuliahs,id',
                'due_date_type' => 'required|in:set_date,add_days,subtract_days,remove_due_date',
                'due_date_value' => 'nullable|date',
                'days_value' => 'nullable|integer|min:1|max:365'
            ]);

            DB::beginTransaction();

            $group = TagihanKuliahGroup::where('code', $groupCode)->firstOrFail();
            $tagihanIds = $request->tagihan_ids;
            $dueDateType = $request->due_date_type;
            $dueDateValue = $request->due_date_value;
            $daysValue = $request->days_value;

            // Get tagihan that belong to this group and are not paid
            $tagihans = TagihanKuliah::where('group_id', $group->id)
                ->whereIn('id', $tagihanIds)
                ->whereIn('status', ['Pending', 'Overdue', 'Grace Period'])
                ->get();

            if ($tagihans->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tagihan yang valid untuk diupdate'
                ], 400);
            }

            $updatedCount = 0;
            $errors = [];

            foreach ($tagihans as $tagihan) {
                try {
                    $newDueDate = null;

                    switch ($dueDateType) {
                        case 'set_date':
                            $newDueDate = $dueDateValue;
                            break;

                        case 'add_days':
                            if ($tagihan->due_date) {
                                $newDueDate = \Carbon\Carbon::parse($tagihan->due_date)->addDays($daysValue)->format('Y-m-d');
                            } else {
                                $newDueDate = \Carbon\Carbon::now()->addDays($daysValue)->format('Y-m-d');
                            }
                            break;

                        case 'subtract_days':
                            if ($tagihan->due_date) {
                                $newDueDate = \Carbon\Carbon::parse($tagihan->due_date)->subDays($daysValue)->format('Y-m-d');
                            } else {
                                // If no due date, subtract from today
                                $newDueDate = \Carbon\Carbon::now()->subDays($daysValue)->format('Y-m-d');
                            }
                            break;

                        case 'remove_due_date':
                            $newDueDate = null;
                            break;
                    }

                    // Update tagihan
                    $tagihan->update([
                        'due_date' => $newDueDate,
                        'updated_by' => Auth::id()
                    ]);

                    $updatedCount++;

                } catch (\Exception $e) {
                    $errors[] = [
                        'tagihan_id' => $tagihan->id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            Log::info('Bulk due date update completed', [
                'group_code' => $groupCode,
                'updated_count' => $updatedCount,
                'errors_count' => count($errors),
                'due_date_type' => $dueDateType,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah jatuh tempo {$updatedCount} tagihan",
                'updated_count' => $updatedCount,
                'errors_count' => count($errors),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk due date update: ' . $e->getMessage(), [
                'group_code' => $groupCode,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah jatuh tempo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk membuat tagihan berdasarkan kriteria group
     */
    private function createTagihanForGroup(TagihanKuliahGroup $group)
    {
        try {
            // Query untuk mencari mahasiswa yang memenuhi kriteria
            $query = Mahasantri::query();

            // Filter berdasarkan program studi
            if ($group->prodi_id) {
                $query->where('prodi_id', $group->prodi_id);
            }

            // Filter berdasarkan kelas
            if ($group->kelas_id) {
                $query->where('kelas_id', $group->kelas_id);
            }

            // Filter berdasarkan semester
            if ($group->semester) {
                $query->where('semester', $group->semester);
            }

            // Handle gelombang_id and jalur_id through Pendaftar model
            if ($group->gelombang_id || $group->jalur_id) {
                $pendaftarQuery = \App\Models\PMB\Pendaftar::query();
                
                if ($group->gelombang_id) {
                    $pendaftarQuery->where('gelombang_id', $group->gelombang_id);
                }
                
                if ($group->jalur_id) {
                    $pendaftarQuery->where('jalur_id', $group->jalur_id);
                }
                
                // Get mahasiswa IDs from pendaftar
                $mahasiswaIds = $pendaftarQuery->pluck('mahasiswa_id')->toArray();
                
                // Add to main query
                if (!empty($mahasiswaIds)) {
                    $query->whereIn('id', $mahasiswaIds);
                } else {
                    // Jika tidak ada mahasiswa yang memenuhi kriteria gelombang/jalur, return early
                    return;
                }
            }

            // Only get active students (type = 1 for active students)
            $query->where('type', 1);

            // Ambil semua mahasiswa yang memenuhi kriteria
            $mahasiswas = $query->get();

            $createdCount = 0;

            // Buat tagihan untuk setiap mahasiswa
            foreach ($mahasiswas as $mahasiswa) {
                // Check if tagihan already exists for this mahasiswa and group
                $existingTagihan = TagihanKuliah::where('mahasiswa_id', $mahasiswa->id)
                    ->where('group_id', $group->id)
                    ->first();

                if (!$existingTagihan) {
                    // Generate unique code
                    $code = 'TGH-' . str_pad($mahasiswa->id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));

                    TagihanKuliah::create([
                        'code' => $code,
                        'taka_id' => $group->taka_id,
                        'mahasiswa_id' => $mahasiswa->id,
                        'group_id' => $group->id,
                        'amount' => $group->getAmountPerStudent(), // Use the proper per-student amount
                        'due_date' => $group->due_date,
                        'status' => 'Pending',
                        'desc' => $group->desc ?: $group->name,
                        'created_by' => Auth::id(),
                    ]);

                    $createdCount++;
                }
            }

            // Log the result
            Log::info("Created {$createdCount} tagihan for group {$group->code} from {$mahasiswas->count()} eligible students");

        } catch (\Exception $e) {
            Log::error("Error creating tagihan for group {$group->code}: " . $e->getMessage());
            throw $e;
        }
    }
}
