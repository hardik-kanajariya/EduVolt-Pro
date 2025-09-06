<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\BulkEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmailService
{
    /**
     * Send a single email using template
     */
    public function sendTemplateEmail(
        string $templateName,
        string $recipientEmail,
        array $variables = [],
        ?User $sender = null,
        string $priority = 'normal',
        ?\DateTime $scheduledAt = null
    ): EmailLog {
        $template = EmailTemplate::where('name', $templateName)->active()->first();

        if (!$template) {
            throw new \Exception("Email template '{$templateName}' not found or inactive");
        }

        return $this->sendEmail(
            $recipientEmail,
            $template->renderSubject($variables),
            $template->renderTemplate($variables),
            $sender,
            $priority,
            $scheduledAt,
            $template
        );
    }

    /**
     * Send a single email
     */
    public function sendEmail(
        string $recipientEmail,
        string $subject,
        string $content,
        ?User $sender = null,
        string $priority = 'normal',
        ?\DateTime $scheduledAt = null,
        ?EmailTemplate $template = null,
        array $attachments = [],
        array $metadata = []
    ): EmailLog {
        $sender = $sender ?? Auth::user();

        if (!$sender) {
            throw new \Exception('No sender specified and no authenticated user found');
        }

        // Create email log entry
        $emailLog = EmailLog::create([
            'message_id' => Str::uuid(),
            'email_template_id' => $template?->id,
            'sender_id' => $sender->id,
            'recipient_email' => $recipientEmail,
            'subject' => $subject,
            'content' => $content,
            'attachments' => $attachments,
            'status' => 'pending',
            'priority' => $priority,
            'scheduled_at' => $scheduledAt,
            'metadata' => $metadata,
        ]);

        // If not scheduled, send immediately
        if (!$scheduledAt || $scheduledAt <= now()) {
            $this->processSingleEmail($emailLog);
        }

        return $emailLog;
    }

    /**
     * Process and send a single email
     */
    public function processSingleEmail(EmailLog $emailLog): bool
    {
        try {
            // Send the email
            Mail::html($emailLog->content, function ($message) use ($emailLog) {
                $message->to($emailLog->recipient_email)
                    ->subject($emailLog->subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));

                // Add attachments if any
                if ($emailLog->attachments) {
                    foreach ($emailLog->attachments as $attachment) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? null,
                            'mime' => $attachment['mime'] ?? null,
                        ]);
                    }
                }

                // Set priority
                switch ($emailLog->priority) {
                    case 'urgent':
                        $message->priority(1);
                        break;
                    case 'high':
                        $message->priority(2);
                        break;
                    case 'low':
                        $message->priority(4);
                        break;
                    default:
                        $message->priority(3);
                }
            });

            $emailLog->markAsSent();

            Log::info("Email sent successfully", [
                'email_log_id' => $emailLog->id,
                'recipient' => $emailLog->recipient_email,
                'subject' => $emailLog->subject,
            ]);

            return true;
        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());

            Log::error("Failed to send email", [
                'email_log_id' => $emailLog->id,
                'recipient' => $emailLog->recipient_email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create and queue bulk email campaign
     */
    public function createBulkEmail(
        string $name,
        string $subject,
        string $content,
        array $recipientCriteria,
        ?User $sender = null,
        string $priority = 'normal',
        ?\DateTime $scheduledAt = null,
        ?EmailTemplate $template = null,
        array $attachments = [],
        string $notes = ''
    ): BulkEmail {
        $sender = $sender ?? Auth::user();

        if (!$sender) {
            throw new \Exception('No sender specified and no authenticated user found');
        }

        $bulkEmail = BulkEmail::create([
            'name' => $name,
            'subject' => $subject,
            'content' => $content,
            'email_template_id' => $template?->id,
            'sender_id' => $sender->id,
            'recipient_criteria' => $recipientCriteria,
            'status' => $scheduledAt ? 'scheduled' : 'draft',
            'priority' => $priority,
            'scheduled_at' => $scheduledAt,
            'attachments' => $attachments,
            'notes' => $notes,
        ]);

        // Build recipient list
        $bulkEmail->buildRecipientList();

        return $bulkEmail;
    }

    /**
     * Process bulk email campaign
     */
    public function processBulkEmail(BulkEmail $bulkEmail): void
    {
        if (!$bulkEmail->isDueForSending()) {
            return;
        }

        $bulkEmail->start();

        foreach ($bulkEmail->recipient_list as $recipient) {
            try {
                $emailLog = $this->sendEmail(
                    $recipient['email'],
                    $bulkEmail->subject,
                    $bulkEmail->content,
                    $bulkEmail->sender,
                    $bulkEmail->priority,
                    null, // Send immediately
                    $bulkEmail->emailTemplate,
                    $bulkEmail->attachments ?? [],
                    [
                        'bulk_email_id' => $bulkEmail->id,
                        'recipient_type' => $recipient['type'],
                        'recipient_id' => $recipient['id'],
                    ]
                );

                if ($emailLog->status === 'sent') {
                    $bulkEmail->incrementSentCount();
                } else {
                    $bulkEmail->incrementFailedCount();
                }
            } catch (\Exception $e) {
                $bulkEmail->incrementFailedCount();
                Log::error("Failed to send bulk email to recipient", [
                    'bulk_email_id' => $bulkEmail->id,
                    'recipient' => $recipient['email'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $bulkEmail->complete();
    }

    /**
     * Process pending emails
     */
    public function processPendingEmails(): void
    {
        $pendingEmails = EmailLog::dueForSending()
            ->orderBy('priority')
            ->orderBy('created_at')
            ->limit(50) // Process in batches
            ->get();

        foreach ($pendingEmails as $emailLog) {
            $this->processSingleEmail($emailLog);
        }
    }

    /**
     * Process scheduled bulk emails
     */
    public function processScheduledBulkEmails(): void
    {
        $scheduledBulkEmails = BulkEmail::dueForSending()->get();

        foreach ($scheduledBulkEmails as $bulkEmail) {
            $this->processBulkEmail($bulkEmail);
        }
    }

    /**
     * Retry failed emails
     */
    public function retryFailedEmails(): void
    {
        $failedEmails = EmailLog::failed()
            ->where('retry_count', '<', 3)
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        foreach ($failedEmails as $emailLog) {
            $this->processSingleEmail($emailLog);
        }
    }

    /**
     * Get email statistics
     */
    public function getEmailStatistics(?\DateTime $from = null, ?\DateTime $to = null): array
    {
        $query = EmailLog::query();

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        $total = $query->count();
        $sent = $query->clone()->sent()->count();
        $delivered = $query->clone()->delivered()->count();
        $failed = $query->clone()->failed()->count();
        $opened = $query->clone()->opened()->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'delivered' => $delivered,
            'failed' => $failed,
            'opened' => $opened,
            'pending' => $total - $sent - $failed,
            'delivery_rate' => $total > 0 ? ($delivered / $total) * 100 : 0,
            'open_rate' => $delivered > 0 ? ($opened / $delivered) * 100 : 0,
            'failure_rate' => $total > 0 ? ($failed / $total) * 100 : 0,
        ];
    }

    /**
     * Send emergency notification
     */
    public function sendEmergencyNotification(
        string $subject,
        string $message,
        array $recipientCriteria = ['roles' => ['super_admin', 'school_admin', 'principal']],
        ?User $sender = null
    ): BulkEmail {
        return $this->createBulkEmail(
            'Emergency Notification - ' . now()->format('Y-m-d H:i'),
            $subject,
            $message,
            $recipientCriteria,
            $sender,
            'urgent',
            null // Send immediately
        );
    }

    /**
     * Send welcome email to new student
     */
    public function sendWelcomeEmail(User $student, array $additionalData = []): EmailLog
    {
        $variables = array_merge([
            'student_name' => $student->name,
            'school_name' => 'EduVault Pro School',
            'admission_number' => $student->student->admission_number ?? 'N/A',
        ], $additionalData);

        return $this->sendTemplateEmail(
            'welcome_student',
            $student->email,
            $variables
        );
    }

    /**
     * Send fee reminder to parent
     */
    public function sendFeeReminder(User $student, float $amount, \DateTime $dueDate): EmailLog
    {
        $variables = [
            'parent_name' => $student->student->parent_name ?? 'Dear Parent',
            'student_name' => $student->name,
            'amount' => number_format($amount, 2),
            'due_date' => $dueDate->format('d M Y'),
        ];

        $parentEmail = $student->student->parent_email ?? $student->email;

        return $this->sendTemplateEmail(
            'fee_reminder',
            $parentEmail,
            $variables
        );
    }

    /**
     * Send attendance alert
     */
    public function sendAttendanceAlert(User $student, float $attendancePercentage): EmailLog
    {
        $variables = [
            'parent_name' => $student->student->parent_name ?? 'Dear Parent',
            'student_name' => $student->name,
            'attendance_percentage' => number_format($attendancePercentage, 1),
        ];

        $parentEmail = $student->student->parent_email ?? $student->email;

        return $this->sendTemplateEmail(
            'attendance_alert',
            $parentEmail,
            $variables
        );
    }
}
