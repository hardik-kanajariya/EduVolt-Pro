<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'student_id',
        'school_id',
        'academic_year_id',
        'installment_ids',
        'total_amount',
        'late_fee_amount',
        'discount_amount',
        'adjustment_amount',
        'net_amount',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'payment_time',
        'collected_by',
        'status',
        'remarks',
        'payment_breakdown',
        'is_printed',
        'printed_at',
        'receipt_data',
    ];

    protected $casts = [
        'installment_ids' => 'array',
        'total_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_time' => 'datetime',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'payment_breakdown' => 'array',
        'receipt_data' => 'array',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function installments()
    {
        return FeeInstallment::whereIn('id', $this->installment_ids ?? []);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', now()->toDateString());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByCollector($query, $collectorId)
    {
        return $query->where('collected_by', $collectorId);
    }

    // Accessors
    public function getFormattedNetAmountAttribute()
    {
        $feeSettings = $this->school->feeSettings;
        return $feeSettings->currency_symbol . number_format($this->net_amount, $feeSettings->decimal_places);
    }

    public function getFormattedTotalAmountAttribute()
    {
        $feeSettings = $this->school->feeSettings;
        return $feeSettings->currency_symbol . number_format($this->total_amount, $feeSettings->decimal_places);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'card' => 'Card',
            'online' => 'Online',
            default => ucfirst($this->payment_method),
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber($payment->school_id);
            }

            if (empty($payment->payment_time)) {
                $payment->payment_time = now();
            }

            // Calculate net amount
            $payment->net_amount = $payment->total_amount
                + $payment->late_fee_amount
                - $payment->discount_amount
                + $payment->adjustment_amount;
        });

        static::created(function ($payment) {
            $payment->updateInstallments();
            $payment->cacheReceiptData();
        });
    }

    // Methods
    public static function generateReceiptNumber($schoolId): string
    {
        $school = School::find($schoolId);
        $feeSettings = $school->feeSettings;

        $feeSettings->increment('last_receipt_number');

        $number = str_pad(
            $feeSettings->last_receipt_number,
            $feeSettings->receipt_number_length,
            '0',
            STR_PAD_LEFT
        );

        return $feeSettings->receipt_prefix . '-' . now()->format('Y') . '-' . $number;
    }

    public function updateInstallments(): void
    {
        if (empty($this->installment_ids)) {
            return;
        }

        $installments = FeeInstallment::whereIn('id', $this->installment_ids)->get();
        $remainingAmount = $this->total_amount;

        foreach ($installments as $installment) {
            $payableAmount = min($remainingAmount, $installment->balance_amount);

            if ($payableAmount > 0) {
                $installment->makePayment($payableAmount, [
                    'payment_id' => $this->id,
                    'receipt_number' => $this->receipt_number,
                    'payment_method' => $this->payment_method,
                ]);

                $remainingAmount -= $payableAmount;
            }
        }
    }

    public function cacheReceiptData(): void
    {
        $receiptData = [
            'student' => [
                'name' => $this->student->user->name,
                'admission_number' => $this->student->admission_number,
                'class' => $this->student->schoolClass->name,
                'parent_name' => $this->student->parent_name,
            ],
            'school' => [
                'name' => $this->school->name,
                'address' => $this->school->address,
                'phone' => $this->school->phone,
                'email' => $this->school->email,
            ],
            'payment' => [
                'receipt_number' => $this->receipt_number,
                'payment_date' => $this->payment_date->format('d/m/Y'),
                'payment_time' => $this->payment_time->format('h:i A'),
                'payment_method' => $this->payment_method_label,
                'collected_by' => $this->collectedBy->name,
            ],
            'amounts' => [
                'total_amount' => $this->total_amount,
                'late_fee_amount' => $this->late_fee_amount,
                'discount_amount' => $this->discount_amount,
                'adjustment_amount' => $this->adjustment_amount,
                'net_amount' => $this->net_amount,
            ],
            'installments' => $this->getInstallmentDetails(),
        ];

        $this->update(['receipt_data' => $receiptData]);
    }

    public function getInstallmentDetails(): array
    {
        if (empty($this->installment_ids)) {
            return [];
        }

        return FeeInstallment::whereIn('id', $this->installment_ids)
            ->with(['studentFeeAssignment.feeStructure.feeCategory'])
            ->get()
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'name' => $installment->installment_name,
                    'category' => $installment->studentFeeAssignment->feeStructure->feeCategory->name,
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    public function markAsPrinted(): void
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && $this->payment_date >= now()->subDays(30);
    }

    public function generateQRCode(): string
    {
        // QR code data for verification
        $qrData = [
            'receipt' => $this->receipt_number,
            'student' => $this->student->admission_number,
            'amount' => $this->net_amount,
            'date' => $this->payment_date->format('Y-m-d'),
            'school' => $this->school->code,
        ];

        return base64_encode(json_encode($qrData));
    }
}
