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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFullTime($query)
    {
        return $query->where('employment_type', 'full_time');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name ?? '';
    }
}
