<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'currency',
        'currency_symbol',
        'decimal_places',
        'receipt_prefix',
        'receipt_number_length',
        'last_receipt_number',
        'auto_generate_installments',
        'default_installments',
        'grace_period_days',
        'default_late_fee',
        'late_fee_calculation',
        'enable_partial_payments',
        'enable_advance_payments',
        'reminder_schedule',
        'receipt_template_settings',
        'notification_settings',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'receipt_number_length' => 'integer',
        'last_receipt_number' => 'integer',
        'auto_generate_installments' => 'boolean',
        'default_installments' => 'integer',
        'grace_period_days' => 'integer',
        'default_late_fee' => 'decimal:2',
        'enable_partial_payments' => 'boolean',
        'enable_advance_payments' => 'boolean',
        'reminder_schedule' => 'array',
        'receipt_template_settings' => 'array',
        'notification_settings' => 'array',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Accessors
    public function getFormattedCurrencyAttribute()
    {
        return $this->currency . ' (' . $this->currency_symbol . ')';
    }

    public function getReminderScheduleDefaultAttribute()
    {
        return $this->reminder_schedule ?? [7, 3, 1]; // Days before due date
    }

    // Methods
    public function formatAmount($amount): string
    {
        return $this->currency_symbol . number_format($amount, $this->decimal_places);
    }

    public function getNextReceiptNumber(): string
    {
        $this->increment('last_receipt_number');

        $number = str_pad(
            $this->last_receipt_number,
            $this->receipt_number_length,
            '0',
            STR_PAD_LEFT
        );

        return $this->receipt_prefix . '-' . now()->format('Y') . '-' . $number;
    }
}
