<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'subject_id',
        'class_id',
        'assignment_average',
        'exam_average',
        'attendance_percentage',
        'overall_grade',
        'letter_grade',
        'gpa',
        'total_assignments',
        'submitted_assignments',
        'late_submissions',
        'total_exams',
        'exams_taken',
        'exams_passed',
        'total_classes',
        'classes_attended',
        'classes_absent',
        'classes_late',
        'performance_trend',
        'previous_grade',
        'grade_change',
        'behavioral_score',
        'achievements',
        'areas_of_concern',
        'teacher_comments',
        'effort_level',
        'participation_level',
        'last_updated_at',
        'updated_by',
        'reporting_period_start',
        'reporting_period_end',
    ];

    protected $casts = [
        'assignment_average' => 'decimal:2',
        'exam_average' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'overall_grade' => 'decimal:2',
        'gpa' => 'decimal:2',
        'previous_grade' => 'decimal:2',
        'grade_change' => 'decimal:2',
        'achievements' => 'array',
        'areas_of_concern' => 'array',
        'last_updated_at' => 'datetime',
        'reporting_period_start' => 'date',
        'reporting_period_end' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByPerformanceTrend($query, $trend)
    {
        return $query->where('performance_trend', $trend);
    }

    public function scopeExcellentPerformance($query)
    {
        return $query->where('overall_grade', '>=', 85);
    }

    public function scopeNeedsAttention($query)
    {
        return $query->where('overall_grade', '<', 60)
                    ->orWhere('performance_trend', 'declining')
                    ->orWhere('attendance_percentage', '<', 75);
    }

    public function scopeImproving($query)
    {
        return $query->where('performance_trend', 'improving')
                    ->where('grade_change', '>', 0);
    }

    public function scopeCurrentPeriod($query)
    {
        return $query->where('reporting_period_start', '<=', now())
                    ->where('reporting_period_end', '>=', now());
    }

    // Accessors
    public function getAssignmentCompletionRateAttribute()
    {
        return $this->total_assignments > 0 
            ? round(($this->submitted_assignments / $this->total_assignments) * 100, 2)
            : 0;
    }

    public function getExamSuccessRateAttribute()
    {
        return $this->total_exams > 0 
            ? round(($this->exams_passed / $this->total_exams) * 100, 2)
            : 0;
    }

    public function getAttendanceStatusAttribute()
    {
        if ($this->attendance_percentage >= 95) return 'Excellent';
        if ($this->attendance_percentage >= 85) return 'Good';
        if ($this->attendance_percentage >= 75) return 'Satisfactory';
        if ($this->attendance_percentage >= 60) return 'Poor';
        return 'Critical';
    }

    public function getPerformanceStatusAttribute()
    {
        if ($this->overall_grade >= 90) return 'Outstanding';
        if ($this->overall_grade >= 80) return 'Excellent';
        if ($this->overall_grade >= 70) return 'Good';
        if ($this->overall_grade >= 60) return 'Satisfactory';
        if ($this->overall_grade >= 40) return 'Needs Improvement';
        return 'Unsatisfactory';
    }

    public function getGradeColorAttribute()
    {
        return match($this->letter_grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'warning',
            'D', 'F' => 'danger',
            default => 'secondary'
        };
    }

    public function getTrendIconAttribute()
    {
        return match($this->performance_trend) {
            'improving' => 'heroicon-o-arrow-trending-up',
            'declining' => 'heroicon-o-arrow-trending-down',
            'stable' => 'heroicon-o-minus',
            'excellent' => 'heroicon-o-star',
            'needs_attention' => 'heroicon-o-exclamation-triangle',
            default => 'heroicon-o-question-mark-circle'
        };
    }

    public function getTrendColorAttribute()
    {
        return match($this->performance_trend) {
            'improving', 'excellent' => 'success',
            'declining', 'needs_attention' => 'danger',
            'stable' => 'warning',
            default => 'secondary'
        };
    }

    // Helper methods
    public static function calculateGPA($grades)
    {
        if (empty($grades)) return 0;

        $gradePoints = [
            'A+' => 4.0, 'A' => 4.0,
            'B+' => 3.5, 'B' => 3.0,
            'C+' => 2.5, 'C' => 2.0,
            'D+' => 1.5, 'D' => 1.0,
            'F' => 0.0
        ];

        $totalPoints = 0;
        $totalSubjects = 0;

        foreach ($grades as $grade) {
            if (isset($gradePoints[$grade])) {
                $totalPoints += $gradePoints[$grade];
                $totalSubjects++;
            }
        }

        return $totalSubjects > 0 ? round($totalPoints / $totalSubjects, 2) : 0;
    }

    public function calculateLetterGrade()
    {
        if ($this->overall_grade >= 97) return 'A+';
        if ($this->overall_grade >= 93) return 'A';
        if ($this->overall_grade >= 90) return 'A-';
        if ($this->overall_grade >= 87) return 'B+';
        if ($this->overall_grade >= 83) return 'B';
        if ($this->overall_grade >= 80) return 'B-';
        if ($this->overall_grade >= 77) return 'C+';
        if ($this->overall_grade >= 73) return 'C';
        if ($this->overall_grade >= 70) return 'C-';
        if ($this->overall_grade >= 67) return 'D+';
        if ($this->overall_grade >= 65) return 'D';
        return 'F';
    }

    public function updateProgress($data = [])
    {
        // Calculate assignment metrics
        $assignments = Assignment::where('subject_id', $this->subject_id)
                                ->where('class_id', $this->class_id)
                                ->whereBetween('due_date', [$this->reporting_period_start, $this->reporting_period_end])
                                ->get();

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->pluck('id'))
                                          ->where('student_id', $this->student_id)
                                          ->get();

        $this->total_assignments = $assignments->count();
        $this->submitted_assignments = $submissions->count();
        $this->late_submissions = $submissions->where('is_late', true)->count();
        $this->setAttribute('assignment_average', round($submissions->avg('marks') ?? 0, 2));

        // Calculate exam metrics
        $examMarks = ExamMark::whereHas('examSubject', function($query) {
                            $query->where('subject_id', $this->subject_id)
                                  ->whereBetween('exam_date', [$this->reporting_period_start, $this->reporting_period_end]);
                        })
                        ->where('student_id', $this->student_id)
                        ->get();

        $this->total_exams = $examMarks->count();
        $this->exams_taken = $examMarks->where('is_absent', false)->count();
        $this->exams_passed = $examMarks->where('is_passed', true)->count();
        $this->setAttribute('exam_average', round($examMarks->where('is_absent', false)->avg('total_marks') ?? 0, 2));

        // Calculate attendance metrics
        $attendances = Attendance::where('student_id', $this->student_id)
                                ->where('subject_id', $this->subject_id)
                                ->whereBetween('date', [$this->reporting_period_start, $this->reporting_period_end])
                                ->get();

        $this->total_classes = $attendances->count();
        $this->classes_attended = $attendances->where('status', 'present')->count();
        $this->classes_absent = $attendances->where('status', 'absent')->count();
        $this->classes_late = $attendances->where('status', 'late')->count();
        $this->setAttribute('attendance_percentage', $this->total_classes > 0 
            ? round(($this->classes_attended / $this->total_classes) * 100, 2)
            : 0.0);

        // Calculate overall grade (weighted average)
        $assignmentWeight = 40; // 40% assignments
        $examWeight = 60; // 60% exams

        $this->setAttribute('overall_grade', round((($this->assignment_average * $assignmentWeight) + 
                               ($this->exam_average * $examWeight)) / 100, 2));

        // Update letter grade and GPA
        $this->letter_grade = $this->calculateLetterGrade();
        $this->setAttribute('gpa', round(self::calculateGPA([$this->letter_grade]), 2));

        // Determine performance trend
        if ($this->previous_grade) {
            $this->setAttribute('grade_change', round($this->overall_grade - $this->previous_grade, 2));
            
            if ($this->grade_change > 5) {
                $this->performance_trend = 'improving';
            } elseif ($this->grade_change < -5) {
                $this->performance_trend = 'declining';
            } else {
                $this->performance_trend = 'stable';
            }
        }

        if ($this->overall_grade >= 90) {
            $this->performance_trend = 'excellent';
        } elseif ($this->overall_grade < 60 || $this->attendance_percentage < 75) {
            $this->performance_trend = 'needs_attention';
        }

        // Update timestamps
        $this->last_updated_at = now();
        $this->updated_by = auth()->id();

        // Merge any additional data
        $this->fill($data);

        $this->save();

        return $this;
    }

    public function generateInsights()
    {
        $insights = [];

        // Academic insights
        if ($this->assignment_completion_rate < 80) {
            $insights[] = "Assignment completion rate is below expected level ({$this->assignment_completion_rate}%)";
        }

        if ($this->exam_success_rate < 70) {
            $insights[] = "Exam performance needs improvement ({$this->exam_success_rate}% success rate)";
        }

        if ($this->attendance_percentage < 85) {
            $insights[] = "Attendance is below satisfactory level ({$this->attendance_percentage}%)";
        }

        // Performance insights
        if ($this->performance_trend === 'improving') {
            $insights[] = "Student is showing positive academic improvement";
        } elseif ($this->performance_trend === 'declining') {
            $insights[] = "Student performance is declining and needs attention";
        }

        if ($this->late_submissions > 3) {
            $insights[] = "Frequent late submissions may indicate time management issues";
        }

        // Positive insights
        if ($this->overall_grade >= 85 && $this->attendance_percentage >= 95) {
            $insights[] = "Excellent academic performance with outstanding attendance";
        }

        return $insights;
    }

    // Boot method for automatic calculations
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($progress) {
            if (!$progress->reporting_period_start) {
                $progress->reporting_period_start = now()->startOfMonth();
            }
            if (!$progress->reporting_period_end) {
                $progress->reporting_period_end = now()->endOfMonth();
            }
        });
    }
}
