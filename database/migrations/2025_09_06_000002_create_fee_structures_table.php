<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('fee_category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Base amount
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('final_amount', 12, 2); // Calculated field
            $table->json('month_wise_amounts')->nullable(); // For different monthly amounts
            $table->date('effective_from');
            $table->date('effective_till')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Conditions for this fee structure
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id', 'class_id', 'fee_category_id'], 'unique_fee_structure');
            $table->index(['school_id', 'academic_year_id', 'is_active']);
            $table->index(['effective_from', 'effective_till']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_structures');
    }
};
