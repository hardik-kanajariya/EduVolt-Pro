<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'session_id',
        'date',
        'status',
        'remarks',
        'marked_by',
        'in_time',
        'out_time',
    ];

    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Accessors
    public function getAttendancePercentageAttribute()
    {
        $totalDays = Attendance::where('student_id', $this->student_id)
            ->whereMonth('date', now()->month)
            ->count();
        
        if ($totalDays === 0) return 0;
        
        $presentDays = Attendance::where('student_id', $this->student_id)
            ->whereMonth('date', now()->month)
            ->where('status', 'present')
            ->count();
        
        return round(($presentDays / $totalDays) * 100, 2);
    }
}
