<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'class_id',
        'admission_number',
        'roll_number',
        'admission_date',
        'parent_name',
        'parent_phone',
        'parent_email',
        'medical_info',
        'transport_route',
        'emergency_contacts',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'emergency_contacts' => 'array',
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

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignments()
    {
        return $this->hasManyThrough(Assignment::class, SchoolClass::class, 'id', 'class_id', 'class_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name ?? '';
    }

    public function getAttendancePercentageAttribute()
    {
        $totalDays = $this->attendances()->count();
        if ($totalDays === 0) return 0;

        $presentDays = $this->attendances()->where('status', 'present')->count();
        return round(($presentDays / $totalDays) * 100, 2);
    }
}
