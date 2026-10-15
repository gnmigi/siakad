<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\BobotNilaiSetting;
use App\Models\Akademik\GradeScaleSetting;

class BobotNilaiController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Setting Bobot Nilai";
        $data['pages'] = "Setting Bobot Nilai";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;

        // Get all bobot nilai settings
        $data['bobotNilai'] = BobotNilaiSetting::orderBy('order')->get();

        // Get all grade scale settings
        $data['gradeScales'] = GradeScaleSetting::orderBy('order')->get();

        // Available color classes for selection
        $data['colorClasses'] = [
            'text-primary' => 'Primary (Biru)',
            'text-info' => 'Info (Cyan)',
            'text-warning' => 'Warning (Kuning)',
            'text-success' => 'Success (Hijau)',
            'text-danger' => 'Danger (Merah)',
            'text-secondary' => 'Secondary (Abu-abu)',
            'text-dark' => 'Dark (Hitam)',
            'text-purple' => 'Purple (Ungu)',
            'text-pink' => 'Pink (Merah Muda)',
            'text-orange' => 'Orange (Oranye)',
        ];

        return view('master.akademik.bobot-nilai-index', $data, compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'components' => 'required|array',
                'components.*.id' => 'required|exists:bobot_nilai_settings,id',
                'components.*.component_name' => 'required|string|max:255',
                'components.*.weight' => 'required|numeric|min:0|max:100',
                'components.*.color_class' => 'required|string',
                'components.*.is_active' => 'nullable|in:true,false,1,0,on,off',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $components = $request->input('components');

            // Validate total weight equals 100% for active components
            $activeComponents = collect($components)->where('is_active', true);
            $totalWeight = $activeComponents->sum('weight');

            if (abs($totalWeight - 100) > 0.01) {
                return redirect()->back()
                    ->with('error', 'Total bobot nilai harus sama dengan 100%. Saat ini: ' . number_format($totalWeight, 2) . '%')
                    ->withInput();
            }

            // Update each component
            DB::beginTransaction();
            
            foreach ($components as $index => $component) {
                BobotNilaiSetting::where('id', $component['id'])->update([
                    'component_name' => $component['component_name'],
                    'weight' => $component['weight'],
                    'color_class' => $component['color_class'],
                    'is_active' => isset($component['is_active']) ? true : false,
                    'order' => $index + 1,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Setting bobot nilai berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui setting bobot nilai: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function resetToDefault()
    {
        try {
            DB::beginTransaction();

            // Reset to default values
            $defaultComponents = [
                [
                    'component_key' => 'tugas',
                    'component_name' => 'Tugas',
                    'weight' => 20.00,
                    'color_class' => 'text-primary',
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'component_key' => 'quiz',
                    'component_name' => 'Quiz',
                    'weight' => 20.00,
                    'color_class' => 'text-info',
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'component_key' => 'uts',
                    'component_name' => 'UTS',
                    'weight' => 30.00,
                    'color_class' => 'text-warning',
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'component_key' => 'uas',
                    'component_name' => 'UAS',
                    'weight' => 30.00,
                    'color_class' => 'text-success',
                    'order' => 4,
                    'is_active' => true,
                ],
            ];

            foreach ($defaultComponents as $component) {
                BobotNilaiSetting::where('component_key', $component['component_key'])
                    ->update([
                        'component_name' => $component['component_name'],
                        'weight' => $component['weight'],
                        'color_class' => $component['color_class'],
                        'order' => $component['order'],
                        'is_active' => $component['is_active'],
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Setting bobot nilai berhasil direset ke default');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal mereset setting bobot nilai: ' . $e->getMessage());
        }
    }

    public function updateGradeScales(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'grade_scales' => 'required|array',
                'grade_scales.*.id' => 'required|exists:grade_scale_settings,id',
                'grade_scales.*.grade_letter' => 'required|string|max:5',
                'grade_scales.*.min_score' => 'required|numeric|min:0|max:100',
                'grade_scales.*.max_score' => 'required|numeric|min:0|max:100',
                'grade_scales.*.grade_point' => 'required|numeric|min:0|max:4',
                'grade_scales.*.color_class' => 'required|string',
                'grade_scales.*.is_active' => 'nullable|in:true,false,1,0,on,off',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $gradeScales = $request->input('grade_scales');

            // Validate that ranges don't overlap
            $activeScales = collect($gradeScales)->where('is_active', true)->sortByDesc('min_score');
            
            // Check for overlaps
            for ($i = 0; $i < $activeScales->count() - 1; $i++) {
                $current = $activeScales->values()[$i];
                $next = $activeScales->values()[$i + 1];
                
                if ($current['min_score'] <= $next['max_score']) {
                    return redirect()->back()
                        ->with('error', 'Grade scale ranges cannot overlap. Please check the score ranges.')
                        ->withInput();
                }
            }

            // Update each grade scale
            DB::beginTransaction();
            
            foreach ($gradeScales as $index => $scale) {
                GradeScaleSetting::where('id', $scale['id'])->update([
                    'grade_letter' => $scale['grade_letter'],
                    'min_score' => $scale['min_score'],
                    'max_score' => $scale['max_score'],
                    'grade_point' => $scale['grade_point'],
                    'color_class' => $scale['color_class'],
                    'is_active' => isset($scale['is_active']) ? true : false,
                    'order' => $index + 1,
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Setting skala nilai berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui setting skala nilai: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function resetGradeScalesToDefault()
    {
        try {
            DB::beginTransaction();

            // Reset to default values
            $defaultScales = GradeScaleSetting::getDefaultGradeScales();

            foreach ($defaultScales as $scale) {
                GradeScaleSetting::where('grade_letter', $scale['grade_letter'])
                    ->update([
                        'min_score' => $scale['min_score'],
                        'max_score' => $scale['max_score'],
                        'grade_point' => $scale['grade_point'],
                        'color_class' => $scale['color_class'],
                        'order' => $scale['order'],
                        'is_active' => $scale['is_active'],
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Setting skala nilai berhasil direset ke default');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal mereset setting skala nilai: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get current bobot nilai settings for dosen interface
     */
    public function getSettings()
    {
        try {
            $settings = BobotNilaiSetting::getActiveComponents();
            $gradeScales = GradeScaleSetting::getActiveGradeScales();
            
            return response()->json([
                'success' => true,
                'settings' => $settings->map(function ($setting) {
                    return [
                        'key' => $setting->component_key,
                        'name' => $setting->component_name,
                        'weight' => $setting->weight,
                        'color_class' => $setting->color_class,
                        'order' => $setting->order,
                    ];
                }),
                'grade_scales' => $gradeScales->map(function ($scale) {
                    return [
                        'grade_letter' => $scale->grade_letter,
                        'min_score' => $scale->min_score,
                        'max_score' => $scale->max_score,
                        'grade_point' => $scale->grade_point,
                        'color_class' => $scale->color_class,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
