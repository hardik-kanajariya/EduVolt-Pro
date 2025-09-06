<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'gateway_name',
        'display_name',
        'settings',
        'is_active',
        'is_global',
        'transaction_fee_percentage',
        'transaction_fee_fixed',
        'supported_currencies',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'transaction_fee_percentage' => 'float',
        'transaction_fee_fixed' => 'float',
        'supported_currencies' => 'array',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get active gateway for a school
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
     * Get all available gateways for a school
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
     * Calculate transaction fee for an amount
     */
    public function calculateTransactionFee(float $amount): float
    {
        $percentageFee = ($amount * $this->transaction_fee_percentage) / 100;
        return $percentageFee + $this->transaction_fee_fixed;
    }

    /**
     * Get gateway configuration for payment processing
     */
    public function getProcessingConfig(): array
    {
        $config = $this->settings;
        $config['gateway_name'] = $this->gateway_name;
        $config['display_name'] = $this->display_name;
        
        return $config;
    }

    /**
     * Test gateway connection
     */
    public function testConnection(): array
    {
        // This would implement actual gateway testing
        // For now, return a mock response
        return [
            'success' => true,
            'message' => 'Gateway connection successful',
            'gateway' => $this->gateway_name,
        ];
    }

    /**
     * Get supported gateways list
     */
    public static function getSupportedGateways(): array
    {
        return [
            'razorpay' => [
                'name' => 'Razorpay',
                'description' => 'Popular payment gateway for India',
                'required_fields' => ['key_id', 'key_secret'],
                'currencies' => ['INR'],
            ],
            'stripe' => [
                'name' => 'Stripe',
                'description' => 'Global payment platform',
                'required_fields' => ['publishable_key', 'secret_key'],
                'currencies' => ['USD', 'EUR', 'GBP', 'INR'],
            ],
            'paypal' => [
                'name' => 'PayPal',
                'description' => 'Digital payments platform',
                'required_fields' => ['client_id', 'client_secret'],
                'currencies' => ['USD', 'EUR', 'GBP'],
            ],
            'paytm' => [
                'name' => 'Paytm',
                'description' => 'Indian payment service',
                'required_fields' => ['merchant_id', 'merchant_key'],
                'currencies' => ['INR'],
            ],
        ];
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
