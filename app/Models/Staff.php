<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'employee_id',
        'position',
        'department',
        'join_date',
        'salary',
        'employment_type',
        'responsibilities',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary' => 'decimal:2',
        'responsibilities' => 'array',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name ?? '';
    }
}
