<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_installment_id',
        'reminder_type',
        'reminder_number',
        'due_date',
        'sent_date',
        'status',
        'message_content',
        'recipient_contact',
        'delivery_details',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_date' => 'date',
        'delivery_details' => 'array',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeInstallment(): BelongsTo
    {
        return $this->belongsTo(FeeInstallment::class);
    }

    // Scopes
    public function scopeEmail($query)
    {
        return $query->where('reminder_type', 'email');
    }

    public function scopeSms($query)
    {
        return $query->where('reminder_type', 'sms');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
