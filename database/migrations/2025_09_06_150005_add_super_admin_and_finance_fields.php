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
        // Add missing fields to users table for super admin functionality
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('status');
            $table->string('last_panel_accessed', 50)->nullable()->after('is_super_admin');
        });

        // Add financial and subscription fields to schools table
        Schema::table('schools', function (Blueprint $table) {
            $table->json('financial_settings')->nullable()->after('settings');
            $table->string('subscription_plan', 50)->default('basic')->after('financial_settings');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_plan');
            $table->decimal('monthly_fee_target', 15, 2)->default(0)->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'last_panel_accessed']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['financial_settings', 'subscription_plan', 'subscription_expires_at', 'monthly_fee_target']);
        });
    }
};
