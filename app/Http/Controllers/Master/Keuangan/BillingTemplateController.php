<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
// Use Models
use App\Models\Keuangan\BillingTemplate;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\TahunAkademik;
use App\Models\PMB\GelombangPendaftaran;
use App\Models\PMB\JalurPendaftaran;
use App\Models\Pengaturan\WebSetting;
// Use Services
use App\Services\SmartBillingService;

class BillingTemplateController extends Controller
{
    protected $smartBillingService;

    public function __construct(SmartBillingService $smartBillingService)
    {
        $this->smartBillingService = $smartBillingService;
    }

    /**
     * Display billing template management dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        
        // Ensure webs object has required properties
        if (!$data['webs']) {
            $data['webs'] = (object) [
                'school_apps' => 'SIAKAD',
                'school_name' => 'Sistem Informasi Akademik',
                'school_logo_hori' => asset('logo.png'),
            ];
        }
        // Note: No need to manually set school_logo_hori as it's handled by the model accessor
        
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Billing Templates";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get templates with statistics
        $data['templates'] = BillingTemplate::with(['createdBy', 'updatedBy'])
            ->withCount('tagihanKuliahGroups')
            ->latest()
            ->get();

        // Get overall statistics
        $data['stats'] = [
            'total_templates' => BillingTemplate::count(),
            'active_templates' => BillingTemplate::active()->count(),
            'auto_generate_templates' => BillingTemplate::autoGenerate()->count(),
            'draft_templates' => BillingTemplate::draft()->count(),
        ];

        // Get billing statistics
        $data['billing_stats'] = $this->smartBillingService->getBillingStatistics();

        // Get reference data for forms
        $data['programs'] = ProgramStudi::all();
        $data['classes'] = \App\Models\Akademik\Kelas::all();
        $data['academic_years'] = TahunAkademik::all();
        $data['gelombangs'] = GelombangPendaftaran::all();
        $data['jalurs'] = JalurPendaftaran::all();
        
        // Get dynamic semester range based on actual data
        $maxSemester = \App\Models\Mahasantri::max('semester') ?: 8;
        $data['maxSemesterLimit'] = max($maxSemester, 14); // Ensure at least 14 semesters available

        $data['user'] = $user;
        return view('master.keuangan.billing-template-index', $data);
    }

    /**
     * Store new billing template
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:semester,annual,one_time,monthly',
                'base_amount' => 'required|numeric|min:0',
                'due_date_formula' => 'required|string|in:semester_start + 30 days,semester_start + 45 days,now + 30 days,now + 60 days,no_due_date',
                'grace_period_days' => 'required|integer|min:0|max:365',
                'description' => 'nullable|string',
                'applicable_semesters' => 'nullable|array',
                'applicable_programs' => 'nullable|array',
                'applicable_classes' => 'nullable|array',
                'auto_generate' => 'boolean',
                'use_dynamic_naming' => 'boolean',
                'dynamic_naming_pattern' => 'nullable|string',
                'generation_trigger' => 'required|in:semester_start,manual,scheduled',
            ]);

            // Generate unique code
            $code = strtoupper(Str::slug($request->name, '-'));
            $originalCode = $code;
            $counter = 1;
            
            while (BillingTemplate::where('code', $code)->exists()) {
                $code = $originalCode . '-' . $counter;
                $counter++;
            }

            // Prepare amount formula
            $amountFormula = [
                'base_amount' => $request->base_amount,
                'modifiers' => []
            ];

            // Add modifiers if provided
            if ($request->has('modifiers') && is_array($request->modifiers)) {
                foreach ($request->modifiers as $modifier) {
                    if (!empty($modifier['condition']) && !empty($modifier['adjustment'])) {
                        $amountFormula['modifiers'][] = [
                            'condition' => $modifier['condition'],
                            'adjustment' => $modifier['adjustment']
                        ];
                    }
                }
            }

            // Prepare auto-generation rules
            $autoGenerateRules = [
                'trigger_on' => $request->generation_trigger,
                'target_students' => [
                    'active_only' => true,
                    'semester_range' => $request->applicable_semesters ?: null,
                    'program_ids' => $request->applicable_programs ?: null,
                    'class_ids' => $request->applicable_classes ?: null,
                ]
            ];

            $template = BillingTemplate::create([
                'name' => $request->name,
                'code' => $code,
                'slug' => Str::slug($request->name),
                'type' => $request->type,
                'amount_formula' => $amountFormula,
                'auto_generate_rules' => $autoGenerateRules,
                'applicable_semesters' => $request->applicable_semesters,
                'applicable_programs' => $request->applicable_programs,
                'applicable_classes' => $request->applicable_classes,
                'due_date_formula' => $request->due_date_formula,
                'grace_period_days' => $request->grace_period_days,
                'auto_generate' => $request->boolean('auto_generate'),
                'use_dynamic_naming' => $request->boolean('use_dynamic_naming'),
                'dynamic_naming_pattern' => $request->dynamic_naming_pattern,
                'generation_trigger' => $request->generation_trigger,
                'generation_schedule' => $request->generation_schedule,
                'status' => $request->status ?: BillingTemplate::STATUS_DRAFT,
                'description' => $request->description,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Template billing berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating billing template: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membuat template billing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update billing template
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $template = BillingTemplate::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:semester,annual,one_time,monthly',
                'base_amount' => 'required|numeric|min:0',
                'due_date_formula' => 'nullable|string|in:semester_start + 30 days,semester_start + 45 days,now + 30 days,now + 60 days,no_due_date',
                'grace_period_days' => 'required|integer|min:0|max:365',
                'description' => 'nullable|string',
                'applicable_semesters' => 'nullable|array',
                'applicable_programs' => 'nullable|array',
                'applicable_classes' => 'nullable|array',
                'auto_generate' => 'boolean',
                'use_dynamic_naming' => 'boolean',
                'dynamic_naming_pattern' => 'nullable|string',
                'status' => 'required|in:draft,active,inactive',
            ]);

            // Prepare amount formula
            $amountFormula = [
                'base_amount' => $request->base_amount,
                'modifiers' => []
            ];

            // Add modifiers if provided
            if ($request->has('modifiers') && is_array($request->modifiers)) {
                foreach ($request->modifiers as $modifier) {
                    if (!empty($modifier['condition']) && !empty($modifier['adjustment'])) {
                        $amountFormula['modifiers'][] = [
                            'condition' => $modifier['condition'],
                            'adjustment' => $modifier['adjustment']
                        ];
                    }
                }
            }

            // Prepare auto-generation rules
            $autoGenerateRules = [
                'trigger_on' => $request->generation_trigger ?: 'manual',
                'target_students' => [
                    'active_only' => true,
                    'semester_range' => $request->applicable_semesters ?: null,
                    'program_ids' => $request->applicable_programs ?: null,
                    'class_ids' => $request->applicable_classes ?: null,
                ]
            ];

            $template->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'type' => $request->type,
                'amount_formula' => $amountFormula,
                'auto_generate_rules' => $autoGenerateRules,
                'applicable_semesters' => $request->applicable_semesters,
                'applicable_programs' => $request->applicable_programs,
                'applicable_classes' => $request->applicable_classes,
                'due_date_formula' => $request->due_date_formula ?: 'now + 30 days',
                'grace_period_days' => $request->grace_period_days,
                'auto_generate' => $request->boolean('auto_generate'),
                'use_dynamic_naming' => $request->boolean('use_dynamic_naming'),
                'dynamic_naming_pattern' => $request->dynamic_naming_pattern,
                'generation_trigger' => $request->generation_trigger ?: 'manual',
                'generation_schedule' => $request->generation_schedule,
                'status' => $request->status,
                'description' => $request->description,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template billing berhasil diperbarui'
                ]);
            }

            return redirect()->back()->with('success', 'Template billing berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating billing template: ' . $e->getMessage());
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui template billing: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui template billing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete billing template
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $template = BillingTemplate::findOrFail($id);

            // Check if template has been used
            if ($template->tagihanKuliahGroups()->count() > 0) {
                DB::rollBack();
                
                // Check if request expects JSON (AJAX request)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Template tidak dapat dihapus karena sudah digunakan untuk generate tagihan'
                    ], 400);
                }
                
                return redirect()->back()->with('error', 'Template tidak dapat dihapus karena sudah digunakan untuk generate tagihan');
            }

            $template->delete();

            DB::commit();

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template billing berhasil dihapus'
                ]);
            }

            return redirect()->back()->with('success', 'Template billing berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting billing template: ' . $e->getMessage());
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus template billing: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal menghapus template billing: ' . $e->getMessage());
        }
    }

    /**
     * Preview billing generation
     */
    public function preview(Request $request, $id)
    {
        try {
            $template = BillingTemplate::findOrFail($id);

            $criteria = [];
            if ($request->has('semester')) {
                $criteria['semester'] = $request->semester;
            }

            $preview = $this->smartBillingService->previewBillingGeneration($id, $criteria);

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing billing generation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate billing from template
     */
    public function generate(Request $request, $id)
    {
        try {
            $template = BillingTemplate::findOrFail($id);

            $criteria = [];
            if ($request->has('semester')) {
                $criteria['semester'] = $request->semester;
            }

            $result = $this->smartBillingService->generateSpecificBilling($id, $criteria);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => "Berhasil generate {$result['created_count']} tagihan dari template {$template->name}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating billing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions for templates
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:activate,deactivate,delete,release_billing',
                'template_ids' => 'required|array|min:1',
                'template_ids.*' => 'exists:billing_templates,id'
            ]);

            DB::beginTransaction();

            $templateIds = $request->template_ids;
            $action = $request->action;
            $affectedCount = 0;

            switch ($action) {
                case 'activate':
                    $affectedCount = BillingTemplate::whereIn('id', $templateIds)
                        ->update(['status' => BillingTemplate::STATUS_ACTIVE]);
                    $message = "Berhasil mengaktifkan {$affectedCount} template";
                    break;

                case 'deactivate':
                    $affectedCount = BillingTemplate::whereIn('id', $templateIds)
                        ->update(['status' => BillingTemplate::STATUS_INACTIVE]);
                    $message = "Berhasil menonaktifkan {$affectedCount} template";
                    break;

                case 'delete':
                    // Check if any template has been used
                    $usedTemplates = BillingTemplate::whereIn('id', $templateIds)
                        ->whereHas('tagihanKuliahGroups')
                        ->count();

                    if ($usedTemplates > 0) {
                        // Check if request expects JSON (AJAX request)
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Beberapa template tidak dapat dihapus karena sudah digunakan'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Beberapa template tidak dapat dihapus karena sudah digunakan');
                    }

                    $affectedCount = BillingTemplate::whereIn('id', $templateIds)->delete();
                    $message = "Berhasil menghapus {$affectedCount} template";
                    break;

                case 'release_billing':
                    $totalGenerated = 0;
                    $successfulTemplates = 0;
                    $failedTemplates = [];

                    foreach ($templateIds as $templateId) {
                        try {
                            $template = BillingTemplate::findOrFail($templateId);
                            
                            // Check if template is active
                            if ($template->status !== BillingTemplate::STATUS_ACTIVE) {
                                $failedTemplates[] = $template->name . ' (tidak aktif)';
                                continue;
                            }

                            $criteria = [];
                            $result = $this->smartBillingService->generateSpecificBilling($templateId, $criteria);
                            
                            $totalGenerated += $result['created_count'];
                            $successfulTemplates++;
                            
                        } catch (\Exception $templateException) {
                            $template = BillingTemplate::find($templateId);
                            $templateName = $template ? $template->name : "Template ID {$templateId}";
                            $failedTemplates[] = $templateName . ' (' . $templateException->getMessage() . ')';
                            Log::error("Failed to generate billing for template {$templateId}: " . $templateException->getMessage());
                        }
                    }

                    if ($successfulTemplates > 0) {
                        $message = "Berhasil release billing untuk {$successfulTemplates} template dengan total {$totalGenerated} tagihan";
                        if (count($failedTemplates) > 0) {
                            $message .= ". Gagal: " . implode(', ', $failedTemplates);
                        }
                    } else {
                        $message = "Gagal release billing untuk semua template: " . implode(', ', $failedTemplates);
                    }
                    
                    $affectedCount = $successfulTemplates;
                    break;
            }

            DB::commit();

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'affected_count' => $affectedCount
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk action: ' . $e->getMessage());
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal melakukan bulk action: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal melakukan bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Get template details for modal
     */
    public function show($id)
    {
        try {
            $template = BillingTemplate::with(['createdBy', 'updatedBy'])
                ->withCount('tagihanKuliahGroups')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get form data for edit modal (programs and classes)
     */
    public function getFormData()
    {
        try {
            $data = [
                'programs' => ProgramStudi::select('id', 'name')->get(),
                'classes' => \App\Models\Akademik\Kelas::select('id', 'name')->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data form'
            ], 500);
        }
    }
}
