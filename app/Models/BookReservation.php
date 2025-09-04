<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'book_id',
        'student_id',
        'reservation_date',
        'expiry_date',
        'status',
        'notes',
        'fulfilled_at',
        'fulfilled_by',
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'expiry_date' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    // Boot method to handle reservation logic
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            // Set default expiry date (7 days from reservation)
            if (empty($reservation->expiry_date)) {
                $reservation->expiry_date = Carbon::parse($reservation->reservation_date)->addDays(7);
            }
        });

        static::updated(function ($reservation) {
            // Update book copy counts when status changes
            $reservation->book->updateCopyCounts();
        });

        static::created(function ($reservation) {
            // Update book copy counts
            $reservation->book->updateCopyCounts();
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

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '<', now());
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeExpiringWithin($query, $days)
    {
        return $query->where('status', 'active')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    // Accessors & Methods
    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'active' && $this->expiry_date < now();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if ($this->status !== 'active') {
            return 0;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    public function canFulfill(): bool
    {
        return $this->status === 'active'
            && !$this->is_expired
            && $this->book->available_copies > 0;
    }

    public function fulfill(User $fulfilledBy): bool
    {
        if (!$this->canFulfill()) {
            return false;
        }

        $this->status = 'fulfilled';
        $this->fulfilled_at = now();
        $this->fulfilled_by = $fulfilledBy->id;

        return $this->save();
    }

    public function cancel(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $this->status = 'cancelled';
        return $this->save();
    }

    public function markExpired(): bool
    {
        if ($this->status !== 'active' || !$this->is_expired) {
            return false;
        }

        $this->status = 'expired';
        return $this->save();
    }
}
