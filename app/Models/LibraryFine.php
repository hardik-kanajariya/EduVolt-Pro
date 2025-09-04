<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryFine extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'book_issue_id',
        'student_id',
        'amount',
        'type',
        'reason',
        'fine_date',
        'status',
        'paid_amount',
        'paid_date',
        'collected_by',
        'payment_notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_amount' => 'float',
        'fine_date' => 'datetime',
        'paid_date' => 'datetime',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function bookIssue(): BelongsTo
    {
        return $this->belongsTo(BookIssue::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeWaived($query)
    {
        return $query->where('status', 'waived');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors & Methods
    public function getBalanceAmountAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->paid_amount >= $this->amount;
    }

    public function markPaid(float $amount, User $collectedBy, ?string $notes = null): bool
    {
        if ($amount <= 0 || $amount > $this->balance_amount) {
            return false;
        }

        $this->paid_amount = ($this->paid_amount ?? 0) + $amount;
        $this->paid_date = now();
        $this->collected_by = $collectedBy->id;
        $this->payment_notes = $notes;

        if ($this->is_fully_paid) {
            $this->status = 'paid';
        }

        return $this->save();
    }

    public function waive(User $waivedBy, ?string $reason = null): bool
    {
        $this->status = 'waived';
        $this->collected_by = $waivedBy->id;
        $this->payment_notes = "Waived: " . ($reason ?? 'No reason provided');

        return $this->save();
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '₹' . number_format($this->balance_amount, 2);
    }
}
