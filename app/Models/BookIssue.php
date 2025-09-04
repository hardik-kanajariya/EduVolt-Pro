<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'book_id',
        'student_id',
        'issued_by',
        'returned_by',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'condition_at_issue',
        'condition_at_return',
        'issue_notes',
        'return_notes',
        'renewal_count',
        'last_renewal_date',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'last_renewal_date' => 'datetime',
        'renewal_count' => 'integer',
    ];

    // Boot method to handle status updates
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($issue) {
            // Set default due date (14 days from issue)
            if (empty($issue->due_date)) {
                $issue->due_date = Carbon::parse($issue->issue_date)->addDays(14);
            }
        });

        static::updated(function ($issue) {
            // Update book copy counts when status changes
            $issue->book->updateCopyCounts();
            
            // Create fine if overdue
            if ($issue->status === 'returned' && $issue->return_date > $issue->due_date) {
                $issue->createOverdueFine();
            }
        });

        static::created(function ($issue) {
            // Update book copy counts
            $issue->book->updateCopyCounts();
        });
    }

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(LibraryFine::class, 'book_issue_id');
    }

    // Scopes
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
                    ->where('due_date', '<', now());
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeDueWithin($query, $days)
    {
        return $query->where('status', 'issued')
                    ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Accessors & Methods
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'issued' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    public function getDaysUntilDueAttribute(): int
    {
        if ($this->status !== 'issued') {
            return 0;
        }
        
        return $this->due_date->diffInDays(now(), false);
    }

    public function canRenew(): bool
    {
        return $this->status === 'issued' 
               && $this->renewal_count < 2 // Max 2 renewals
               && !$this->is_overdue
               && !$this->book->activeReservations()->exists();
    }

    public function renew(int $days = 14): bool
    {
        if (!$this->canRenew()) {
            return false;
        }

        $this->due_date = $this->due_date->addDays($days);
        $this->renewal_count += 1;
        $this->last_renewal_date = now();
        
        return $this->save();
    }

    public function markReturned(User $returnedBy, string $condition = 'good', ?string $notes = null): bool
    {
        $this->status = 'returned';
        $this->return_date = now();
        $this->returned_by = $returnedBy->id;
        $this->condition_at_return = $condition;
        $this->return_notes = $notes;
        
        return $this->save();
    }

    private function createOverdueFine(): void
    {
        if ($this->fines()->where('type', 'overdue')->exists()) {
            return; // Fine already exists
        }

        $overdueDays = $this->return_date->diffInDays($this->due_date);
        $fineAmount = $overdueDays * 2; // ₹2 per day

        LibraryFine::create([
            'school_id' => $this->school_id,
            'book_issue_id' => $this->id,
            'student_id' => $this->student_id,
            'amount' => $fineAmount,
            'type' => 'overdue',
            'reason' => "Book returned {$overdueDays} days late",
            'fine_date' => $this->return_date,
        ]);
    }
}
