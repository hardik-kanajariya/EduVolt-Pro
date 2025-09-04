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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name ?? '';
    }
}
