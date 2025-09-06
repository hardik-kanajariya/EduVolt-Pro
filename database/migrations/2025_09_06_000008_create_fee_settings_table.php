<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('currency', 3)->default('USD'); // ISO currency code
            $table->string('currency_symbol', 5)->default('$');
            $table->integer('decimal_places')->default(2);
            $table->string('receipt_prefix', 10)->default('REC');
            $table->integer('receipt_number_length')->default(6);
            $table->integer('last_receipt_number')->default(0);
            $table->boolean('auto_generate_installments')->default(true);
            $table->integer('default_installments')->default(12); // Monthly installments
            $table->integer('grace_period_days')->default(7);
            $table->decimal('default_late_fee', 10, 2)->default(0);
            $table->enum('late_fee_calculation', ['per_day', 'fixed', 'percentage'])->default('fixed');
            $table->boolean('enable_partial_payments')->default(true);
            $table->boolean('enable_advance_payments')->default(true);
            $table->json('reminder_schedule')->nullable(); // Days before due date to send reminders
            $table->json('receipt_template_settings')->nullable();
            $table->json('notification_settings')->nullable();
            $table->timestamps();

            $table->unique('school_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_settings');
    }
};
