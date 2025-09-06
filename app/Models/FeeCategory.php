<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'type',
        'applicable_classes',
        'is_recurring',
        'frequency',
        'due_day',
        'late_fee_amount',
        'late_fee_days',
        'late_fee_type',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'applicable_classes' => 'array',
        'settings' => 'array',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'late_fee_amount' => 'decimal:2',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function feeWaivers(): HasMany
    {
        return $this->hasMany(FeeWaiver::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where(function ($q) use ($classId) {
            $q->whereNull('applicable_classes')
                ->orWhereJsonContains('applicable_classes', $classId);
        });
    }

    public function scopeMandatory($query)
    {
        return $query->where('type', 'mandatory');
    }

    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }

    // Accessors & Mutators
    public function getFormattedLateFeeDaysAttribute()
    {
        return $this->late_fee_days . ' days';
    }

    public function getFormattedLateFeesAttribute()
    {
        if ($this->late_fee_type === 'percentage') {
            return $this->late_fee_amount . '%';
        }
        return $this->school->feeSettings->currency_symbol . number_format($this->late_fee_amount, 2);
    }

    // Methods
    public function isApplicableToClass($classId): bool
    {
        if (empty($this->applicable_classes)) {
            return true; // Applicable to all classes
        }

        return in_array($classId, $this->applicable_classes);
    }

    public function calculateLateFee($amount, $overdueDays): float
    {
        if ($overdueDays <= $this->late_fee_days) {
            return 0;
        }

        if ($this->late_fee_type === 'percentage') {
            return ($amount * $this->late_fee_amount) / 100;
        }

        return $this->late_fee_amount;
    }
}
