<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'school_id',
        'name',
        'description',
        'type',
        'start_date',
        'end_date',
        'status',
        'total_marks',
        'passing_marks',
        'grade_scale',
        'instructions',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'grade_scale' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function examSubjects(): HasMany
    {
        return $this->hasMany(ExamSubject::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    public function getIsOngoingAttribute()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    public function getIsCompletedAttribute()
    {
        return $this->end_date < now();
    }

    public function getDurationDaysAttribute()
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }

    // Helper methods
    public function getDefaultGradeScale()
    {
        return [
            'A+' => ['min' => 90, 'max' => 100],
            'A' => ['min' => 80, 'max' => 89],
            'B+' => ['min' => 70, 'max' => 79],
            'B' => ['min' => 60, 'max' => 69],
            'C+' => ['min' => 50, 'max' => 59],
            'C' => ['min' => 40, 'max' => 49],
            'F' => ['min' => 0, 'max' => 39],
        ];
    }

    public function calculateGrade($marks)
    {
        $gradeScale = $this->grade_scale ?? $this->getDefaultGradeScale();
        $percentage = ($marks / $this->total_marks) * 100;

        foreach ($gradeScale as $grade => $range) {
            if ($percentage >= $range['min'] && $percentage <= $range['max']) {
                return $grade;
            }
        }

        return 'F';
    }
}
