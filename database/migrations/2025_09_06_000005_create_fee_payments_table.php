<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->json('installment_ids'); // Array of paid installment IDs
            $table->decimal('total_amount', 12, 2);
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('adjustment_amount', 10, 2)->default(0); // +/- adjustments
            $table->decimal('net_amount', 12, 2); // Final amount paid
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'card', 'online'])->default('cash');
            $table->string('transaction_reference')->nullable(); // Cheque number, transaction ID, etc.
            $table->date('payment_date');
            $table->datetime('payment_time');
            $table->foreignId('collected_by')->constrained('users')->onDelete('cascade'); // Staff who collected
            $table->enum('status', ['completed', 'pending', 'failed', 'refunded', 'cancelled'])->default('completed');
            $table->text('remarks')->nullable();
            $table->json('payment_breakdown')->nullable(); // Detailed breakdown
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->json('receipt_data')->nullable(); // Cached receipt data
            $table->timestamps();

            $table->index(['student_id', 'payment_date']);
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['payment_date', 'status']);
            $table->index(['collected_by', 'payment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_payments');
    }
};
