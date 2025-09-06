<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'section',
        'capacity',
        'room_number',
        'class_teacher_id',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherClassSubject::class, 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class_subjects', 'class_id', 'teacher_id')
            ->withPivot(['subject_id', 'status'])
            ->wherePivot('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->section;
    }

    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->student_count;
    }
}
