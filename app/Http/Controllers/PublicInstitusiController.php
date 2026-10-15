<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Institusi\InstitutionPage;
use App\Models\Publikasi\Galeri;
use App\Models\Pengaturan\WebSetting;

class PublicInstitusiController extends Controller
{
    public function profile()
    {
        $data['webs'] = WebSetting::first();
        $data['page'] = InstitutionPage::where('page_type', 'profile')
            ->where('is_active', true)
            ->first();
        
        return view('public.institusi.profile', $data);
    }

    public function visionMission()
    {
        $data['webs'] = WebSetting::first();
        $data['page'] = InstitutionPage::where('page_type', 'vision_mission')
            ->where('is_active', true)
            ->first();
        
        return view('public.institusi.vision-mission', $data);
    }

    public function structure()
    {
        $data['webs'] = WebSetting::first();
        $data['page'] = InstitutionPage::where('page_type', 'structure')
            ->where('is_active', true)
            ->first();
        
        return view('public.institusi.structure', $data);
    }

    public function facilities()
    {
        $data['webs'] = WebSetting::first();
        $data['page'] = InstitutionPage::where('page_type', 'facilities')
            ->where('is_active', true)
            ->first();
        
        return view('public.institusi.facilities', $data);
    }

    public function gallery()
    {
        $data['webs'] = WebSetting::first();
        $data['galleries'] = Galeri::where('status', 'Publish')
            ->with('kategori', 'fotos')
            ->latest()
            ->paginate(12);
        
        return view('public.institusi.gallery', $data);
    }
}
