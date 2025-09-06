<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'email_template_id',
        'sender_id',
        'recipient_criteria',
        'recipient_list',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'failed_count',
        'opened_count',
        'status',
        'priority',
        'scheduled_at',
        'started_at',
        'completed_at',
        'attachments',
        'notes',
    ];

    protected $casts = [
        'recipient_criteria' => 'array',
        'recipient_list' => 'array',
        'attachments' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'bulk_email_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDueForSending($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    // Helper methods
    public function buildRecipientList(): array
    {
        $criteria = $this->recipient_criteria;
        $recipients = [];

        // Build recipients based on criteria
        if (isset($criteria['roles'])) {
            foreach ($criteria['roles'] as $role) {
                $users = User::role($role)->get();
                foreach ($users as $user) {
                    $recipients[] = [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'type' => $role,
                    ];
                }
            }
        }

        if (isset($criteria['classes'])) {
            foreach ($criteria['classes'] as $classId) {
                // Get students from specific classes
                $students = Student::where('class_id', $classId)->with('user')->get();
                foreach ($students as $student) {
                    if ($student->user) {
                        $recipients[] = [
                            'id' => $student->user->id,
                            'email' => $student->user->email,
                            'name' => $student->user->name,
                            'type' => 'student',
                        ];
                    }

                    // Also include parents if criteria includes parents
                    if (isset($criteria['include_parents']) && $criteria['include_parents']) {
                        if ($student->parent_email) {
                            $recipients[] = [
                                'id' => null,
                                'email' => $student->parent_email,
                                'name' => $student->parent_name,
                                'type' => 'parent',
                            ];
                        }
                    }
                }
            }
        }

        if (isset($criteria['specific_emails'])) {
            foreach ($criteria['specific_emails'] as $email) {
                $recipients[] = [
                    'id' => null,
                    'email' => $email,
                    'name' => null,
                    'type' => 'custom',
                ];
            }
        }

        // Remove duplicates based on email
        $recipients = collect($recipients)->unique('email')->values()->toArray();

        // Update the bulk email with recipient list
        $this->update([
            'recipient_list' => $recipients,
            'total_recipients' => count($recipients),
        ]);

        return $recipients;
    }

    public function schedule(): void
    {
        $this->buildRecipientList();
        $this->update(['status' => 'scheduled']);
    }

    public function start(): void
    {
        $this->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getSuccessRate(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return ($this->delivered_count / $this->total_recipients) * 100;
    }

    public function getOpenRate(): float
    {
        if ($this->delivered_count === 0) {
            return 0;
        }

        return ($this->opened_count / $this->delivered_count) * 100;
    }

    public function incrementSentCount(): void
    {
        $this->increment('sent_count');
    }

    public function incrementDeliveredCount(): void
    {
        $this->increment('delivered_count');
    }

    public function incrementFailedCount(): void
    {
        $this->increment('failed_count');
    }

    public function incrementOpenedCount(): void
    {
        $this->increment('opened_count');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isDueForSending(): bool
    {
        return $this->status === 'scheduled' &&
            $this->scheduled_at &&
            $this->scheduled_at->isPast();
    }

    public function getProgress(): array
    {
        return [
            'total' => $this->total_recipients,
            'sent' => $this->sent_count,
            'delivered' => $this->delivered_count,
            'failed' => $this->failed_count,
            'opened' => $this->opened_count,
            'percentage' => $this->total_recipients > 0 ?
                ($this->sent_count / $this->total_recipients) * 100 : 0,
        ];
    }
}
