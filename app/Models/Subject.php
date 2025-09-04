<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'type',
        'credits',
        'status',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }

    public function scopeElective($query)
    {
        return $query->where('type', 'elective');
    }
}
