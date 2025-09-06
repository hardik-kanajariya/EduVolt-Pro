<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFeeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'custom_amount',
        'discount_percentage',
        'discount_amount',
        'discount_type',
        'discount_reason',
        'final_amount',
        'assigned_date',
        'effective_from',
        'effective_till',
        'is_active',
        'special_conditions',
        'notes',
    ];

    protected $casts = [
        'custom_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'assigned_date' => 'date',
        'effective_from' => 'date',
        'effective_till' => 'date',
        'is_active' => 'boolean',
        'special_conditions' => 'array',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeInstallments(): HasMany
    {
        return $this->hasMany(FeeInstallment::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class, 'student_id', 'student_id')
            ->whereJsonContains('installment_ids', function ($query) {
                return $query->select('id')->from('fee_installments')
                    ->where('student_fee_assignment_id', $this->id);
            });
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

    // Accessors
    public function getFormattedFinalAmountAttribute()
    {
        return $this->feeStructure->school->feeSettings->currency_symbol . number_format($this->final_amount, 2);
    }

    public function getTotalPaidAmountAttribute()
    {
        return $this->feeInstallments->sum('paid_amount');
    }

    public function getTotalOutstandingAmountAttribute()
    {
        return $this->feeInstallments->sum('balance_amount');
    }

    public function getPaymentStatusAttribute()
    {
        $totalPaid = $this->total_paid_amount;
        $totalAmount = $this->final_amount;

        if ($totalPaid == 0) {
            return 'unpaid';
        } elseif ($totalPaid >= $totalAmount) {
            return 'paid';
        } else {
            return 'partially_paid';
        }
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($assignment) {
            $baseAmount = $assignment->custom_amount ?? $assignment->feeStructure->final_amount;

            if ($assignment->discount_type === 'percentage') {
                $assignment->discount_amount = ($baseAmount * $assignment->discount_percentage) / 100;
            }

            $assignment->final_amount = $baseAmount - $assignment->discount_amount;
        });

        static::created(function ($assignment) {
            $assignment->generateInstallments();
        });
    }

    // Methods
    public function generateInstallments(): void
    {
        $feeCategory = $this->feeStructure->feeCategory;
        $school = $this->feeStructure->school;
        $feeSettings = $school->feeSettings;

        if (!$feeSettings->auto_generate_installments) {
            return;
        }

        $installments = [];
        $frequency = $feeCategory->frequency;
        $totalAmount = $this->final_amount;

        switch ($frequency) {
            case 'yearly':
                $installments[] = [
                    'installment_name' => 'Annual Fee',
                    'installment_number' => 1,
                    'amount' => $totalAmount,
                    'due_date' => $this->effective_from,
                    'balance_amount' => $totalAmount,
                ];
                break;

            case 'half_yearly':
                for ($i = 1; $i <= 2; $i++) {
                    $dueDate = $this->effective_from->copy()->addMonths(($i - 1) * 6);
                    $installments[] = [
                        'installment_name' => "Half Yearly - $i",
                        'installment_number' => $i,
                        'amount' => $totalAmount / 2,
                        'due_date' => $dueDate,
                        'balance_amount' => $totalAmount / 2,
                    ];
                }
                break;

            case 'quarterly':
                for ($i = 1; $i <= 4; $i++) {
                    $dueDate = $this->effective_from->copy()->addMonths(($i - 1) * 3);
                    $installments[] = [
                        'installment_name' => "Quarter $i",
                        'installment_number' => $i,
                        'amount' => $totalAmount / 4,
                        'due_date' => $dueDate,
                        'balance_amount' => $totalAmount / 4,
                    ];
                }
                break;

            case 'monthly':
            default:
                $monthlyAmount = $totalAmount / 12;
                for ($i = 1; $i <= 12; $i++) {
                    $dueDate = $this->effective_from->copy()->addMonths($i - 1)->day($feeCategory->due_day);
                    $installments[] = [
                        'installment_name' => $dueDate->format('F Y'),
                        'installment_number' => $i,
                        'amount' => $monthlyAmount,
                        'due_date' => $dueDate,
                        'balance_amount' => $monthlyAmount,
                    ];
                }
                break;
        }

        foreach ($installments as $installmentData) {
            $this->feeInstallments()->create(array_merge($installmentData, [
                'student_fee_assignment_id' => $this->id,
            ]));
        }
    }

    public function recalculateAmounts(): void
    {
        $this->save(); // Triggers the boot method calculation

        // Update existing installments
        $this->feeInstallments()->delete();
        $this->generateInstallments();
    }
}
