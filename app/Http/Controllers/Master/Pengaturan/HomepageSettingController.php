<?php

namespace App\Http\Controllers\Master\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Pengaturan\WebSetting;

class HomepageSettingController extends Controller
{
    public function renderHomepageSettings()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Pengaturan";
        $data['pages'] = "Homepage Settings";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        
        return view('master.pengaturan.homepage-setting-index', $data, compact('user'));
    }

    public function updateHomepageSettings(Request $request)
    {
        try {
            $webs = WebSetting::first();
            $section = $request->input('section');

            switch ($section) {
                case 'hero':
                    $request->validate([
                        'hero_title' => 'required|string|max:255',
                        'hero_subtitle' => 'required|string',
                        'hero_background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                    ]);

                    $webs->hero_title = $request->hero_title;
                    $webs->hero_subtitle = $request->hero_subtitle;

                    if ($request->hasFile('hero_background_image')) {
                        // Delete old image
                        if ($webs->hero_background_image) {
                            Storage::disk('public')->delete('images/homepage/' . $webs->hero_background_image);
                        }

                        // Save new image
                        $imageName = time() . '_hero_bg.' . $request->hero_background_image->getClientOriginalExtension();
                        $request->hero_background_image->storeAs('images/homepage', $imageName, 'public');
                        $webs->hero_background_image = $imageName;
                    }
                    break;

                case 'statistics':
                    $webs->stat_programs_count = $request->stat_programs_count;
                    $webs->stat_programs_label = $request->stat_programs_label;
                    $webs->stat_students_count = $request->stat_students_count;
                    $webs->stat_students_label = $request->stat_students_label;
                    $webs->stat_lecturers_count = $request->stat_lecturers_count;
                    $webs->stat_lecturers_label = $request->stat_lecturers_label;
                    $webs->stat_partners_count = $request->stat_partners_count;
                    $webs->stat_partners_label = $request->stat_partners_label;
                    break;

                case 'calendar':
                    $webs->academic_calendar = json_encode($request->calendar);
                    break;

                case 'features':
                    $webs->features_section = json_encode($request->features);
                    break;

                case 'programs':
                    $programs = $request->programs;
                    
                    // Handle image uploads for programs
                    foreach ($programs as $index => $program) {
                        if ($request->hasFile("programs.{$index}.image")) {
                            $imageName = time() . "_program_{$index}." . $request->file("programs.{$index}.image")->getClientOriginalExtension();
                            $request->file("programs.{$index}.image")->storeAs('images/homepage', $imageName, 'public');
                            $programs[$index]['image'] = $imageName;
                        }
                    }
                    
                    $webs->programs_section = json_encode($programs);
                    break;

                case 'news':
                    $request->validate([
                        'news_main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                    ]);

                    $webs->news_main_title = $request->news_main_title;
                    $webs->news_main_content = $request->news_main_content;
                    $webs->news_main_category = $request->news_main_category;
                    $webs->news_main_date = $request->news_main_date;

                    if ($request->hasFile('news_main_image')) {
                        // Delete old image
                        if ($webs->news_main_image) {
                            Storage::disk('public')->delete('images/homepage/' . $webs->news_main_image);
                        }

                        // Save new image
                        $imageName = time() . '_news_main.' . $request->news_main_image->getClientOriginalExtension();
                        $request->news_main_image->storeAs('images/homepage', $imageName, 'public');
                        $webs->news_main_image = $imageName;
                    }
                    break;

                case 'gallery':
                    $gallery = $request->gallery;
                    
                    // Handle image uploads for gallery
                    foreach ($gallery as $index => $item) {
                        if ($request->hasFile("gallery.{$index}.image")) {
                            $imageName = time() . "_gallery_{$index}." . $request->file("gallery.{$index}.image")->getClientOriginalExtension();
                            $request->file("gallery.{$index}.image")->storeAs('images/homepage', $imageName, 'public');
                            $gallery[$index]['image'] = $imageName;
                        }
                    }
                    
                    $webs->gallery_section = json_encode($gallery);
                    break;

                case 'faq':
                    $webs->faq_section = json_encode($request->faq);
                    break;

                case 'cta':
                    $webs->cta_title = $request->cta_title;
                    $webs->cta_subtitle = $request->cta_subtitle;
                    $webs->cta_button_text = $request->cta_button_text;
                    $webs->cta_button_link = $request->cta_button_link;
                    break;
            }

            $webs->save();

            Alert::success('Berhasil', 'Pengaturan homepage berhasil diperbarui');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
