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

    public function getClassAssignmentsAttribute()
    {
        return Assignment::where('class_id', $this->class_id)->get();
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function feeAssignments()
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function feeWaivers()
    {
        return $this->hasMany(FeeWaiver::class);
    }

    public function feeReminders()
    {
        return $this->hasMany(FeeReminder::class);
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

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    public function bookReservations()
    {
        return $this->hasMany(BookReservation::class);
    }

    public function libraryFines()
    {
        return $this->hasMany(LibraryFine::class);
    }

    public function currentBookIssues()
    {
        return $this->hasMany(BookIssue::class)->where('status', 'issued');
    }

    public function activeBookReservations()
    {
        return $this->hasMany(BookReservation::class)->where('status', 'active');
    }

    public function pendingLibraryFines()
    {
        return $this->hasMany(LibraryFine::class)->where('status', 'pending');
    }

    public function getAttendancePercentageAttribute()
    {
        $totalDays = $this->attendances()->count();
        if ($totalDays === 0) return 0;

        $presentDays = $this->attendances()->where('status', 'present')->count();
        return round(($presentDays / $totalDays) * 100, 2);
    }

    public function getFirstNameAttribute()
    {
        $name = $this->user->name ?? '';
        return explode(' ', $name)[0] ?? '';
    }

    public function getLastNameAttribute()
    {
        $name = $this->user->name ?? '';
        $nameParts = explode(' ', $name);
        return count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
    }

    // Library-related methods
    public function getTotalBooksIssuedAttribute(): int
    {
        return $this->bookIssues()->count();
    }

    public function getCurrentBooksCountAttribute(): int
    {
        return $this->currentBookIssues()->count();
    }

    public function getTotalLibraryFineAmountAttribute(): float
    {
        return $this->pendingLibraryFines()->sum('amount') ?? 0;
    }

    public function hasOverdueBooks(): bool
    {
        return $this->currentBookIssues()->where('due_date', '<', now())->exists();
    }

    public function canIssueBooks(): bool
    {
        return $this->getCurrentBooksCountAttribute() < 3 // Max 3 books at a time
            && $this->getTotalLibraryFineAmountAttribute() < 50; // Max ₹50 pending fine
    }
}
