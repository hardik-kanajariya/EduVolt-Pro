<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeWaiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_category_id',
        'waiver_type',
        'waiver_percentage',
        'waiver_amount',
        'waiver_method',
        'effective_from',
        'effective_till',
        'approved_by',
        'approval_date',
        'reason',
        'supporting_documents',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'waiver_percentage' => 'decimal:2',
        'waiver_amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_till' => 'date',
        'approval_date' => 'date',
        'supporting_documents' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeCategory(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    public function scopeByType($query, $type)
    {
        return $query->where('waiver_type', $type);
    }

    // Accessors
    public function getFormattedWaiverAmountAttribute()
    {
        $currency = $this->student->school->feeSettings->currency_symbol;

        if ($this->waiver_method === 'percentage') {
            return $this->waiver_percentage . '%';
        }

        return $currency . number_format($this->waiver_amount, 2);
    }

    public function getIsExpiredAttribute()
    {
        return $this->effective_till && $this->effective_till < now()->toDateString();
    }

    // Methods
    public function calculateWaiverAmount(float $baseAmount): float
    {
        if ($this->waiver_method === 'percentage') {
            return ($baseAmount * $this->waiver_percentage) / 100;
        }

        return min($this->waiver_amount, $baseAmount);
    }

    public function isEffectiveOn($date = null): bool
    {
        $date = $date ?? now()->toDateString();

        return $this->is_active &&
            $this->effective_from <= $date &&
            ($this->effective_till === null || $this->effective_till >= $date);
    }
}
