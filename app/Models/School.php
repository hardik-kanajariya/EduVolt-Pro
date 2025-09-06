<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'established_date',
        'type',
        'status',
        'settings',
        'financial_settings',
        'subscription_plan',
        'subscription_expires_at',
        'monthly_fee_target',
    ];

    protected $casts = [
        'established_date' => 'date',
        'settings' => 'array',
        'financial_settings' => 'array',
        'subscription_expires_at' => 'datetime',
        'monthly_fee_target' => 'float',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schoolAdmins()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'school_admin');
        });
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function feeSettings()
    {
        return $this->hasOne(FeeSettings::class);
    }

    public function feeCategories()
    {
        return $this->hasMany(FeeCategory::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function schoolFinances()
    {
        return $this->hasMany(SchoolFinance::class);
    }

    public function paymentGateways()
    {
        return $this->hasMany(PaymentGatewaySetting::class);
    }

    public function smsGateways()
    {
        return $this->hasMany(SmsGatewaySetting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getCurrentAcademicYear()
    {
        return $this->academicYears()->where('is_current', true)->first();
    }

    /**
     * Get current month's financial data
     */
    public function getCurrentMonthFinance(): ?SchoolFinance
    {
        return SchoolFinance::getOrCreateCurrentMonth($this->id);
    }

    /**
     * Check if subscription is active
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_expires_at === null || 
               $this->subscription_expires_at->isFuture();
    }

    /**
     * Get active payment gateway
     */
    public function getActivePaymentGateway(): ?PaymentGatewaySetting
    {
        return PaymentGatewaySetting::getActiveGateway($this->id);
    }

    /**
     * Get active SMS gateway
     */
    public function getActiveSmsGateway(): ?SmsGatewaySetting
    {
        return SmsGatewaySetting::getActiveGateway($this->id);
    }
}
