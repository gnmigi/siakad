<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get Web Settings for Mobile App
     */
    public function getSettings()
    {
        $settings = WebSetting::first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'app_name' => $settings->school_apps ?? 'SIAKAD MOBILE',
                'logo_vert' => $settings->school_logo_vert,
                'logo_hori' => $settings->school_logo_hori,
                'signin_bg' => $settings->signin_background_image,
                'hero_title' => $settings->hero_title,
                'hero_subtitle' => $settings->hero_subtitle,
                'copyright' => "© " . date('Y') . " - " . ($settings->school_name ?? 'Al-Amin Academic System'),
            ]
        ]);
    }
}
