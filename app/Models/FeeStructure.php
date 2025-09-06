<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'class_id',
        'fee_category_id',
        'amount',
        'discount_amount',
        'additional_charges',
        'final_amount',
        'month_wise_amounts',
        'effective_from',
        'effective_till',
        'is_active',
        'conditions',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'month_wise_amounts' => 'array',
        'effective_from' => 'date',
        'effective_till' => 'date',
        'is_active' => 'boolean',
        'conditions' => 'array',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feeCategory(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function studentFeeAssignments(): HasMany
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_till')
                    ->orWhere('effective_till', '>=', $date);
            });
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return $this->school->feeSettings->currency_symbol . number_format($this->amount, 2);
    }

    public function getFormattedFinalAmountAttribute()
    {
        return $this->school->feeSettings->currency_symbol . number_format($this->final_amount, 2);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($feeStructure) {
            $feeStructure->final_amount = $feeStructure->amount
                - $feeStructure->discount_amount
                + $feeStructure->additional_charges;
        });
    }

    // Methods
    public function isEffectiveOn($date = null): bool
    {
        $date = $date ?? now()->toDateString();

        return $this->effective_from <= $date &&
            ($this->effective_till === null || $this->effective_till >= $date);
    }

    public function getAmountForMonth($month): float
    {
        if (empty($this->month_wise_amounts) || !isset($this->month_wise_amounts[$month])) {
            return $this->final_amount;
        }

        return $this->month_wise_amounts[$month];
    }

    public function calculateTotalForYear(): float
    {
        if ($this->feeCategory->frequency === 'yearly') {
            return $this->final_amount;
        }

        if ($this->feeCategory->frequency === 'monthly' && !empty($this->month_wise_amounts)) {
            return array_sum($this->month_wise_amounts);
        }

        $multiplier = match ($this->feeCategory->frequency) {
            'monthly' => 12,
            'quarterly' => 4,
            'half_yearly' => 2,
            default => 1,
        };

        return $this->final_amount * $multiplier;
    }
}
