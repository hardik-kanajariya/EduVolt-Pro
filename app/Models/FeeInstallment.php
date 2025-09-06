<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_assignment_id',
        'installment_name',
        'installment_number',
        'amount',
        'due_date',
        'late_fee_date',
        'late_fee_amount',
        'status',
        'paid_amount',
        'balance_amount',
        'last_payment_date',
        'is_late',
        'payment_schedule',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'due_date' => 'date',
        'late_fee_date' => 'date',
        'last_payment_date' => 'date',
        'is_late' => 'boolean',
        'payment_schedule' => 'array',
    ];

    // Relationships
    public function studentFeeAssignment(): BelongsTo
    {
        return $this->belongsTo(StudentFeeAssignment::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(Student::class, StudentFeeAssignment::class, 'id', 'id', 'student_fee_assignment_id', 'student_id');
    }

    public function feeReminders(): HasMany
    {
        return $this->hasMany(FeeReminder::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', 'paid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partially_paid');
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        $feeSettings = $this->studentFeeAssignment->feeStructure->school->feeSettings;
        return $feeSettings->currency_symbol . number_format($this->amount, $feeSettings->decimal_places);
    }

    public function getFormattedBalanceAmountAttribute()
    {
        $feeSettings = $this->studentFeeAssignment->feeStructure->school->feeSettings;
        return $feeSettings->currency_symbol . number_format($this->balance_amount, $feeSettings->decimal_places);
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->due_date >= now()->toDateString()) {
            return 0;
        }

        return Carbon::parse($this->due_date)->diffInDays(now());
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now()->toDateString() && $this->status !== 'paid';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'paid' => 'success',
            'partially_paid' => 'warning',
            'overdue' => 'danger',
            'pending' => $this->is_overdue ? 'danger' : 'info',
            'waived' => 'secondary',
            default => 'secondary',
        };
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($installment) {
            // Calculate balance amount
            $installment->balance_amount = $installment->amount - $installment->paid_amount;

            // Update status based on payment
            if ($installment->paid_amount >= $installment->amount) {
                $installment->status = 'paid';
            } elseif ($installment->paid_amount > 0) {
                $installment->status = 'partially_paid';
            } elseif ($installment->due_date < now()->toDateString()) {
                $installment->status = 'overdue';
            } else {
                $installment->status = 'pending';
            }

            // Mark as late if overdue
            if ($installment->due_date < now()->toDateString() && $installment->status !== 'paid') {
                $installment->is_late = true;
                $installment->calculateLateFee();
            }
        });
    }

    // Methods
    public function makePayment(float $amount, array $paymentData = []): bool
    {
        if ($amount <= 0 || $amount > $this->balance_amount) {
            return false;
        }

        $this->paid_amount += $amount;
        $this->last_payment_date = now()->toDateString();

        // Add to payment schedule
        $schedule = $this->payment_schedule ?? [];
        $schedule[] = [
            'amount' => $amount,
            'date' => now()->toDateString(),
            'payment_data' => $paymentData,
        ];
        $this->payment_schedule = $schedule;

        $this->save();

        return true;
    }

    public function calculateLateFee(): void
    {
        if (!$this->is_late || $this->status === 'paid') {
            $this->late_fee_amount = 0;
            return;
        }

        $feeCategory = $this->studentFeeAssignment->feeStructure->feeCategory;
        $overdueDays = $this->days_overdue;

        $this->late_fee_amount = $feeCategory->calculateLateFee($this->balance_amount, $overdueDays);
        $this->late_fee_date = $this->due_date->addDays($feeCategory->late_fee_days);
    }

    public function getTotalAmountDue(): float
    {
        return $this->balance_amount + $this->late_fee_amount;
    }

    public function canMakePartialPayment(): bool
    {
        $feeSettings = $this->studentFeeAssignment->feeStructure->school->feeSettings;
        return $feeSettings->enable_partial_payments;
    }

    public function sendReminder(string $type = 'email'): void
    {
        $reminderNumber = $this->feeReminders()->count() + 1;
        $student = $this->studentFeeAssignment->student;
        $school = $this->studentFeeAssignment->feeStructure->school;

        $reminder = $this->feeReminders()->create([
            'student_id' => $this->studentFeeAssignment->student_id,
            'reminder_type' => $type,
            'reminder_number' => $reminderNumber,
            'due_date' => $this->due_date,
            'sent_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        try {
            if ($type === 'email') {
                $this->sendEmailReminder($student, $school, $reminder);
            } elseif ($type === 'sms') {
                $this->sendSMSReminder($student, $school, $reminder);
            }

            $reminder->update(['status' => 'sent']);
        } catch (\Exception $e) {
            $reminder->update([
                'status' => 'failed',
                'notes' => 'Failed to send: ' . $e->getMessage()
            ]);

            // Log the error
            Log::error('Fee reminder sending failed', [
                'installment_id' => $this->id,
                'student_id' => $student->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendEmailReminder($student, $school, $reminder): void
    {
        $emailContent = $this->generateEmailContent($student, $school);

        // Send email using Laravel's mail system
        Mail::send('emails.fee-reminder', [
            'student' => $student,
            'school' => $school,
            'installment' => $this,
            'reminder' => $reminder,
            'content' => $emailContent
        ], function ($message) use ($student, $school) {
            $message->to($student->email, $student->name)
                ->subject("Fee Payment Reminder - {$school->name}")
                ->from($school->email, $school->name);
        });
    }

    private function sendSMSReminder($student, $school, $reminder): void
    {
        $smsContent = $this->generateSMSContent($student, $school);

        // SMS sending logic would depend on your SMS provider
        // For now, we'll log it as a placeholder
        Log::info('SMS Reminder', [
            'to' => $student->phone,
            'content' => $smsContent,
            'student_id' => $student->id,
            'installment_id' => $this->id
        ]);
    }

    private function generateEmailContent($student, $school): array
    {
        return [
            'greeting' => "Dear {$student->name},",
            'subject' => 'Fee Payment Reminder',
            'body' => "This is a friendly reminder that your fee payment for {$this->installment_name} is due on {$this->due_date->format('d M Y')}.",
            'amount' => "Amount Due: {$this->formatted_balance_amount}",
            'late_fee' => $this->late_fee_amount > 0 ? "Late Fee: ₹" . number_format($this->late_fee_amount, 2) : null,
            'instructions' => 'Please make the payment at your earliest convenience to avoid any late fees.',
            'contact' => "For any queries, please contact us at {$school->phone} or {$school->email}.",
            'closing' => "Thank you,\n{$school->name}"
        ];
    }

    private function generateSMSContent($student, $school): string
    {
        $amount = $this->formatted_balance_amount;
        $dueDate = $this->due_date->format('d-M-Y');

        return "Dear {$student->name}, Fee payment reminder: {$this->installment_name} - {$amount} due on {$dueDate}. Pay at school office. -{$school->name}";
    }
}
