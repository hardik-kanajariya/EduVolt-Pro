<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'room',
        'teacher_id',
        'max_marks',
        'theory_marks',
        'practical_marks',
        'instructions',
        'is_active',
        'is_completed',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'max_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function examMarks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('exam_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('exam_date', $date);
    }

    // Accessors
    public function getIsUpcomingAttribute()
    {
        return $this->exam_date > today();
    }

    public function getIsTodayAttribute()
    {
        return Carbon::parse($this->exam_date)->isToday();
    }

    public function getIsOverdueAttribute()
    {
        return $this->exam_date < today() && !$this->is_completed;
    }

    public function getDurationHoursAttribute()
    {
        return round($this->duration_minutes / 60, 2);
    }

    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A') . ' - ' .
            Carbon::parse($this->end_time)->format('h:i A');
    }

    public function getStudentsCountAttribute()
    {
        return $this->examMarks()->distinct('student_id')->count();
    }

    public function getMarksEnteredCountAttribute()
    {
        return $this->examMarks()->whereNotNull('total_marks')->count();
    }

    public function getProgressPercentageAttribute()
    {
        $total = $this->students_count;
        $completed = $this->marks_entered_count;

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    // Helper methods
    public function hasTheoryMarks()
    {
        return $this->theory_marks > 0;
    }

    public function hasPracticalMarks()
    {
        return $this->practical_marks > 0;
    }

    public function canStartExam()
    {
        return $this->is_active &&
            $this->exam_date <= today() &&
            !$this->is_completed;
    }

    public function markAsCompleted()
    {
        $this->update(['is_completed' => true]);
    }

    public function getAverageMarks()
    {
        return $this->examMarks()
            ->whereNotNull('total_marks')
            ->avg('total_marks') ?? 0;
    }

    public function getHighestMarks()
    {
        return $this->examMarks()
            ->whereNotNull('total_marks')
            ->max('total_marks') ?? 0;
    }

    public function getLowestMarks()
    {
        return $this->examMarks()
            ->whereNotNull('total_marks')
            ->min('total_marks') ?? 0;
    }

    public function getPassedStudentsCount()
    {
        $passingMarks = $this->exam->passing_marks ?? ($this->max_marks * 0.4);

        return $this->examMarks()
            ->whereNotNull('total_marks')
            ->where('total_marks', '>=', $passingMarks)
            ->count();
    }

    public function getFailedStudentsCount()
    {
        $passingMarks = $this->exam->passing_marks ?? ($this->max_marks * 0.4);

        return $this->examMarks()
            ->whereNotNull('total_marks')
            ->where('total_marks', '<', $passingMarks)
            ->count();
    }

    public function getPassPercentage()
    {
        $total = $this->marks_entered_count;
        $passed = $this->getPassedStudentsCount();

        return $total > 0 ? round(($passed / $total) * 100, 2) : 0;
    }
}
