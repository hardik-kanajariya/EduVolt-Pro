<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'content',
        'variables',
        'type',
        'category',
        'is_active',
        'description',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function bulkEmails(): HasMany
    {
        return $this->hasMany(BulkEmail::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function renderTemplate(array $variables = []): string
    {
        $content = $this->content;

        foreach ($variables as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }

    public function renderSubject(array $variables = []): string
    {
        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{" . $key . "}}", $value, $subject);
        }

        return $subject;
    }

    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }

    // Static methods for common templates
    public static function getSystemTemplates(): array
    {
        return [
            'welcome_student' => [
                'name' => 'Welcome Student',
                'subject' => 'Welcome to {{school_name}}!',
                'body' => 'Dear {{student_name}}, Welcome to {{school_name}}. Your admission number is {{admission_number}}.',
                'variables' => ['school_name', 'student_name', 'admission_number'],
                'type' => 'system',
                'category' => 'academic'
            ],
            'fee_reminder' => [
                'name' => 'Fee Payment Reminder',
                'subject' => 'Fee Payment Reminder - {{student_name}}',
                'body' => 'Dear {{parent_name}}, This is a reminder that fee payment of {{amount}} is due for {{student_name}}.',
                'variables' => ['parent_name', 'student_name', 'amount', 'due_date'],
                'type' => 'system',
                'category' => 'fees'
            ],
            'attendance_alert' => [
                'name' => 'Low Attendance Alert',
                'subject' => 'Attendance Alert - {{student_name}}',
                'body' => 'Dear {{parent_name}}, {{student_name}} has low attendance ({{attendance_percentage}}%).',
                'variables' => ['parent_name', 'student_name', 'attendance_percentage'],
                'type' => 'system',
                'category' => 'attendance'
            ],
            'exam_reminder' => [
                'name' => 'Exam Schedule Reminder',
                'subject' => 'Upcoming Exam - {{exam_name}}',
                'body' => 'Dear {{student_name}}, Your {{exam_name}} is scheduled on {{exam_date}} at {{exam_time}}.',
                'variables' => ['student_name', 'exam_name', 'exam_date', 'exam_time'],
                'type' => 'system',
                'category' => 'examinations'
            ]
        ];
    }
}
