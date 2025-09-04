<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_subject_id',
        'student_id',
        'theory_marks',
        'practical_marks',
        'total_marks',
        'grade',
        'is_absent',
        'remarks',
        'entered_by',
        'verified_by',
        'entered_at',
        'verified_at',
        'is_verified',
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'entered_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function examSubject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }

    public function scopePassed($query, $passingMarks = null)
    {
        $passing = $passingMarks ?? 40;
        return $query->where('total_marks', '>=', $passing)
            ->where('is_absent', false);
    }

    public function scopeFailed($query, $passingMarks = null)
    {
        $passing = $passingMarks ?? 40;
        return $query->where('total_marks', '<', $passing)
            ->where('is_absent', false);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    // Accessors
    public function getPercentageAttribute()
    {
        if ($this->is_absent || !$this->examSubject) {
            return 0;
        }

        $maxMarks = $this->examSubject->max_marks;
        return $maxMarks > 0 ? round(($this->total_marks / $maxMarks) * 100, 2) : 0;
    }

    public function getStatusAttribute()
    {
        if ($this->is_absent) {
            return 'Absent';
        }

        if (!$this->is_verified) {
            return 'Pending Verification';
        }

        $passingMarks = $this->examSubject->exam->passing_marks ??
            ($this->examSubject->max_marks * 0.4);

        return $this->total_marks >= $passingMarks ? 'Pass' : 'Fail';
    }

    public function getGradeColorAttribute()
    {
        return match ($this->grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'primary',
            'C+', 'C' => 'warning',
            'F' => 'danger',
            default => 'secondary'
        };
    }

    public function getIsPassedAttribute()
    {
        if ($this->is_absent) {
            return false;
        }

        $passingMarks = $this->examSubject->exam->passing_marks ??
            ($this->examSubject->max_marks * 0.4);

        return $this->total_marks >= $passingMarks;
    }

    public function getIsFailedAttribute()
    {
        return !$this->is_passed && !$this->is_absent;
    }

    // Helper methods
    public function calculateTotalMarks()
    {
        if ($this->is_absent) {
            return 0;
        }

        $total = 0;

        if ($this->theory_marks !== null) {
            $total += $this->theory_marks;
        }

        if ($this->practical_marks !== null) {
            $total += $this->practical_marks;
        }

        return $total;
    }

    public function calculateGrade()
    {
        if ($this->is_absent) {
            return 'AB'; // Absent
        }

        if (!$this->examSubject || !$this->examSubject->exam) {
            return 'F';
        }

        return $this->examSubject->exam->calculateGrade($this->total_marks);
    }

    public function markAsAbsent($remarks = null)
    {
        $this->update([
            'is_absent' => true,
            'theory_marks' => 0,
            'practical_marks' => 0,
            'total_marks' => 0,
            'grade' => 'AB',
            'remarks' => $remarks ?? 'Student was absent',
            'entered_at' => now(),
        ]);
    }

    public function verify($verifierId, $remarks = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $verifierId,
            'verified_at' => now(),
            'remarks' => $remarks ? $this->remarks . "\nVerification: " . $remarks : $this->remarks,
        ]);
    }

    public function unverify($remarks = null)
    {
        $this->update([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'remarks' => $remarks ? $this->remarks . "\nUnverified: " . $remarks : $this->remarks,
        ]);
    }

    // Boot method to auto-calculate totals
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($examMark) {
            if (!$examMark->is_absent) {
                $examMark->total_marks = $examMark->calculateTotalMarks();
                $examMark->grade = $examMark->calculateGrade();
            }

            if (!$examMark->entered_at) {
                $examMark->entered_at = now();
            }
        });
    }
}
