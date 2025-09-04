<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'report_type',
        'academic_year_id',
        'class_id',
        'subject_id',
        'student_id',
        'term',
        'date_from',
        'date_to',
        'status',
        'file_path',
        'file_format',
        'generated_by',
        'generated_at',
        'parameters',
        'summary_data',
        'is_scheduled',
        'schedule_frequency',
        'next_generation',
        'recipients',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'generated_at' => 'datetime',
        'next_generation' => 'datetime',
        'parameters' => 'array',
        'summary_data' => 'array',
        'recipients' => 'array',
        'is_scheduled' => 'boolean',
    ];

    // Report Types
    const TYPE_STUDENT_PROGRESS = 'student_progress';
    const TYPE_CLASS_PERFORMANCE = 'class_performance';
    const TYPE_ATTENDANCE_SUMMARY = 'attendance_summary';
    const TYPE_ASSIGNMENT_ANALYSIS = 'assignment_analysis';
    const TYPE_EXAM_RESULTS = 'exam_results';
    const TYPE_BEHAVIORAL_REPORT = 'behavioral_report';
    const TYPE_COMPREHENSIVE = 'comprehensive';
    const TYPE_PARENT_REPORT = 'parent_report';
    const TYPE_TEACHER_REPORT = 'teacher_report';
    const TYPE_ADMIN_DASHBOARD = 'admin_dashboard';

    // Report Status
    const STATUS_PENDING = 'pending';
    const STATUS_GENERATING = 'generating';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_SCHEDULED = 'scheduled';

    // File Formats
    const FORMAT_PDF = 'pdf';
    const FORMAT_EXCEL = 'excel';
    const FORMAT_CSV = 'csv';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';

    // Schedule Frequencies
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_ANNUALLY = 'annually';

    // Relationships
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scopes
    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeByClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    public function scopeDueForGeneration($query)
    {
        return $query->scheduled()
            ->where('next_generation', '<=', now())
            ->where('status', '!=', self::STATUS_GENERATING);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getReportTypeNameAttribute(): string
    {
        return match ($this->report_type) {
            self::TYPE_STUDENT_PROGRESS => 'Student Progress Report',
            self::TYPE_CLASS_PERFORMANCE => 'Class Performance Report',
            self::TYPE_ATTENDANCE_SUMMARY => 'Attendance Summary',
            self::TYPE_ASSIGNMENT_ANALYSIS => 'Assignment Analysis',
            self::TYPE_EXAM_RESULTS => 'Exam Results Report',
            self::TYPE_BEHAVIORAL_REPORT => 'Behavioral Report',
            self::TYPE_COMPREHENSIVE => 'Comprehensive Report',
            self::TYPE_PARENT_REPORT => 'Parent Report',
            self::TYPE_TEACHER_REPORT => 'Teacher Report',
            self::TYPE_ADMIN_DASHBOARD => 'Admin Dashboard',
            default => ucwords(str_replace('_', ' ', $this->report_type)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'success',
            self::STATUS_GENERATING => 'warning',
            self::STATUS_PENDING => 'info',
            self::STATUS_SCHEDULED => 'primary',
            self::STATUS_FAILED => 'danger',
            default => 'gray',
        };
    }

    public function getFormatIconAttribute(): string
    {
        return match ($this->file_format) {
            self::FORMAT_PDF => 'heroicon-o-document',
            self::FORMAT_EXCEL => 'heroicon-o-table-cells',
            self::FORMAT_CSV => 'heroicon-o-document-text',
            self::FORMAT_HTML => 'heroicon-o-globe-alt',
            self::FORMAT_JSON => 'heroicon-o-code-bracket',
            default => 'heroicon-o-document',
        };
    }

    public function getFileSizeAttribute(): ?string
    {
        if (!$this->file_path || !file_exists(storage_path('app/' . $this->file_path))) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->file_path || $this->status !== self::STATUS_COMPLETED) {
            return null;
        }
        
        return route('reports.download', ['report' => $this->id]);
    }

    // Methods
    public function markAsGenerating(): void
    {
        $this->update([
            'status' => self::STATUS_GENERATING,
            'generated_at' => now(),
        ]);
    }

    public function markAsCompleted(string $filePath, array $summaryData = []): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'file_path' => $filePath,
            'summary_data' => $summaryData,
            'generated_at' => now(),
        ]);

        if ($this->is_scheduled) {
            $this->scheduleNext();
        }
    }

    public function markAsFailed(string $error = null): void
    {
        $summaryData = $this->summary_data ?? [];
        if ($error) {
            $summaryData['error'] = $error;
        }

        $this->update([
            'status' => self::STATUS_FAILED,
            'summary_data' => $summaryData,
        ]);
    }

    public function scheduleNext(): void
    {
        if (!$this->is_scheduled || !$this->schedule_frequency) {
            return;
        }

        $nextGeneration = match ($this->schedule_frequency) {
            self::FREQUENCY_DAILY => now()->addDay(),
            self::FREQUENCY_WEEKLY => now()->addWeek(),
            self::FREQUENCY_MONTHLY => now()->addMonth(),
            self::FREQUENCY_QUARTERLY => now()->addMonths(3),
            self::FREQUENCY_ANNUALLY => now()->addYear(),
            default => null,
        };

        if ($nextGeneration) {
            $this->update([
                'next_generation' => $nextGeneration,
                'status' => self::STATUS_SCHEDULED,
            ]);
        }
    }

    public static function getReportTypes(): array
    {
        return [
            self::TYPE_STUDENT_PROGRESS => 'Student Progress Report',
            self::TYPE_CLASS_PERFORMANCE => 'Class Performance Report',
            self::TYPE_ATTENDANCE_SUMMARY => 'Attendance Summary',
            self::TYPE_ASSIGNMENT_ANALYSIS => 'Assignment Analysis',
            self::TYPE_EXAM_RESULTS => 'Exam Results Report',
            self::TYPE_BEHAVIORAL_REPORT => 'Behavioral Report',
            self::TYPE_COMPREHENSIVE => 'Comprehensive Report',
            self::TYPE_PARENT_REPORT => 'Parent Report',
            self::TYPE_TEACHER_REPORT => 'Teacher Report',
            self::TYPE_ADMIN_DASHBOARD => 'Admin Dashboard',
        ];
    }

    public static function getFileFormats(): array
    {
        return [
            self::FORMAT_PDF => 'PDF Document',
            self::FORMAT_EXCEL => 'Excel Spreadsheet',
            self::FORMAT_CSV => 'CSV File',
            self::FORMAT_HTML => 'HTML Page',
            self::FORMAT_JSON => 'JSON Data',
        ];
    }

    public static function getScheduleFrequencies(): array
    {
        return [
            self::FREQUENCY_DAILY => 'Daily',
            self::FREQUENCY_WEEKLY => 'Weekly',
            self::FREQUENCY_MONTHLY => 'Monthly',
            self::FREQUENCY_QUARTERLY => 'Quarterly',
            self::FREQUENCY_ANNUALLY => 'Annually',
        ];
    }
}
