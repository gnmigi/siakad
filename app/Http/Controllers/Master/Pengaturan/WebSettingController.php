<?php

namespace App\Http\Controllers\Master\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
// USE MODELS
use App\Models\Pengaturan\WebSetting;
// USE PLUGINS
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RealRashid\SweetAlert\Facades\Alert;

class WebSettingController extends Controller
{
    public function renderIndex()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Web Settings";
        $data['academy'] = $data['webs']->school_apps . '' . $data['webs']->school_name;
        
        return view('master.pengaturan.web-setting-index', $data, compact('user'));
    }

    public function handleSettings(Request $request)
    {
        try {
            $isFinance = (Auth::user() && Auth::user()->raw_type === 2);

            if ($isFinance) {
                // Validate only WhatsApp Admin Notification Settings
                $validator = Validator::make($request->all(), [
                    'wa_admin_payment_notification_enabled' => 'nullable|in:0,1',
                    'wa_admin_krs_notification_enabled' => 'nullable|in:0,1',
                    'wa_admin_numbers' => 'nullable',
                    'wa_finance_numbers' => 'nullable',
                ]);
            } else {
                // Validate the request
                $validator = Validator::make($request->all(), [
                    // Identity
                    'school_apps' => 'required|string|max:255',
                    'school_name' => 'required|string|max:255',
                    'school_head' => 'required|string|max:255',
                    'school_link' => 'required|url|max:255',
                    'school_desc' => 'required|string',
                    'school_logo_vert' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'school_logo_hori' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'signin_background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10120',
                    'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                    'signature_title' => 'nullable|string|max:255',
                    'signature_name' => 'nullable|string|max:255',

                    // Contact
                    'school_email' => 'nullable|email|max:255',
                    'school_phone' => 'nullable|string|max:20',
                    'school_address' => 'nullable|string',
                    'school_longitude' => 'nullable|string|max:20',
                    'school_latitude' => 'nullable|string|max:20',

                    // Social Media
                    'social_fb' => 'nullable|url|max:255',
                    'social_ig' => 'nullable|url|max:255',
                    'social_in' => 'nullable|url|max:255',
                    'social_tw' => 'nullable|url|max:255',

                    // System
                    'taka_now' => 'nullable|integer',
                    'maintenance_mode' => 'nullable|in:0,1',
                    'enable_captcha' => 'nullable|in:0,1',
                    'max_login_attempts' => 'required|integer|min:1|max:10',

                    // Email Verification
                    'email_verification_enabled' => 'nullable|in:0,1',
                    'verification_code_length' => 'nullable|integer|min:4|max:8',
                    'verification_code_expiry_minutes' => 'nullable|integer|min:5|max:60',
                    'max_verification_attempts' => 'nullable|integer|min:1|max:10',
                    'require_email_verification_pmb' => 'nullable|in:0,1',

                    // WhatsApp Admin Notification Settings
                    'wa_admin_payment_notification_enabled' => 'nullable|in:0,1',
                    'wa_admin_krs_notification_enabled' => 'nullable|in:0,1',
                    'wa_admin_numbers' => 'nullable',
                    'wa_finance_numbers' => 'nullable',
                ]);
            }

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $settings = WebSetting::first();
            $data = $validator->validated();

            if ($isFinance) {
                // Handle WhatsApp admin notification settings
                $data['wa_admin_payment_notification_enabled'] = $request->has('wa_admin_payment_notification_enabled') ? true : false;
                $data['wa_admin_krs_notification_enabled'] = $request->has('wa_admin_krs_notification_enabled') ? true : false;
                
                $adminNumbers = $request->input('wa_admin_numbers');
                $financeNumbers = $request->input('wa_finance_numbers');

                if (is_array($adminNumbers)) {
                    $adminNumbers = array_filter(array_map('trim', $adminNumbers));
                    $data['wa_admin_numbers'] = implode(',', $adminNumbers);
                } else {
                    $data['wa_admin_numbers'] = $adminNumbers;
                }

                if (is_array($financeNumbers)) {
                    $financeNumbers = array_filter(array_map('trim', $financeNumbers));
                    $data['wa_finance_numbers'] = implode(',', $financeNumbers);
                } else {
                    $data['wa_finance_numbers'] = $financeNumbers;
                }

                $settings->update([
                    'wa_admin_payment_notification_enabled' => $data['wa_admin_payment_notification_enabled'],
                    'wa_admin_krs_notification_enabled' => $data['wa_admin_krs_notification_enabled'],
                    'wa_admin_numbers' => $data['wa_admin_numbers'],
                    'wa_finance_numbers' => $data['wa_finance_numbers'],
                ]);

                Alert::success('Success', 'WhatsApp settings updated successfully');
                return redirect()->back();
            }

            // Handle vertical logo upload
            if ($request->hasFile('school_logo_vert')) {
                // Delete old logo if exists
                if ($settings->school_logo_vert && $settings->school_logo_vert !== 'logo-vert.png') {
                    Storage::disk('public')->delete('images/logo/' . $settings->school_logo_vert);
                }
            
                // Get original file extension to preserve format
                $originalExtension = $request->school_logo_vert->getClientOriginalExtension();
                $logoVertName = 'logo-vert-' . time() . '.' . $originalExtension;
                
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($request->school_logo_vert->getRealPath());
                
                // Resize if too large
                if ($image->height() > 800) {
                    $image->scaleDown(height: 800); 
                }
                
                // Save in original format to preserve transparency
                if (strtolower($originalExtension) === 'png') {
                    // Save as PNG to preserve transparency
                    Storage::disk('public')->put('images/logo/' . $logoVertName, $image->toPng());
                } else {
                    // Save as JPEG for other formats
                    Storage::disk('public')->put('images/logo/' . $logoVertName, $image->toJpeg(90));
                }
                
                $data['school_logo_vert'] = $logoVertName;
            }

            // Handle horizontal logo upload
            if ($request->hasFile('school_logo_hori')) {
                // Delete old logo if exists
                if ($settings->school_logo_hori && $settings->school_logo_hori !== 'logo-hori.png') {
                    Storage::disk('public')->delete('images/logo/' . $settings->school_logo_hori);
                }
            
                // Get original file extension to preserve format
                $originalExtension = $request->school_logo_hori->getClientOriginalExtension();
                $logoHoriName = 'logo-hori-' . time() . '.' . $originalExtension;
                
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($request->school_logo_hori->getRealPath());
                
                // Resize if too large
                if ($image->height() > 800) {
                    $image->scaleDown(height: 800); 
                }
                
                // Save in original format to preserve transparency
                if (strtolower($originalExtension) === 'png') {
                    // Save as PNG to preserve transparency
                    Storage::disk('public')->put('images/logo/' . $logoHoriName, $image->toPng());
                } else {
                    // Save as JPEG for other formats
                    Storage::disk('public')->put('images/logo/' . $logoHoriName, $image->toJpeg(90));
                }
                
                $data['school_logo_hori'] = $logoHoriName;
            }

            // Handle signin background image upload
            if ($request->hasFile('signin_background_image')) {
                // Delete old signin background if exists
                if ($settings->signin_background_image) {
                    Storage::disk('public')->delete('images/signin/' . $settings->signin_background_image);
                }
            
                // Get original file extension to preserve format
                $originalExtension = $request->signin_background_image->getClientOriginalExtension();
                $signinBgName = 'signin-bg-' . time() . '.' . $originalExtension;
                
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($request->signin_background_image->getRealPath());
                
                // Resize if too large (signin background should be larger for better quality)
                if ($image->width() > 1920) {
                    $image->scaleDown(width: 1920); 
                }
                
                // Create directory if not exists
                if (!Storage::disk('public')->exists('images/signin')) {
                    Storage::disk('public')->makeDirectory('images/signin');
                }
                
                // Save as JPEG for better compression (signin backgrounds are usually photos)
                Storage::disk('public')->put('images/signin/' . $signinBgName, $image->toJpeg(85));
                
                $data['signin_background_image'] = $signinBgName;
            }

            // Handle kop surat upload
            if ($request->hasFile('kop_surat')) {
                // Delete old kop surat if exists
                if ($settings->kop_surat) {
                    Storage::disk('public')->delete('images/kop-surat/' . $settings->kop_surat);
                }
            
                // Get original file extension to preserve format
                $originalExtension = $request->kop_surat->getClientOriginalExtension();
                $kopSuratName = 'kop-surat-' . time() . '.' . $originalExtension;
                
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($request->kop_surat->getRealPath());
                
                // Resize if too large (kop surat biasanya lebih besar)
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200); 
                }
                
                // Create directory if not exists
                if (!Storage::disk('public')->exists('images/kop-surat')) {
                    Storage::disk('public')->makeDirectory('images/kop-surat');
                }
                
                // Save in original format to preserve transparency
                if (strtolower($originalExtension) === 'png') {
                    // Save as PNG to preserve transparency
                    Storage::disk('public')->put('images/kop-surat/' . $kopSuratName, $image->toPng());
                } else {
                    // Save as JPEG for other formats
                    Storage::disk('public')->put('images/kop-surat/' . $kopSuratName, $image->toJpeg(90));
                }
                
                $data['kop_surat'] = $kopSuratName;
            }

            // Handle boolean values
            $data['maintenance_mode'] = $request->has('maintenance_mode') ? true : false;
            
            // Check TURNSTILE configuration before enabling captcha
            $turnstileSiteKey = env('TURNSTILE_SITE_KEY');
            $turnstileSecretKey = env('TURNSTILE_SECRET_KEY');
            
            if ($request->has('enable_captcha')) {
                // Check if keys are empty
                if (empty($turnstileSiteKey) || empty($turnstileSecretKey)) {
                    Alert::error('Error', 'TURNSTILE configuration is not set properly. Please configure valid TURNSTILE_SITE_KEY and TURNSTILE_SECRET_KEY in .env file first.');
                    return redirect()->back()->withInput();
                }
                
                // More flexible validation - allow various key formats including real Cloudflare keys
                if (strlen($turnstileSiteKey) < 20 || strlen($turnstileSecretKey) < 20) {
                    Alert::error('Error', 'TURNSTILE keys appear to be invalid. Please check your TURNSTILE_SITE_KEY and TURNSTILE_SECRET_KEY in .env file.');
                    return redirect()->back()->withInput();
                }
                
                $data['enable_captcha'] = true;
            } else {
                $data['enable_captcha'] = false;
            }

            // Handle email verification settings
            $data['email_verification_enabled'] = $request->has('email_verification_enabled') ? true : false;
            $data['require_email_verification_pmb'] = $request->has('require_email_verification_pmb') ? true : false;

            // Handle WhatsApp admin notification settings
            $data['wa_admin_payment_notification_enabled'] = $request->has('wa_admin_payment_notification_enabled') ? true : false;
            $data['wa_admin_krs_notification_enabled'] = $request->has('wa_admin_krs_notification_enabled') ? true : false;
            
            $adminNumbers = $request->input('wa_admin_numbers');
            $financeNumbers = $request->input('wa_finance_numbers');

            if (is_array($adminNumbers)) {
                $adminNumbers = array_filter(array_map('trim', $adminNumbers));
                $data['wa_admin_numbers'] = implode(',', $adminNumbers);
            } else {
                $data['wa_admin_numbers'] = $adminNumbers;
            }

            if (is_array($financeNumbers)) {
                $financeNumbers = array_filter(array_map('trim', $financeNumbers));
                $data['wa_finance_numbers'] = implode(',', $financeNumbers);
            } else {
                $data['wa_finance_numbers'] = $financeNumbers;
            }
            
            // Set default values for email verification if enabled
            if ($data['email_verification_enabled']) {
                $data['verification_code_length'] = $request->verification_code_length ?? 6;
                $data['verification_code_expiry_minutes'] = $request->verification_code_expiry_minutes ?? 10;
                $data['max_verification_attempts'] = $request->max_verification_attempts ?? 3;
                
                // Check mail configuration if email verification is enabled
                $mailHost = env('MAIL_HOST');
                $mailUsername = env('MAIL_USERNAME');
                $mailPassword = env('MAIL_PASSWORD');
                
                if (empty($mailHost) || empty($mailUsername) || empty($mailPassword)) {
                    Alert::warning('Warning', 'Email verification enabled but mail configuration is incomplete. Please configure MAIL_HOST, MAIL_USERNAME, and MAIL_PASSWORD in .env file for email verification to work properly.');
                }
            }

            $settings->update($data);
            Alert::success('Success', 'Web settings updated successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to update web settings: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function exportDatabase()
    {
        try {
            // Get database configuration
            $host = env('DB_HOST');
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');

            // Validate database configuration
            if (empty($host) || empty($database) || empty($username)) {
                throw new \Exception('Database configuration is incomplete');
            }

            // Create backup directory if not exists
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Generate filename with timestamp
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
            $filePath = $backupPath . '/' . $filename;

            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tables = array_map(function($table) {
                return array_values((array) $table)[0];
            }, $tables);

            // Start transaction
            DB::beginTransaction();

            try {
                // Open file for writing
                $handle = fopen($filePath, 'w');

                // Write header
                fwrite($handle, "-- Database Backup\n");
                fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");

                // Process each table
                foreach ($tables as $table) {
                    // Get table structure
                    $createTable = DB::select("SHOW CREATE TABLE `$table`")[0];
                    $createTable = array_values((array) $createTable)[1];
                    
                    // Write table structure
                    fwrite($handle, "\n-- Table structure for `$table`\n\n");
                    fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
                    fwrite($handle, $createTable . ";\n\n");

                    // Get table data
                    $rows = DB::table($table)->get();
                    
                    if (count($rows) > 0) {
                        fwrite($handle, "-- Data for table `$table`\n\n");
                        
                        foreach ($rows as $row) {
                            $values = array_map(function($value) {
                                if (is_null($value)) return 'NULL';
                                if (is_numeric($value)) return $value;
                                return "'" . addslashes($value) . "'";
                            }, (array) $row);
                            
                            fwrite($handle, "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n");
                        }
                    }
                }

                fclose($handle);
                DB::commit();

                // Check if file was created and has content
                if (!File::exists($filePath) || File::size($filePath) === 0) {
                    throw new \Exception('Backup file was not created or is empty');
                }

                // Return file download response
                return response()->download($filePath, $filename)->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                DB::rollBack();
                if (isset($handle) && is_resource($handle)) {
                    fclose($handle);
                }
                throw $e;
            }

        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to export database: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importDatabase(Request $request)
    {
        try {
            // More permissive validation
            $request->validate([
                'database_file' => 'required|file|max:50240' // Only check if it's a file and size limit
            ]);

            // Store uploaded file
            $file = $request->file('database_file');
            
            // Debug information
            Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize()
            ]);

            // Read the SQL file
            $sql = file_get_contents($file->getRealPath());
            
            // Get a fresh database connection
            $connection = DB::connection();
            
            try {
                // Disable foreign key checks
                $connection->statement('SET FOREIGN_KEY_CHECKS=0');
                
                // Process the SQL file
                $queries = [];
                $currentQuery = '';
                
                // Split the file into lines
                $lines = explode("\n", $sql);
                
                foreach ($lines as $line) {
                    // Skip comments and empty lines
                    if (empty(trim($line)) || strpos(trim($line), '--') === 0) {
                        continue;
                    }
                    
                    $currentQuery .= $line;
                    
                    // If the line ends with a semicolon, it's the end of a query
                    if (substr(trim($line), -1) === ';') {
                        $queries[] = $currentQuery;
                        $currentQuery = '';
                    }
                }
                
                // Execute each query
                foreach ($queries as $query) {
                    if (!empty(trim($query))) {
                        try {
                            $connection->unprepared($query);
                        } catch (\Exception $e) {
                            Log::error('Query failed: ' . $query);
                            Log::error('Error: ' . $e->getMessage());
                            throw $e;
                        }
                    }
                }
                
                // Re-enable foreign key checks
                $connection->statement('SET FOREIGN_KEY_CHECKS=1');
                
                // Clear cache after import
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');

                Alert::success('Success', 'Database imported successfully');
                return redirect()->back();
                
            } catch (\Exception $e) {
                // Re-enable foreign key checks in case of error
                $connection->statement('SET FOREIGN_KEY_CHECKS=1');
                throw new \Exception('Failed to import database: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to import database: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function renderHomepageSetting()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Pengaturan Homepage";
        $data['academy'] = $data['webs']->school_apps . '' . $data['webs']->school_name;
        
        return view('master.pengaturan.homepage-setting-index', $data, compact('user'));
    }

    public function updateHomepageSetting(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $webSetting = WebSetting::first();
            $section = $request->input('section');
            
            switch ($section) {
                case 'hero':
                    $updateData = [
                        'hero_title' => $request->hero_title,
                        'hero_subtitle' => $request->hero_subtitle,
                    ];
                    
                    // Handle hero background image
                    if ($request->hasFile('hero_background_image')) {
                        // Delete old image
                        if ($webSetting->hero_background_image) {
                            Storage::disk('public')->delete('images/homepage/' . $webSetting->hero_background_image);
                        }
                        
                        // Create directory if not exists
                        if (!Storage::disk('public')->exists('images/homepage')) {
                            Storage::disk('public')->makeDirectory('images/homepage');
                        }
                        
                        $imageName = 'hero-bg-' . time() . '.' . $request->hero_background_image->getClientOriginalExtension();
                        $request->hero_background_image->storeAs('images/homepage', $imageName, 'public');
                        $updateData['hero_background_image'] = $imageName;
                    }
                    break;
                    
                case 'statistics':
                    $updateData = [
                        'stat_programs_count' => $request->stat_programs_count,
                        'stat_programs_label' => $request->stat_programs_label,
                        'stat_students_count' => $request->stat_students_count,
                        'stat_students_label' => $request->stat_students_label,
                        'stat_lecturers_count' => $request->stat_lecturers_count,
                        'stat_lecturers_label' => $request->stat_lecturers_label,
                        'stat_partners_count' => $request->stat_partners_count,
                        'stat_partners_label' => $request->stat_partners_label,
                    ];
                    break;
                    
                case 'calendar':
                    $updateData = [
                        'academic_calendar' => json_encode($request->calendar)
                    ];
                    break;
                    
                case 'features':
                    $updateData = [
                        'features_section' => json_encode($request->features)
                    ];
                    break;
                    
                case 'programs':
                    $programs = $request->programs;
                    
                    // Handle program images
                    foreach ($programs as $index => $program) {
                        if ($request->hasFile("programs.{$index}.image")) {
                            // Create directory if not exists
                            if (!Storage::disk('public')->exists('images/homepage')) {
                                Storage::disk('public')->makeDirectory('images/homepage');
                            }
                            
                            $imageName = 'program-' . $index . '-' . time() . '.' . $request->file("programs.{$index}.image")->getClientOriginalExtension();
                            $request->file("programs.{$index}.image")->storeAs('images/homepage', $imageName, 'public');
                            $programs[$index]['image'] = $imageName;
                        }
                    }
                    
                    $updateData = [
                        'programs_section' => json_encode($programs)
                    ];
                    break;
                    
                case 'news':
                    $updateData = [
                        'news_main_title' => $request->news_main_title,
                        'news_main_content' => $request->news_main_content,
                        'news_main_category' => $request->news_main_category,
                        'news_main_date' => $request->news_main_date,
                    ];
                    
                    // Handle news main image
                    if ($request->hasFile('news_main_image')) {
                        // Delete old image
                        if ($webSetting->news_main_image) {
                            Storage::disk('public')->delete('images/homepage/' . $webSetting->news_main_image);
                        }
                        
                        // Create directory if not exists
                        if (!Storage::disk('public')->exists('images/homepage')) {
                            Storage::disk('public')->makeDirectory('images/homepage');
                        }
                        
                        $imageName = 'news-main-' . time() . '.' . $request->news_main_image->getClientOriginalExtension();
                        $request->news_main_image->storeAs('images/homepage', $imageName, 'public');
                        $updateData['news_main_image'] = $imageName;
                    }
                    break;
                    
                case 'gallery':
                    $gallery = $request->gallery;
                    
                    // Handle gallery images
                    foreach ($gallery as $index => $item) {
                        if ($request->hasFile("gallery.{$index}.image")) {
                            // Create directory if not exists
                            if (!Storage::disk('public')->exists('images/homepage')) {
                                Storage::disk('public')->makeDirectory('images/homepage');
                            }
                            
                            $imageName = 'gallery-' . $index . '-' . time() . '.' . $request->file("gallery.{$index}.image")->getClientOriginalExtension();
                            $request->file("gallery.{$index}.image")->storeAs('images/homepage', $imageName, 'public');
                            $gallery[$index]['image'] = $imageName;
                        }
                    }
                    
                    $updateData = [
                        'gallery_section' => json_encode($gallery)
                    ];
                    break;
                    
                case 'faq':
                    $updateData = [
                        'faq_section' => json_encode($request->faq)
                    ];
                    break;
                    
                case 'cta':
                    $updateData = [
                        'cta_title' => $request->cta_title,
                        'cta_subtitle' => $request->cta_subtitle,
                        'cta_button_text' => $request->cta_button_text,
                        'cta_button_link' => $request->cta_button_link,
                    ];
                    break;
                    
                default:
                    throw new \Exception('Section tidak valid');
            }
            
            $webSetting->update($updateData);
            
            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'pengaturan.homepage-render')->with('success', 'Pengaturan homepage berhasil diperbarui');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function getWhatsappStatus()
    {
        if (session_id()) {
            session_write_close();
        }

        try {
            $token = $this->getEnvValue('WHATSAPP_WEBHOOK_KEY') ?: $this->getEnvValue('WHATSAPP_WEBHOOK_TOKEN', 'siakad_wa_webhook_secret_key');
            $gatewayUrl = $this->getEnvValue('WHATSAPP_GATEWAY_URL', 'http://127.0.0.1:3000/message/sendText');
            $baseUrl = preg_replace('/\/message\/sendText.*/', '', $gatewayUrl);
            if (empty($baseUrl)) {
                $baseUrl = 'http://127.0.0.1:3000';
            }
            $statusUrl = $baseUrl . '/status';
            
            $response = \Illuminate\Support\Facades\Http::timeout(1.5)
                ->withHeaders(['X-Webhook-Token' => $token])
                ->get($statusUrl, ['token' => $token]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['status' => 'offline', 'message' => 'Node.js Bot returned status ' . $response->status()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'offline', 'message' => 'Unable to connect to WhatsApp Bot: ' . $e->getMessage()]);
        }
    }

    private function getEnvValue($key, $default = null)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1]);
                    if ($name === $key) {
                        return trim($value, " \t\n\r\0\x0B\"'");
                    }
                }
            }
        }
        return env($key, $default);
    }
}
