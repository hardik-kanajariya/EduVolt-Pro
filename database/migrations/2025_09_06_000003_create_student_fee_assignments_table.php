<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_fee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained()->onDelete('cascade');
            $table->decimal('custom_amount', 12, 2)->nullable(); // Override amount for specific student
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'fixed', 'scholarship'])->nullable();
            $table->string('discount_reason')->nullable();
            $table->decimal('final_amount', 12, 2); // Final calculated amount
            $table->date('assigned_date');
            $table->date('effective_from');
            $table->date('effective_till')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('special_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'fee_structure_id'], 'unique_student_fee');
            $table->index(['student_id', 'is_active']);
            $table->index(['effective_from', 'effective_till']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_fee_assignments');
    }
};
