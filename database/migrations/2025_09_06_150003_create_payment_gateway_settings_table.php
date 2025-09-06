<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('gateway_name'); // razorpay, stripe, paypal, etc.
            $table->string('display_name');
            $table->json('settings'); // API keys, configurations
            $table->boolean('is_active')->default(false);
            $table->boolean('is_global')->default(false); // If this is a global setting
            $table->decimal('transaction_fee_percentage', 5, 2)->default(0);
            $table->decimal('transaction_fee_fixed', 10, 2)->default(0);
            $table->json('supported_currencies')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
            $table->index(['is_global', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
};
