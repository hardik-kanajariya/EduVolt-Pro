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
        Schema::create('sms_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('provider'); // twilio, aws_sns, nexmo, etc.
            $table->string('display_name');
            $table->json('settings'); // API credentials and configurations
            $table->boolean('is_active')->default(false);
            $table->boolean('is_global')->default(false); // If this is a global setting
            $table->decimal('cost_per_sms', 10, 4)->default(0);
            $table->json('supported_countries')->nullable();
            $table->integer('daily_limit')->default(1000);
            $table->integer('monthly_limit')->default(10000);
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
        Schema::dropIfExists('sms_gateway_settings');
    }
};
