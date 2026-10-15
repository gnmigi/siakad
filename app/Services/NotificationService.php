<?php

namespace App\Services;

use App\Models\Keuangan\PaymentTransaction;
use App\Models\Keuangan\PaymentVerification;
use App\Models\User;
use App\Models\Pengaturan\WebSetting;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notify student about payment created
     */
    public function notifyPaymentCreated(PaymentTransaction $transaction)
    {
        try {
            $mahasiswa = $transaction->mahasiswa;
            
            // Email notification
            if ($mahasiswa->email) {
                $this->sendPaymentCreatedEmail($transaction);
            }

            // WhatsApp notification
            if ($mahasiswa->phone) {
                $this->sendPaymentCreatedWhatsApp($transaction);
            }

            // In-app notification
            $dueDateText = $transaction->due_date ? $transaction->due_date->format('d/m/Y H:i') : 'segera';
            $this->createInAppNotification($mahasiswa->user_id ?? null, [
                'type' => 'payment_created',
                'title' => 'Pembayaran Dibuat',
                'message' => "Pembayaran {$transaction->tagihan->name} telah dibuat. Silakan lakukan pembayaran sebelum {$dueDateText}",
                'data' => [
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'amount' => $transaction->total_amount,
                ]
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment Created Notification Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify student about payment verified
     */
    public function notifyPaymentVerified(PaymentTransaction $transaction)
    {
        try {
            $mahasiswa = $transaction->mahasiswa;
            
            // Email notification
            if ($mahasiswa->email) {
                $this->sendPaymentVerifiedEmail($transaction);
            }

            // WhatsApp notification
            if ($mahasiswa->phone) {
                $this->sendPaymentVerifiedWhatsApp($transaction);
            }

            // In-app notification
            $this->createInAppNotification($mahasiswa->user_id ?? null, [
                'type' => 'payment_verified',
                'title' => 'Pembayaran Terverifikasi',
                'message' => "Pembayaran {$transaction->tagihan->name} telah berhasil diverifikasi. Kwitansi dapat diunduh melalui dashboard Anda.",
                'data' => [
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'amount' => $transaction->total_amount,
                ]
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment Verified Notification Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify student about payment rejected
     */
    public function notifyPaymentRejected(PaymentTransaction $transaction, $reason)
    {
        try {
            $mahasiswa = $transaction->mahasiswa;
            
            // Email notification
            if ($mahasiswa->email) {
                $this->sendPaymentRejectedEmail($transaction, $reason);
            }

            // WhatsApp notification
            if ($mahasiswa->phone) {
                $this->sendPaymentRejectedWhatsApp($transaction, $reason);
            }

            // In-app notification
            $this->createInAppNotification($mahasiswa->user_id ?? null, [
                'type' => 'payment_rejected',
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran {$transaction->tagihan->name} ditolak. Alasan: {$reason}",
                'data' => [
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'reason' => $reason,
                ]
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment Rejected Notification Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify admin about new verification request
     */
    public function notifyAdminNewVerificationRequest(PaymentVerification $verification)
    {
        try {
            $transaction = $verification->paymentTransaction;
            $admins = $this->getFinanceAdmins();

            foreach ($admins as $admin) {
                // Email notification
                if ($admin->email) {
                    $this->sendVerificationRequestEmail($admin, $verification);
                }

                // In-app notification
                $this->createInAppNotification($admin->id, [
                    'type' => 'verification_request',
                    'title' => 'Permintaan Verifikasi Baru',
                    'message' => "Permintaan verifikasi pembayaran dari {$transaction->mahasiswa->name} untuk {$transaction->tagihan->name}",
                    'data' => [
                        'verification_id' => $verification->id,
                        'transaction_id' => $transaction->id,
                        'student_name' => $transaction->mahasiswa->name,
                        'amount' => $transaction->total_amount,
                    ]
                ]);
            }

            // Send WA notification to admin if enabled
            $webSetting = WebSetting::first();
            if ($webSetting && $webSetting->wa_admin_payment_notification_enabled && !empty($webSetting->wa_finance_numbers)) {
                $mhsName = $transaction->mahasiswa->name ?? 'Mahasantri';
                $mhsNim = $transaction->mahasiswa->numb_nim ?? '-';
                $tagihanName = $transaction->tagihan->group->name ?? $transaction->tagihan->name ?? 'Tagihan Kuliah';
                $totalAmt = number_format($transaction->total_amount, 0, ',', '.');
                $paidAmt = number_format($verification->verified_amount ?? $transaction->paid_amount ?? 0, 0, ',', '.');

                $waMessage = "📢 *NOTIFIKASI PEMBAYARAN BARU* (Untuk Admin Keuangan)\n\n" .
                             "Halo Admin Keuangan,\n\n" .
                             "Ada pengajuan verifikasi pembayaran baru yang memerlukan persetujuan Anda:\n" .
                             "👤 *Nama:* {$mhsName}\n" .
                             "🆔 *NIM:* {$mhsNim}\n" .
                             "📋 *Tagihan:* {$tagihanName}\n" .
                             "💰 *Nominal Tagihan:* Rp {$totalAmt}\n" .
                             "💵 *Jumlah Dibayar:* Rp {$paidAmt}\n\n" .
                             "Silakan cek dashboard SIAKAD Anda untuk memverifikasi transaksi ini.\n\n" .
                             "_Pesan otomatis dari SIAKAD._";

                $adminNumbers = explode(',', $webSetting->wa_finance_numbers);
                foreach ($adminNumbers as $number) {
                    $cleanNumber = trim($number);
                    if (!empty($cleanNumber)) {
                        $this->sendWhatsApp($cleanNumber, $waMessage);
                    }
                }
            }

            return true;

        } catch (Exception $e) {
            Log::error('Admin Verification Request Notification Error: ' . $e->getMessage(), [
                'verification_id' => $verification->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify admin about KRS submission
     */
    public function notifyAdminKrsSubmitted(\App\Models\Mahasantri\KrsMahasiswa $krs)
    {
        try {
            $webSetting = WebSetting::first();
            if ($webSetting && $webSetting->wa_admin_krs_notification_enabled && !empty($webSetting->wa_admin_numbers)) {
                $mahasiswa = $krs->mahasiswa;
                $mhsName = $mahasiswa->name ?? 'Mahasantri';
                $mhsNim = $mahasiswa->numb_nim ?? '-';
                $semester = $krs->semester;
                $totalSks = $krs->total_sks ?? 0;
                
                $message = "📢 *NOTIFIKASI PENGAJUAN KRS BARU* (Untuk Admin Akademik)\n\n" .
                           "Halo Admin Akademik,\n\n" .
                           "Ada mahasantri yang mengajukan KRS baru dan memerlukan persetujuan Anda:\n" .
                           "👤 *Nama:* {$mhsName}\n" .
                           "🆔 *NIM:* {$mhsNim}\n" .
                           "📊 *Semester:* {$semester}\n" .
                           "📚 *Total SKS:* {$totalSks} SKS\n\n" .
                           "Silakan cek dashboard SIAKAD Anda untuk memproses persetujuan KRS ini.\n\n" .
                           "_Pesan otomatis dari SIAKAD._";

                $adminNumbers = explode(',', $webSetting->wa_admin_numbers);
                foreach ($adminNumbers as $number) {
                    $cleanNumber = trim($number);
                    if (!empty($cleanNumber)) {
                        $this->sendWhatsApp($cleanNumber, $message);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error('KRS Submission Admin Notification Error: ' . $e->getMessage(), [
                'krs_id' => $krs->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify admin that verification needs review
     */
    public function notifyVerificationNeedsReview(PaymentVerification $verification)
    {
        try {
            $transaction = $verification->paymentTransaction;
            $seniorAdmins = $this->getSeniorFinanceAdmins();

            foreach ($seniorAdmins as $admin) {
                // Email notification
                if ($admin->email) {
                    $this->sendVerificationReviewEmail($admin, $verification);
                }

                // In-app notification
                $this->createInAppNotification($admin->id, [
                    'type' => 'verification_review',
                    'title' => 'Verifikasi Perlu Review',
                    'message' => "Verifikasi pembayaran dari {$transaction->mahasiswa->name} memerlukan review senior",
                    'data' => [
                        'verification_id' => $verification->id,
                        'transaction_id' => $transaction->id,
                        'student_name' => $transaction->mahasiswa->name,
                        'amount' => $transaction->total_amount,
                    ]
                ]);
            }

            return true;

        } catch (Exception $e) {
            Log::error('Verification Review Notification Error: ' . $e->getMessage(), [
                'verification_id' => $verification->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Notify about payment reminder
     */
    public function sendPaymentReminder(PaymentTransaction $transaction)
    {
        try {
            $mahasiswa = $transaction->mahasiswa;
            $daysLeft = now()->diffInDays($transaction->due_date, false);

            if ($daysLeft <= 0) {
                return false; // Already expired
            }

            $message = "Pengingat: Pembayaran {$transaction->tagihan->name} akan jatuh tempo dalam {$daysLeft} hari. Silakan segera lakukan pembayaran.";

            // Email notification
            if ($mahasiswa->email) {
                $this->sendPaymentReminderEmail($transaction, $daysLeft);
            }

            // WhatsApp notification
            if ($mahasiswa->phone) {
                $this->sendPaymentReminderWhatsApp($transaction, $daysLeft);
            }

            // In-app notification
            $this->createInAppNotification($mahasiswa->user_id ?? null, [
                'type' => 'payment_reminder',
                'title' => 'Pengingat Pembayaran',
                'message' => $message,
                'data' => [
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'due_date' => $transaction->due_date,
                    'days_left' => $daysLeft,
                ]
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment Reminder Notification Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send bulk payment reminders
     */
    public function sendBulkPaymentReminders($days = 3)
    {
        try {
            $transactions = PaymentTransaction::where('status', 'pending')
                ->whereDate('due_date', '=', now()->addDays($days))
                ->with(['mahasiswa', 'tagihan'])
                ->get();

            $sentCount = 0;
            $failedCount = 0;

            foreach ($transactions as $transaction) {
                if ($this->sendPaymentReminder($transaction)) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            return [
                'success' => true,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'total' => $transactions->count()
            ];

        } catch (Exception $e) {
            Log::error('Bulk Payment Reminder Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send payment created email
     */
    private function sendPaymentCreatedEmail(PaymentTransaction $transaction)
    {
        // Implement email sending logic
        // Mail::to($transaction->mahasiswa->email)->send(new PaymentCreatedMail($transaction));
    }

    /**
     * Send payment verified email
     */
    private function sendPaymentVerifiedEmail(PaymentTransaction $transaction)
    {
        // Implement email sending logic
        // Mail::to($transaction->mahasiswa->email)->send(new PaymentVerifiedMail($transaction));
    }

    /**
     * Send payment rejected email
     */
    private function sendPaymentRejectedEmail(PaymentTransaction $transaction, $reason)
    {
        // Implement email sending logic
        // Mail::to($transaction->mahasiswa->email)->send(new PaymentRejectedMail($transaction, $reason));
    }

    /**
     * Send payment reminder email
     */
    private function sendPaymentReminderEmail(PaymentTransaction $transaction, $daysLeft)
    {
        // Implement email sending logic
        // Mail::to($transaction->mahasiswa->email)->send(new PaymentReminderMail($transaction, $daysLeft));
    }

    /**
     * Send verification request email to admin
     */
    private function sendVerificationRequestEmail(User $admin, PaymentVerification $verification)
    {
        // Implement email sending logic
        // Mail::to($admin->email)->send(new VerificationRequestMail($admin, $verification));
    }

    /**
     * Send verification review email to admin
     */
    private function sendVerificationReviewEmail(User $admin, PaymentVerification $verification)
    {
        // Implement email sending logic
        // Mail::to($admin->email)->send(new VerificationReviewMail($admin, $verification));
    }

    /**
     * Send registration success email
     */
    public function sendRegistrationSuccessEmail($pendaftar)
    {
        try {
            Log::info('Attempting to send registration success email', [
                'email' => $pendaftar->email,
                'student_id' => $pendaftar->code,
                'name' => $pendaftar->name
            ]);
            
            $subject = 'Pendaftaran Berhasil - Ma\'al Amin';
            $htmlContent = $this->buildRegistrationSuccessEmailContent($pendaftar);
            
            // Use Laravel Mail with proper SMTP configuration
            Mail::send([], [], function ($message) use ($pendaftar, $subject, $htmlContent) {
                $message->to($pendaftar->email)
                        ->subject($subject)
                        ->html($htmlContent);
            });

            Log::info('Registration success email sent successfully', [
                'email' => $pendaftar->email,
                'student_id' => $pendaftar->code,
                'subject' => $subject
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Registration success email error: ' . $e->getMessage(), [
                'email' => $pendaftar->email,
                'student_id' => $pendaftar->code,
                'error' => $e->getTraceAsString(),
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username'),
                    'from_address' => config('mail.from.address')
                ]
            ]);
            return false;
        }
    }

    /**
     * Send email verification code
     */
    public function sendVerificationEmail($email, $code, $expiryMinutes = 10, $type = 'registration', $metadata = [])
    {
        try {
            Log::info('Attempting to send verification email', [
                'email' => $email,
                'code_length' => strlen($code),
                'expiry_minutes' => $expiryMinutes,
                'type' => $type
            ]);
            
            // Choose subject and content based on type
            if ($type === 'password_reset') {
                $subject = 'Kode Verifikasi Reset Password - Ma\'al Amin';
                $htmlContent = $this->buildPasswordResetEmailContent($code, $expiryMinutes, $metadata);
            } else {
                $subject = 'Kode Verifikasi Email - Ma\'al Amin';
                $htmlContent = $this->buildVerificationEmailContent($code, $expiryMinutes);
            }
            
            // Use Laravel Mail with proper SMTP configuration
            Mail::send([], [], function ($message) use ($email, $subject, $htmlContent) {
                $message->to($email)
                        ->subject($subject)
                        ->html($htmlContent);
            });

            Log::info('Verification email sent successfully via Laravel Mail', [
                'email' => $email,
                'subject' => $subject,
                'type' => $type
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Verification email error: ' . $e->getMessage(), [
                'email' => $email,
                'type' => $type,
                'error' => $e->getTraceAsString(),
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'username' => config('mail.mailers.smtp.username'),
                    'from_address' => config('mail.from.address')
                ]
            ]);
            return false;
        }
    }

    /**
     * Build registration success email content
     */
    private function buildRegistrationSuccessEmailContent($pendaftar)
    {
        $appName = "Ma'al Amin";
        $loginUrl = url('/signin');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Pendaftaran Berhasil - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; background: #f8f9fa; }
                .student-info { background: white; border: 2px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 8px; }
                .student-id { font-size: 28px; font-weight: bold; color: #28a745; text-align: center; 
                            padding: 15px; background: #e8f5e8; border-radius: 6px; margin: 15px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; background: #e9ecef; border-radius: 0 0 8px 8px; }
                .next-steps { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; }
                .login-info { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; border-radius: 6px; }
                .logo { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                .btn { display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🕌 Ma'al Amin</div>
                    <h1>🎉 Selamat! Pendaftaran Berhasil</h1>
                    <p>Pendaftaran Mahasiswa Baru Telah Berhasil Diproses</p>
                </div>
                <div class='content'>
                    <h2>Assalamu'alaikum {$pendaftar->name},</h2>
                    <p>Alhamdulillah, pendaftaran Anda sebagai calon mahasiswa di <strong>{$appName}</strong> telah berhasil diproses. Berikut adalah informasi pendaftaran Anda:</p>
                    
                    <div class='student-info'>
                        <h3 style='margin-top: 0; color: #28a745;'>📋 Informasi Pendaftaran</h3>
                        <div class='student-id'>{$pendaftar->code}</div>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; font-weight: bold; width: 30%;'>Nama Lengkap:</td>
                                <td style='padding: 8px 0;'>{$pendaftar->name}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-weight: bold;'>Email:</td>
                                <td style='padding: 8px 0;'>{$pendaftar->email}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-weight: bold;'>No. Telepon:</td>
                                <td style='padding: 8px 0;'>{$pendaftar->phone}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-weight: bold;'>Tanggal Daftar:</td>
                                <td style='padding: 8px 0;'>" . $pendaftar->register_date->format('d F Y, H:i') . " WIB</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-weight: bold;'>Status:</td>
                                <td style='padding: 8px 0;'><span style='background: #ffc107; color: #212529; padding: 4px 8px; border-radius: 4px; font-size: 12px;'>Menunggu Verifikasi</span></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class='login-info'>
                        <h4 style='margin-top: 0;'>🔐 Informasi Login</h4>
                        <p><strong>Email:</strong> {$pendaftar->email}<br>
                        <strong>Password:</strong> Gunakan password yang Anda buat saat pendaftaran</p>
                        <p><em>Jika lupa password, Anda dapat menggunakan fitur reset password di halaman login.</em></p>
                    </div>
                    
                    <div class='next-steps'>
                        <h4 style='margin-top: 0;'>📝 Langkah Selanjutnya:</h4>
                        <ol style='margin: 0; padding-left: 20px;'>
                            <li><strong>Simpan nomor pendaftaran Anda:</strong> {$pendaftar->code}</li>
                            <li><strong>Login ke dashboard:</strong> Gunakan email dan password Anda</li>
                            <li><strong>Lengkapi dokumen:</strong> Upload dokumen yang diperlukan</li>
                            <li><strong>Tunggu verifikasi:</strong> Admin akan memverifikasi dokumen Anda</li>
                            <li><strong>Pantau status:</strong> Cek dashboard secara berkala</li>
                        </ol>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$loginUrl}' class='btn'>🚀 Login ke Dashboard</a>
                    </div>
                    
                    <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami melalui WhatsApp atau email yang tersedia di website.</p>
                    
                    <p>Barakallahu fiikum,<br>Tim {$appName}</p>
                </div>
                <div class='footer'>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                    <p>&copy; " . date('Y') . " {$appName} - Sistem Informasi Akademik. All rights reserved.</p>
                    <p style='font-size: 12px; color: #999;'>Dikirim pada " . date('d F Y, H:i') . " WIB</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Build verification email content
     */
    private function buildVerificationEmailContent($code, $expiryMinutes)
    {
        $appName = "Ma'al Amin";
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Kode Verifikasi Email</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; background: #f8f9fa; }
                .code { font-size: 32px; font-weight: bold; color: #28a745; text-align: center; 
                        padding: 20px; background: white; border: 2px dashed #28a745; margin: 20px 0; border-radius: 8px; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; background: #e9ecef; border-radius: 0 0 8px 8px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; border-radius: 6px; }
                .logo { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🕌 Ma'al Amin</div>
                    <h1>Verifikasi Email Anda</h1>
                </div>
                <div class='content'>
                    <h2>Assalamu'alaikum!</h2>
                    <p>Terima kasih telah mendaftar di <strong>{$appName}</strong>. Untuk melanjutkan proses pendaftaran, silakan masukkan kode verifikasi berikut:</p>
                    
                    <div class='code'>{$code}</div>
                    
                    <div class='warning'>
                        <strong>⚠️ Penting:</strong>
                        <ul>
                            <li>Kode ini akan kedaluwarsa dalam <strong>{$expiryMinutes} menit</strong></li>
                            <li>Jangan bagikan kode ini kepada siapa pun</li>
                            <li>Jika Anda tidak melakukan pendaftaran, abaikan email ini</li>
                        </ul>
                    </div>
                    
                    <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami.</p>
                    
                    <p>Barakallahu fiikum,<br>Tim {$appName}</p>
                </div>
                <div class='footer'>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                    <p>&copy; " . date('Y') . " {$appName} - Sistem Informasi Akademik. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Build password reset email content
     */
    private function buildPasswordResetEmailContent($code, $expiryMinutes, $metadata = [])
    {
        $appName = "Ma'al Amin";
        $userName = $metadata['name'] ?? 'Pengguna';
        $userType = $metadata['user_type'] ?? 'user';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Reset Password - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; background: #f8f9fa; }
                .code { font-size: 32px; font-weight: bold; color: #28a745; text-align: center; 
                        padding: 20px; background: white; border: 2px dashed #28a745; margin: 20px 0; border-radius: 8px; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; background: #e9ecef; border-radius: 0 0 8px 8px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; border-radius: 6px; }
                .security-info { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; }
                .logo { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🕌 Ma'al Amin</div>
                    <h1>🔐 Reset Password</h1>
                    <p>Kode Verifikasi untuk Reset Password Anda</p>
                </div>
                <div class='content'>
                    <h2>Assalamu'alaikum {$userName},</h2>
                    <p>Kami menerima permintaan untuk reset password akun " . ($userType === 'dosen' ? 'dosen' : 'mahasiswa') . " Anda di <strong>{$appName}</strong>. Untuk melanjutkan proses reset password, silakan masukkan kode verifikasi berikut:</p>
                    
                    <div class='code'>{$code}</div>
                    
                    <div class='security-info'>
                        <strong>🛡️ Informasi Keamanan:</strong>
                        <ul>
                            <li>Kode ini hanya berlaku untuk <strong>{$expiryMinutes} menit</strong></li>
                            <li>Jangan bagikan kode ini kepada siapa pun</li>
                            <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                            <li>Untuk keamanan, segera ganti password setelah berhasil reset</li>
                        </ul>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Penting:</strong>
                        <ul>
                            <li>Kode ini akan kadaluarsa dalam <strong>{$expiryMinutes} menit</strong></li>
                            <li>Maksimal <strong>3 kali percobaan</strong> untuk memasukkan kode</li>
                            <li>Jika Anda tidak melakukan permintaan ini, segera hubungi administrator</li>
                        </ul>
                    </div>
                    
                    <p>Setelah memasukkan kode verifikasi, Anda akan diminta untuk membuat password baru. Pastikan password baru Anda:</p>
                    <ul>
                        <li>Minimal 6 karakter</li>
                        <li>Kombinasi huruf dan angka</li>
                        <li>Mudah diingat tapi sulit ditebak</li>
                    </ul>
                    
                    <p>Jika Anda mengalami kesulitan, silakan hubungi tim support kami.</p>
                    
                    <p>Barakallahu fiikum,<br>Tim {$appName}</p>
                </div>
                <div class='footer'>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                    <p>&copy; " . date('Y') . " {$appName} - Sistem Informasi Akademik. All rights reserved.</p>
                    <p style='font-size: 12px; color: #999;'>Dikirim pada " . date('d F Y, H:i') . " WIB</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Send WhatsApp message via configured gateway
     */
    private function sendWhatsApp($recipient, $message)
    {
        $gatewayUrl = config('services.whatsapp.url');
        $apiKey = config('services.whatsapp.api_key');
        $instance = config('services.whatsapp.instance', 'siakad_instance');

        if (empty($gatewayUrl) || empty($recipient)) {
            Log::info('📭 WA Gateway or recipient not configured. Message:', [
                'recipient' => $recipient,
                'message' => $message
            ]);
            return false;
        }

        try {
            $apiUrl = $gatewayUrl;
            if (strpos($apiUrl, '{instance}') !== false) {
                $apiUrl = str_replace('{instance}', $instance, $apiUrl);
            } elseif (substr($apiUrl, -1) !== '/' && strpos($apiUrl, 'sendText') !== false && strpos($apiUrl, $instance) === false) {
                $apiUrl = rtrim($apiUrl, '/') . '/' . $instance;
            }

            $headers = [];
            if (!empty($apiKey)) {
                $headers['apikey'] = $apiKey;
            }

            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders($headers)
                ->post($apiUrl, [
                    'number' => $recipient,
                    'text' => $message
                ]);

            if ($response->failed()) {
                Log::error('❌ WhatsApp Notification send failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error('❌ WhatsApp Notification Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve destination JID or phone from transaction metadata or student profile
     */
    private function resolveRecipientPhone(PaymentTransaction $transaction)
    {
        if ($transaction->metadata && is_array($transaction->metadata) && !empty($transaction->metadata['sender_jid'])) {
            return $transaction->metadata['sender_jid'];
        }
        
        $mhs = $transaction->mahasiswa;
        if ($mhs && $mhs->whatsapp_jid) {
            $jid = $mhs->whatsapp_jid;
            if (strpos($jid, '@') === false) {
                if (strlen($jid) > 13 || strpos($jid, '.') !== false) {
                    $jid .= '@lid';
                } else {
                    $jid .= '@s.whatsapp.net';
                }
            }
            return $jid;
        }
        
        return $transaction->mahasiswa->wa_phone ?? $transaction->mahasiswa->phone;
    }

    /**
     * Send WhatsApp notifications
     */
    private function sendPaymentCreatedWhatsApp(PaymentTransaction $transaction)
    {
        $dueDateText = $transaction->due_date ? $transaction->due_date->format('d/m/Y H:i') : 'Segera';
        $phone = $this->resolveRecipientPhone($transaction);

        $message = "🔔 *PEMBAYARAN DIBUAT*\n\n" .
                   "Halo {$transaction->mahasiswa->name},\n\n" .
                   "Pembayaran baru telah dibuat:\n" .
                   "📋 Kode: {$transaction->transaction_code}\n" .
                   "💰 Jumlah: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "\n" .
                   "📅 Jatuh Tempo: {$dueDateText}\n\n" .
                   "Silakan lakukan pembayaran melalui dashboard Anda.\n\n" .
                   "_Pesan otomatis dari sistem._";

        $this->sendWhatsApp($phone, $message);
    }

    private function sendPaymentVerifiedWhatsApp(PaymentTransaction $transaction)
    {
        $verifiedAtText = $transaction->verified_at ? $transaction->verified_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
        $phone = $this->resolveRecipientPhone($transaction);

        $paidAmount = (float)($transaction->paid_amount ?: $transaction->total_amount ?: $transaction->amount ?: 0);
        $tagihan = $transaction->tagihan;
        
        $statusText = "Lunas";
        $remainingText = "";
        
        if ($tagihan) {
            $tagihan = $tagihan->fresh();
            if ($tagihan->status === 'Cicilan') {
                $statusText = "Cicilan";
                $remainingText = "\n📊 Sisa Tagihan: Rp " . number_format($tagihan->remaining_amount, 0, ',', '.');
            }
        }

        $message = "✅ *PEMBAYARAN TERVERIFIKASI*\n\n" .
                   "Halo {$transaction->mahasiswa->name},\n\n" .
                   "Pembayaran Anda telah berhasil diverifikasi:\n" .
                   "📋 Kode: {$transaction->transaction_code}\n" .
                   "💰 Jumlah: Rp " . number_format($paidAmount, 0, ',', '.') . "\n" .
                   "📌 Status Tagihan: {$statusText}{$remainingText}\n" .
                   "📅 Tanggal: {$verifiedAtText}\n\n" .
                   "Kwitansi dapat diunduh melalui dashboard Anda.\n\n" .
                   "_Pesan otomatis dari sistem._";

        $this->sendWhatsApp($phone, $message);
    }

    private function sendPaymentRejectedWhatsApp(PaymentTransaction $transaction, $reason)
    {
        $phone = $this->resolveRecipientPhone($transaction);

        $message = "❌ *PEMBAYARAN DITOLAK*\n\n" .
                   "Halo {$transaction->mahasiswa->name},\n\n" .
                   "Pembayaran Anda ditolak:\n" .
                   "📋 Kode: {$transaction->transaction_code}\n" .
                   "💰 Jumlah: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "\n" .
                   "❗ Alasan: {$reason}\n\n" .
                   "Silakan hubungi bagian keuangan untuk informasi lebih lanjut.\n\n" .
                   "_Pesan otomatis dari sistem._";

        $this->sendWhatsApp($phone, $message);
    }

    private function sendPaymentReminderWhatsApp(PaymentTransaction $transaction, $daysLeft)
    {
        $dueDateText = $transaction->due_date ? $transaction->due_date->format('d/m/Y H:i') : 'Segera';
        $phone = $this->resolveRecipientPhone($transaction);

        $message = "⏰ *PENGINGAT PEMBAYARAN*\n\n" .
                   "Halo {$transaction->mahasiswa->name},\n\n" .
                   "Pembayaran akan jatuh tempo dalam {$daysLeft} hari:\n" .
                   "📋 Kode: {$transaction->transaction_code}\n" .
                   "💰 Jumlah: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "\n" .
                   "📅 Jatuh Tempo: {$dueDateText}\n\n" .
                   "Silakan segera lakukan pembayaran.\n\n" .
                   "_Pesan otomatis dari sistem._";

        $this->sendWhatsApp($phone, $message);
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification($userId, $data)
    {
        if (!$userId) return;

        // Implement in-app notification creation
        // This could be stored in database or sent via broadcasting
        /*
        Notification::create([
            'user_id' => $userId,
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => json_encode($data['data']),
            'read_at' => null,
        ]);
        */
    }

    /**
     * Get finance admin users
     */
    private function getFinanceAdmins()
    {
        return User::whereIn('type', [0, 2])->get();
    }

    /**
     * Get senior finance admin users
     */
    private function getSeniorFinanceAdmins()
    {
        return User::where('type', 0)->get();
    }
}
