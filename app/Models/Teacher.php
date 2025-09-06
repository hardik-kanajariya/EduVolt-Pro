<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'employee_id',
        'qualification',
        'experience_years',
        'join_date',
        'salary',
        'employment_type',
        'specialization',
        'certifications',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary' => 'decimal:2',
        'experience_years' => 'integer',
        'certifications' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function assignedClasses()
    {
        return $this->hasMany(SchoolClass::class, 'class_teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    public function teacherClassSubjects()
    {
        return $this->hasMany(TeacherClassSubject::class);
    }

    public function teachingClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_class_subjects', 'teacher_id', 'class_id')
            ->withPivot(['subject_id', 'status'])
            ->wherePivot('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFullTime($query)
    {
        return $query->where('employment_type', 'full_time');
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->whereHas('subjects', function ($q) use ($subjectId) {
            $q->where('subjects.id', $subjectId);
        });
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name ?? '';
    }

    public function getTotalStudentsAttribute()
    {
        return $this->assignedClasses->sum(function ($class) {
            return $class->students()->count();
        });
    }
}
