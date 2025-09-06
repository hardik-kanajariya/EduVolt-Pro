<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_assignment_id')->constrained()->onDelete('cascade');
            $table->string('installment_name'); // 1st Installment, April 2025, etc.
            $table->integer('installment_number');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->date('late_fee_date')->nullable(); // When late fee kicks in
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'partially_paid', 'overdue', 'waived'])->default('pending');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2);
            $table->date('last_payment_date')->nullable();
            $table->boolean('is_late')->default(false);
            $table->json('payment_schedule')->nullable(); // For partial payments
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_fee_assignment_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['status', 'is_late']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_installments');
    }
};
