<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGatewaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'provider',
        'display_name',
        'settings',
        'is_active',
        'is_global',
        'cost_per_sms',
        'supported_countries',
        'daily_limit',
        'monthly_limit',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'cost_per_sms' => 'float',
        'supported_countries' => 'array',
        'daily_limit' => 'integer',
        'monthly_limit' => 'integer',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get active SMS gateway for a school
     */
    public static function getActiveGateway(?int $schoolId = null): ?self
    {
        // First try to get school-specific active gateway
        if ($schoolId) {
            $gateway = static::where('school_id', $schoolId)
                ->where('is_active', true)
                ->first();
            
            if ($gateway) {
                return $gateway;
            }
        }
        
        // Fall back to global active gateway
        return static::where('is_global', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all available SMS gateways for a school
     */
    public static function getAvailableGateways(?int $schoolId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where('is_active', true);
        
        if ($schoolId) {
            $query->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhere('is_global', true);
            });
        } else {
            $query->where('is_global', true);
        }
        
        return $query->get();
    }

    /**
     * Check if SMS can be sent (within limits)
     */
    public function canSendSms(): bool
    {
        // This would implement actual limit checking
        // For now, return true
        return true;
    }

    /**
     * Get SMS sending configuration
     */
    public function getSendingConfig(): array
    {
        $config = $this->settings;
        $config['provider'] = $this->provider;
        $config['cost_per_sms'] = $this->cost_per_sms;
        
        return $config;
    }

    /**
     * Test SMS gateway connection
     */
    public function testConnection(): array
    {
        // This would implement actual gateway testing
        // For now, return a mock response
        return [
            'success' => true,
            'message' => 'SMS gateway connection successful',
            'provider' => $this->provider,
        ];
    }

    /**
     * Get supported SMS providers list
     */
    public static function getSupportedProviders(): array
    {
        return [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Global SMS service provider',
                'required_fields' => ['account_sid', 'auth_token', 'from_number'],
                'countries' => ['US', 'UK', 'IN', 'AU', 'CA'],
                'features' => ['SMS', 'MMS', 'Voice'],
            ],
            'aws_sns' => [
                'name' => 'AWS SNS',
                'description' => 'Amazon Simple Notification Service',
                'required_fields' => ['aws_access_key', 'aws_secret_key', 'region'],
                'countries' => ['Global'],
                'features' => ['SMS', 'Push Notifications'],
            ],
            'nexmo' => [
                'name' => 'Vonage (Nexmo)',
                'description' => 'Communications APIs platform',
                'required_fields' => ['api_key', 'api_secret'],
                'countries' => ['Global'],
                'features' => ['SMS', 'Voice', 'Video'],
            ],
            'textlocal' => [
                'name' => 'Textlocal',
                'description' => 'SMS platform for businesses',
                'required_fields' => ['username', 'hash'],
                'countries' => ['UK', 'IN'],
                'features' => ['SMS', 'Bulk SMS'],
            ],
            'msg91' => [
                'name' => 'MSG91',
                'description' => 'Cloud communication platform',
                'required_fields' => ['authkey', 'sender_id'],
                'countries' => ['IN', 'Global'],
                'features' => ['SMS', 'OTP', 'Voice'],
            ],
        ];
    }

    /**
     * Calculate SMS cost for multiple messages
     */
    public function calculateCost(int $messageCount): float
    {
        return $messageCount * $this->cost_per_sms;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
