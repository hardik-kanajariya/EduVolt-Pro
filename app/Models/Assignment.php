<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'due_time',
        'max_marks',
        'attachments',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'due_time' => 'datetime:H:i',
        'attachments' => 'array',
        'max_marks' => 'decimal:2',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->where('due_date', '>=', now()->toDateString());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString());
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Accessors
    public function getDueAtAttribute()
    {
        $dueDate = $this->attributes['due_date'];

        if ($this->attributes['due_time']) {
            return $dueDate . ' ' . $this->attributes['due_time'];
        }
        return $dueDate . ' 23:59:59';
    }

    public function getIsOverdueAttribute()
    {
        return now() > $this->due_at;
    }

    public function getSubmissionStatsAttribute()
    {
        $totalStudents = $this->schoolClass->students()->count();
        $submissions = $this->submissions()->where('status', '!=', 'draft')->count();
        $graded = $this->submissions()->where('status', 'graded')->count();

        return [
            'total_students' => $totalStudents,
            'submitted' => $submissions,
            'pending' => $totalStudents - $submissions,
            'graded' => $graded,
            'ungraded' => $submissions - $graded,
        ];
    }
}
