<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_progress';

    protected $fillable = [
        'student_id',
        'subject_id',
        'term',
        'academic_year',
        'attendance_percentage',
        'assignment_average',
        'exam_average',
        'overall_grade',
        'letter_grade',
        'teacher_remarks',
        'conduct',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'assignment_average' => 'decimal:2',
        'exam_average' => 'decimal:2',
        'overall_grade' => 'decimal:2',
        'academic_year' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
