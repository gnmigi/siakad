<?php

namespace App\Services;

use App\Models\Keuangan\TagihanKuliah;
use App\Models\Mahasantri;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class PaymentReminderService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send reminders for payments approaching due date
     * Called by scheduled job daily
     */
    public function sendDailyReminders()
    {
        try {
            Log::info('Starting daily payment reminders');

            // Send reminders for different stages
            $this->sendBeforeDueReminders();
            $this->sendGracePeriodReminders();
            $this->sendOverdueReminders();
            $this->updateOverdueStatuses();

            Log::info('Daily payment reminders completed');

        } catch (\Exception $e) {
            Log::error('Error in daily payment reminders: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send reminders before due date (7, 3, 1 days before)
     */
    public function sendBeforeDueReminders()
    {
        $reminderDays = [7, 3, 1];

        foreach ($reminderDays as $days) {
            $targetDate = now()->addDays($days)->format('Y-m-d');
            
            $tagihans = TagihanKuliah::where('status', TagihanKuliah::PAYMENT_PENDING)
                ->whereNotNull('due_date') // Skip tagihan without due_date
                ->whereDate('due_date', $targetDate)
                ->where(function ($query) use ($days) {
                    // Only send if not sent today for this specific reminder type
                    $query->whereNull('reminder_sent_at')
                          ->orWhere('reminder_sent_at', '<', now()->startOfDay());
                })
                ->with(['mahasiswa', 'group'])
                ->get();

            foreach ($tagihans as $tagihan) {
                $this->sendBeforeDueReminder($tagihan, $days);
            }

            Log::info("Sent {$tagihans->count()} reminders for payments due in {$days} days");
        }
    }

    /**
     * Send reminders for payments in grace period
     */
    public function sendGracePeriodReminders()
    {
        $tagihans = TagihanKuliah::where('status', TagihanKuliah::PAYMENT_PENDING)
            ->whereNotNull('due_date') // Skip tagihan without due_date
            ->where('due_date', '<', now())
            ->where('overdue_status', TagihanKuliah::STATUS_GRACE_PERIOD)
            ->where(function ($query) {
                // Send grace period reminder every 3 days
                $query->whereNull('last_reminder_sent')
                      ->orWhere('last_reminder_sent', '<', now()->subDays(3));
            })
            ->with(['mahasiswa', 'group'])
            ->get();

        foreach ($tagihans as $tagihan) {
            $this->sendGracePeriodReminder($tagihan);
        }

        Log::info("Sent {$tagihans->count()} grace period reminders");
    }

    /**
     * Send reminders for overdue payments
     */
    public function sendOverdueReminders()
    {
        $tagihans = TagihanKuliah::where('status', TagihanKuliah::PAYMENT_PENDING)
            ->where('overdue_status', TagihanKuliah::STATUS_OVERDUE)
            ->where(function ($query) {
                // Send overdue reminder every 7 days
                $query->whereNull('last_reminder_sent')
                      ->orWhere('last_reminder_sent', '<', now()->subDays(7));
            })
            ->with(['mahasiswa', 'group'])
            ->get();

        foreach ($tagihans as $tagihan) {
            $this->sendOverdueReminder($tagihan);
        }

        Log::info("Sent {$tagihans->count()} overdue reminders");
    }

    /**
     * Update overdue statuses for all pending payments
     */
    public function updateOverdueStatuses()
    {
        $tagihans = TagihanKuliah::where('status', TagihanKuliah::PAYMENT_PENDING)->get();
        
        $updated = 0;
        foreach ($tagihans as $tagihan) {
            $oldStatus = $tagihan->overdue_status;
            $tagihan->updateOverdueStatus();
            
            if ($oldStatus !== $tagihan->overdue_status) {
                $updated++;
            }
        }

        Log::info("Updated overdue status for {$updated} payments");
    }

    /**
     * Send before due date reminder
     */
    protected function sendBeforeDueReminder(TagihanKuliah $tagihan, int $daysUntilDue)
    {
        try {
            $mahasiswa = $tagihan->mahasiswa;
            
            if (!$mahasiswa || !$mahasiswa->email) {
                Log::warning("Cannot send reminder - missing mahasiswa or email for tagihan {$tagihan->code}");
                return;
            }

            // Prepare reminder data
            $data = [
                'mahasiswa_name' => $mahasiswa->name,
                'tagihan_code' => $tagihan->code,
                'tagihan_name' => $tagihan->group->name ?? 'Tagihan Kuliah',
                'amount' => number_format($tagihan->amount, 0, ',', '.'),
                'due_date' => $tagihan->due_date->format('d/m/Y'),
                'days_until_due' => $daysUntilDue,
                'grace_period_days' => $tagihan->grace_period_days,
            ];

            // Send email reminder
            $this->sendEmailReminder($mahasiswa->email, 'before_due', $data);

            // Send WhatsApp if phone number available
            if ($mahasiswa->phone) {
                $this->sendWhatsAppReminder($mahasiswa->phone, 'before_due', $data);
            }

            // Update reminder tracking
            $tagihan->addReminderHistory('before_due', 'email', "Reminder sent {$daysUntilDue} days before due date");
            $tagihan->reminder_sent_at = now();
            $tagihan->save();

            Log::info("Sent before due reminder for tagihan {$tagihan->code} to {$mahasiswa->name}");

        } catch (\Exception $e) {
            Log::error("Error sending before due reminder for tagihan {$tagihan->code}: " . $e->getMessage());
        }
    }

    /**
     * Send grace period reminder
     */
    protected function sendGracePeriodReminder(TagihanKuliah $tagihan)
    {
        try {
            $mahasiswa = $tagihan->mahasiswa;
            
            if (!$mahasiswa || !$mahasiswa->email) {
                return;
            }

            $daysOverdue = $tagihan->getDaysOverdue();
            $gracePeriodEnd = $tagihan->due_date->addDays($tagihan->grace_period_days);
            $daysLeftInGrace = now()->diffInDays($gracePeriodEnd, false);

            $data = [
                'mahasiswa_name' => $mahasiswa->name,
                'tagihan_code' => $tagihan->code,
                'tagihan_name' => $tagihan->group->name ?? 'Tagihan Kuliah',
                'amount' => number_format($tagihan->amount, 0, ',', '.'),
                'due_date' => $tagihan->due_date->format('d/m/Y'),
                'days_overdue' => $daysOverdue,
                'days_left_in_grace' => max(0, $daysLeftInGrace),
                'grace_period_end' => $gracePeriodEnd->format('d/m/Y'),
            ];

            // Send email reminder
            $this->sendEmailReminder($mahasiswa->email, 'grace_period', $data);

            // Send WhatsApp if phone number available
            if ($mahasiswa->phone) {
                $this->sendWhatsAppReminder($mahasiswa->phone, 'grace_period', $data);
            }

            // Update reminder tracking
            $tagihan->addReminderHistory('grace_period', 'email', "Grace period reminder - {$daysOverdue} days overdue");

            Log::info("Sent grace period reminder for tagihan {$tagihan->code} to {$mahasiswa->name}");

        } catch (\Exception $e) {
            Log::error("Error sending grace period reminder for tagihan {$tagihan->code}: " . $e->getMessage());
        }
    }

    /**
     * Send overdue reminder
     */
    protected function sendOverdueReminder(TagihanKuliah $tagihan)
    {
        try {
            $mahasiswa = $tagihan->mahasiswa;
            
            if (!$mahasiswa || !$mahasiswa->email) {
                return;
            }

            $daysOverdue = $tagihan->getDaysOverdue();

            $data = [
                'mahasiswa_name' => $mahasiswa->name,
                'tagihan_code' => $tagihan->code,
                'tagihan_name' => $tagihan->group->name ?? 'Tagihan Kuliah',
                'amount' => number_format($tagihan->amount, 0, ',', '.'),
                'due_date' => $tagihan->due_date->format('d/m/Y'),
                'days_overdue' => $daysOverdue,
                'academic_advisor' => $mahasiswa->dosenPenasehat->name ?? 'Dosen Penasehat',
            ];

            // Send email reminder
            $this->sendEmailReminder($mahasiswa->email, 'overdue', $data);

            // Send WhatsApp if phone number available
            if ($mahasiswa->phone) {
                $this->sendWhatsAppReminder($mahasiswa->phone, 'overdue', $data);
            }

            // Notify academic advisor if overdue > 30 days
            if ($daysOverdue > 30 && $mahasiswa->dosenPenasehat) {
                $this->notifyAcademicAdvisor($tagihan, $mahasiswa->dosenPenasehat);
            }

            // Apply academic hold if overdue > 60 days
            if ($daysOverdue > 60 && !$tagihan->academic_hold_applied) {
                $tagihan->applyAcademicHold("Tunggakan pembayaran lebih dari 60 hari");
                $this->notifyAcademicHold($tagihan);
            }

            // Update reminder tracking
            $tagihan->addReminderHistory('overdue', 'email', "Overdue reminder - {$daysOverdue} days overdue");

            Log::info("Sent overdue reminder for tagihan {$tagihan->code} to {$mahasiswa->name}");

        } catch (\Exception $e) {
            Log::error("Error sending overdue reminder for tagihan {$tagihan->code}: " . $e->getMessage());
        }
    }

    /**
     * Send email reminder
     */
    protected function sendEmailReminder(string $email, string $type, array $data)
    {
        // This would integrate with your email service
        // For now, we'll log it
        Log::info("Email reminder sent", [
            'type' => $type,
            'email' => $email,
            'tagihan_code' => $data['tagihan_code']
        ]);

        // TODO: Implement actual email sending
        // Mail::to($email)->send(new PaymentReminderMail($type, $data));
    }

    /**
     * Send WhatsApp reminder
     */
    protected function sendWhatsAppReminder(string $phone, string $type, array $data)
    {
        // This would integrate with WhatsApp API
        Log::info("WhatsApp reminder sent", [
            'type' => $type,
            'phone' => $phone,
            'tagihan_code' => $data['tagihan_code']
        ]);

        // TODO: Implement actual WhatsApp sending
    }

    /**
     * Notify academic advisor about overdue payment
     */
    protected function notifyAcademicAdvisor(TagihanKuliah $tagihan, $dosen)
    {
        if (!$dosen->email) {
            return;
        }

        $data = [
            'dosen_name' => $dosen->name,
            'mahasiswa_name' => $tagihan->mahasiswa->name,
            'mahasiswa_nim' => $tagihan->mahasiswa->nim,
            'tagihan_code' => $tagihan->code,
            'tagihan_name' => $tagihan->group->name ?? 'Tagihan Kuliah',
            'amount' => number_format($tagihan->amount, 0, ',', '.'),
            'days_overdue' => $tagihan->getDaysOverdue(),
        ];

        Log::info("Academic advisor notification sent", [
            'dosen_email' => $dosen->email,
            'mahasiswa_name' => $tagihan->mahasiswa->name,
            'tagihan_code' => $tagihan->code
        ]);

        // TODO: Send actual email to academic advisor
    }

    /**
     * Notify about academic hold application
     */
    protected function notifyAcademicHold(TagihanKuliah $tagihan)
    {
        $mahasiswa = $tagihan->mahasiswa;

        Log::info("Academic hold notification sent", [
            'mahasiswa_name' => $mahasiswa->name,
            'tagihan_code' => $tagihan->code,
            'days_overdue' => $tagihan->getDaysOverdue()
        ]);

        // TODO: Send academic hold notification to mahasiswa and admin
    }

    /**
     * Get reminder statistics
     */
    public function getReminderStatistics()
    {
        return [
            'total_pending' => TagihanKuliah::pending()->count(),
            'on_time' => TagihanKuliah::pending()->onTime()->count(),
            'grace_period' => TagihanKuliah::pending()->gracePeriod()->count(),
            'overdue' => TagihanKuliah::pending()->overdue()->count(),
            'academic_hold' => TagihanKuliah::academicHoldApplied()->count(),
            'needs_reminder' => TagihanKuliah::needsReminder()->count(),
        ];
    }

    /**
     * Send manual reminder for specific tagihan
     */
    public function sendManualReminder(TagihanKuliah $tagihan, string $message = null)
    {
        try {
            $mahasiswa = $tagihan->mahasiswa;
            
            if (!$mahasiswa || !$mahasiswa->email) {
                throw new \Exception('Mahasiswa atau email tidak ditemukan');
            }

            $data = [
                'mahasiswa_name' => $mahasiswa->name,
                'tagihan_code' => $tagihan->code,
                'tagihan_name' => $tagihan->group->name ?? 'Tagihan Kuliah',
                'amount' => number_format($tagihan->amount, 0, ',', '.'),
                'due_date' => $tagihan->due_date ? $tagihan->due_date->format('d/m/Y') : 'Tidak ada tempo',
                'custom_message' => $message,
            ];

            // Send email reminder
            $this->sendEmailReminder($mahasiswa->email, 'manual', $data);

            // Send WhatsApp if phone number available
            if ($mahasiswa->phone) {
                $this->sendWhatsAppReminder($mahasiswa->phone, 'manual', $data);
            }

            // Update reminder tracking
            $tagihan->addReminderHistory('manual', 'email', $message ?? 'Manual reminder sent');

            return true;

        } catch (\Exception $e) {
            Log::error("Error sending manual reminder for tagihan {$tagihan->code}: " . $e->getMessage());
            throw $e;
        }
    }
}
