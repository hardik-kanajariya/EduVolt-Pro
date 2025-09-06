<?php

use App\Services\EmailService;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/test-email', function () {
    try {
        $emailService = app(EmailService::class);

        // Test sending a welcome email
        $testUser = User::first();
        if (!$testUser) {
            return response()->json(['error' => 'No users found in database']);
        }

        $variables = [
            'student_name' => $testUser->name,
            'school_name' => 'EduVault Pro School',
            'admission_number' => 'EV2025001',
        ];

        $emailLog = $emailService->sendTemplateEmail(
            'welcome_student',
            $testUser->email,
            $variables
        );

        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully',
            'email_log_id' => $emailLog->id,
            'status' => $emailLog->status,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to send email: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/test-bulk-email', function () {
    try {
        $emailService = app(EmailService::class);

        $bulkEmail = $emailService->createBulkEmail(
            'Test Announcement',
            'Important School Announcement',
            '<h2>Test Announcement</h2><p>This is a test bulk email to all users.</p>',
            ['roles' => ['super_admin', 'school_admin']], // Send to admins only for testing
            null, // sender (will use current user)
            'normal',
            null // send immediately
        );

        return response()->json([
            'success' => true,
            'message' => 'Test bulk email created successfully',
            'bulk_email_id' => $bulkEmail->id,
            'recipient_count' => $bulkEmail->recipient_count,
            'status' => $bulkEmail->status,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create bulk email: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/email-stats', function () {
    $emailService = app(EmailService::class);

    $stats = $emailService->getEmailStatistics();

    return response()->json([
        'success' => true,
        'stats' => $stats,
    ]);
});
