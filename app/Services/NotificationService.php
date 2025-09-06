<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send push notification to user's devices
     */
    public function sendPushNotification(
        User $user,
        string $title,
        string $message,
        array $data = [],
        ?string $icon = null,
        ?string $clickAction = null
    ): bool {
        $deviceTokens = $this->getUserDeviceTokens($user);

        if (empty($deviceTokens)) {
            Log::info("No device tokens found for user", ['user_id' => $user->id]);
            return false;
        }

        $payload = [
            'registration_ids' => $deviceTokens,
            'notification' => [
                'title' => $title,
                'body' => $message,
                'icon' => $icon ?? 'default',
                'click_action' => $clickAction,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'user_id' => $user->id,
                'timestamp' => now()->toISOString(),
            ]),
        ];

        return $this->sendFCMRequest($payload);
    }

    /**
     * Send push notification to multiple users
     */
    public function sendBulkPushNotification(
        array $users,
        string $title,
        string $message,
        array $data = [],
        ?string $icon = null,
        ?string $clickAction = null
    ): array {
        $results = [];

        foreach ($users as $user) {
            $results[$user->id] = $this->sendPushNotification(
                $user,
                $title,
                $message,
                $data,
                $icon,
                $clickAction
            );
        }

        return $results;
    }

    /**
     * Send push notification by role
     */
    public function sendNotificationByRole(
        string $role,
        string $title,
        string $message,
        array $data = [],
        ?string $icon = null,
        ?string $clickAction = null
    ): array {
        $users = User::role($role)->get();

        return $this->sendBulkPushNotification(
            $users->toArray(),
            $title,
            $message,
            $data,
            $icon,
            $clickAction
        );
    }

    /**
     * Send emergency notification to all admin users
     */
    public function sendEmergencyNotification(
        string $title,
        string $message,
        array $data = []
    ): array {
        $adminRoles = ['super_admin', 'school_admin', 'principal'];
        $results = [];

        foreach ($adminRoles as $role) {
            $roleResults = $this->sendNotificationByRole(
                $role,
                "🚨 EMERGENCY: {$title}",
                $message,
                array_merge($data, ['emergency' => true]),
                'emergency',
                'emergency_action'
            );

            $results = array_merge($results, $roleResults);
        }

        // Also send email notification
        $this->emailService->sendEmergencyNotification($title, $message);

        return $results;
    }

    /**
     * Send attendance alert notification
     */
    public function sendAttendanceAlert(User $student, float $attendancePercentage): void
    {
        // Send to student
        $this->sendPushNotification(
            $student,
            'Attendance Alert',
            "Your attendance is {$attendancePercentage}%. Please maintain regular attendance.",
            [
                'type' => 'attendance_alert',
                'attendance_percentage' => $attendancePercentage,
            ],
            'warning'
        );

        // Find and notify parent if exists
        $parent = $this->findStudentParent($student);
        if ($parent) {
            $this->sendPushNotification(
                $parent,
                'Student Attendance Alert',
                "{$student->name}'s attendance is {$attendancePercentage}%. Please ensure regular attendance.",
                [
                    'type' => 'attendance_alert',
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'attendance_percentage' => $attendancePercentage,
                ],
                'warning'
            );
        }

        // Also send email
        $this->emailService->sendAttendanceAlert($student, $attendancePercentage);
    }

    /**
     * Send fee reminder notification
     */
    public function sendFeeReminder(User $student, float $amount, \DateTime $dueDate): void
    {
        $daysUntilDue = now()->diffInDays($dueDate, false);
        $urgency = $daysUntilDue <= 3 ? 'urgent' : ($daysUntilDue <= 7 ? 'warning' : 'info');

        // Send to student
        $this->sendPushNotification(
            $student,
            'Fee Payment Reminder',
            "Fee payment of ₹{$amount} is due on {$dueDate->format('d M Y')}.",
            [
                'type' => 'fee_reminder',
                'amount' => $amount,
                'due_date' => $dueDate->toISOString(),
                'days_until_due' => $daysUntilDue,
            ],
            $urgency
        );

        // Find and notify parent
        $parent = $this->findStudentParent($student);
        if ($parent) {
            $this->sendPushNotification(
                $parent,
                'Fee Payment Reminder',
                "Fee payment of ₹{$amount} for {$student->name} is due on {$dueDate->format('d M Y')}.",
                [
                    'type' => 'fee_reminder',
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'amount' => $amount,
                    'due_date' => $dueDate->toISOString(),
                    'days_until_due' => $daysUntilDue,
                ],
                $urgency
            );
        }

        // Also send email
        $this->emailService->sendFeeReminder($student, $amount, $dueDate);
    }

    /**
     * Send grade/result notification
     */
    public function sendGradeNotification(User $student, string $examName, array $grades): void
    {
        $message = "Results for {$examName} are now available.";

        // Send to student
        $this->sendPushNotification(
            $student,
            'Exam Results Available',
            $message,
            [
                'type' => 'grade_notification',
                'exam_name' => $examName,
                'grades' => $grades,
            ],
            'academic'
        );

        // Find and notify parent
        $parent = $this->findStudentParent($student);
        if ($parent) {
            $this->sendPushNotification(
                $parent,
                'Student Results Available',
                "Results for {$student->name}'s {$examName} are now available.",
                [
                    'type' => 'grade_notification',
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'exam_name' => $examName,
                    'grades' => $grades,
                ],
                'academic'
            );
        }
    }

    /**
     * Send event announcement
     */
    public function sendEventAnnouncement(
        string $eventTitle,
        string $eventDescription,
        \DateTime $eventDate,
        array $targetRoles = ['student', 'parent', 'faculty']
    ): array {
        $results = [];

        foreach ($targetRoles as $role) {
            $roleResults = $this->sendNotificationByRole(
                $role,
                "Event: {$eventTitle}",
                "{$eventDescription} - {$eventDate->format('d M Y, H:i')}",
                [
                    'type' => 'event_announcement',
                    'event_title' => $eventTitle,
                    'event_description' => $eventDescription,
                    'event_date' => $eventDate->toISOString(),
                ],
                'event'
            );

            $results = array_merge($results, $roleResults);
        }

        return $results;
    }

    /**
     * Register device token for user
     */
    public function registerDeviceToken(User $user, string $token, string $platform = 'web'): void
    {
        $userTokens = $this->getUserDeviceTokens($user);

        if (!in_array($token, $userTokens)) {
            $userTokens[] = $token;
            $this->storeUserDeviceTokens($user, $userTokens);
        }

        Log::info("Device token registered", [
            'user_id' => $user->id,
            'platform' => $platform,
        ]);
    }

    /**
     * Remove device token for user
     */
    public function removeDeviceToken(User $user, string $token): void
    {
        $userTokens = $this->getUserDeviceTokens($user);
        $userTokens = array_values(array_filter($userTokens, fn($t) => $t !== $token));
        $this->storeUserDeviceTokens($user, $userTokens);

        Log::info("Device token removed", [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Get user's device tokens
     */
    protected function getUserDeviceTokens(User $user): array
    {
        return Cache::get("user_device_tokens_{$user->id}", []);
    }

    /**
     * Store user's device tokens
     */
    protected function storeUserDeviceTokens(User $user, array $tokens): void
    {
        Cache::put("user_device_tokens_{$user->id}", $tokens, now()->addDays(30));
    }

    /**
     * Send FCM request
     */
    protected function sendFCMRequest(array $payload): bool
    {
        $serverKey = config('services.fcm.server_key');

        if (!$serverKey) {
            Log::error("FCM server key not configured");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$serverKey}",
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info("Push notification sent successfully", [
                    'success_count' => $result['success'] ?? 0,
                    'failure_count' => $result['failure'] ?? 0,
                ]);
                return true;
            } else {
                Log::error("FCM request failed", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("FCM request exception", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Find student's parent user
     */
    protected function findStudentParent(User $student): ?User
    {
        // This would depend on your database structure
        // For now, we'll try to find by parent_email in student profile
        if ($student->student && $student->student->parent_email) {
            return User::where('email', $student->student->parent_email)->first();
        }

        return null;
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStatistics(?\DateTime $from = null, ?\DateTime $to = null): array
    {
        // This would require implementing notification logging
        // For now, return basic structure
        return [
            'push_notifications_sent' => 0,
            'email_notifications_sent' => 0,
            'emergency_notifications' => 0,
            'attendance_alerts' => 0,
            'fee_reminders' => 0,
            'grade_notifications' => 0,
            'event_announcements' => 0,
        ];
    }
}
