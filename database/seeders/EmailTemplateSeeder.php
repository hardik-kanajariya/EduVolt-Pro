<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Student',
                'slug' => 'welcome_student',
                'subject' => 'Welcome to {{school_name}}!',
                'content' => '<h2>Welcome {{student_name}}!</h2>
                <p>We are excited to have you join {{school_name}}. Your admission number is <strong>{{admission_number}}</strong>.</p>
                <p>Please login to your student portal to access your courses, assignments, and other important information.</p>
                <p>If you have any questions, please contact our office.</p>
                <p>Best regards,<br>{{school_name}} Administration</p>',
                'category' => 'academic',
                'type' => 'system',
                'variables' => json_encode(['student_name', 'school_name', 'admission_number']),
                'is_active' => true,
                'description' => 'Welcome email sent to new students',
            ],
            [
                'name' => 'Fee Reminder',
                'slug' => 'fee_reminder',
                'subject' => 'Fee Payment Reminder - {{student_name}}',
                'content' => '<h2>Fee Payment Reminder</h2>
                <p>Dear {{parent_name}},</p>
                <p>This is a friendly reminder that the fee payment for <strong>{{student_name}}</strong> is due on <strong>{{due_date}}</strong>.</p>
                <p><strong>Amount Due:</strong> ₹{{amount}}</p>
                <p>Please make the payment before the due date to avoid any late fees.</p>
                <p>You can make the payment through our online portal or visit the school office.</p>
                <p>Thank you for your attention to this matter.</p>
                <p>Best regards,<br>{{school_name}} Finance Department</p>',
                'category' => 'fees',
                'type' => 'system',
                'variables' => json_encode(['parent_name', 'student_name', 'amount', 'due_date', 'school_name']),
                'is_active' => true,
                'description' => 'Fee reminder sent to parents',
            ],
            [
                'name' => 'Attendance Alert',
                'slug' => 'attendance_alert',
                'subject' => 'Attendance Alert - {{student_name}}',
                'content' => '<h2>Attendance Alert</h2>
                <p>Dear {{parent_name}},</p>
                <p>We would like to bring to your attention that <strong>{{student_name}}</strong>\'s attendance has fallen to <strong>{{attendance_percentage}}%</strong>.</p>
                <p>Regular attendance is crucial for academic success. We encourage you to ensure that your child attends school regularly.</p>
                <p>If there are any specific concerns or issues affecting attendance, please contact us to discuss how we can help.</p>
                <p>Thank you for your cooperation.</p>
                <p>Best regards,<br>{{school_name}} Administration</p>',
                'category' => 'attendance',
                'type' => 'system',
                'variables' => json_encode(['parent_name', 'student_name', 'attendance_percentage', 'school_name']),
                'is_active' => true,
                'description' => 'Attendance alert sent to parents when attendance is low',
            ],
            [
                'name' => 'Exam Results Available',
                'slug' => 'exam_results',
                'subject' => 'Exam Results Available - {{exam_name}}',
                'content' => '<h2>Exam Results Available</h2>
                <p>Dear {{parent_name}},</p>
                <p>The results for <strong>{{exam_name}}</strong> are now available for <strong>{{student_name}}</strong>.</p>
                <p>You can view the detailed results by logging into the parent portal.</p>
                <p>If you have any questions about the results, please feel free to contact the respective subject teachers or the academic office.</p>
                <p>Thank you.</p>
                <p>Best regards,<br>{{school_name}} Academic Department</p>',
                'category' => 'examinations',
                'type' => 'system',
                'variables' => json_encode(['parent_name', 'student_name', 'exam_name', 'school_name']),
                'is_active' => true,
                'description' => 'Notification when exam results are published',
            ],
            [
                'name' => 'Event Announcement',
                'slug' => 'event_announcement',
                'subject' => 'Event: {{event_title}}',
                'content' => '<h2>{{event_title}}</h2>
                <p>Dear {{recipient_name}},</p>
                <p>We are pleased to announce an upcoming event:</p>
                <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0;">
                    <h3>{{event_title}}</h3>
                    <p><strong>Date:</strong> {{event_date}}</p>
                    <p><strong>Time:</strong> {{event_time}}</p>
                    <p><strong>Venue:</strong> {{event_venue}}</p>
                </div>
                <p>{{event_description}}</p>
                <p>We look forward to your participation.</p>
                <p>Best regards,<br>{{school_name}} Events Team</p>',
                'category' => 'events',
                'type' => 'system',
                'variables' => json_encode(['recipient_name', 'event_title', 'event_date', 'event_time', 'event_venue', 'event_description', 'school_name']),
                'is_active' => true,
                'description' => 'Event announcement template',
            ],
            [
                'name' => 'Emergency Notification',
                'slug' => 'emergency_notification',
                'subject' => '🚨 URGENT: {{emergency_title}}',
                'content' => '<div style="background: #dc3545; color: white; padding: 15px; text-align: center; margin-bottom: 20px;">
                    <h2>🚨 EMERGENCY NOTIFICATION</h2>
                </div>
                <h2>{{emergency_title}}</h2>
                <p>Dear {{recipient_name}},</p>
                <p>This is an urgent notification regarding:</p>
                <div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin: 15px 0;">
                    {{emergency_message}}
                </div>
                <p><strong>Action Required:</strong> {{action_required}}</p>
                <p><strong>Contact Information:</strong> {{contact_info}}</p>
                <p>Please take immediate action as required.</p>
                <p>{{school_name}} Administration</p>',
                'category' => 'emergency',
                'type' => 'system',
                'variables' => json_encode(['recipient_name', 'emergency_title', 'emergency_message', 'action_required', 'contact_info', 'school_name']),
                'is_active' => true,
                'description' => 'Emergency notification template for urgent communications',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
