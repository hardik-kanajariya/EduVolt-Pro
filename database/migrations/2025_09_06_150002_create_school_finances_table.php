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
        Schema::create('school_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('month_year', 7); // Format: 2024-09
            $table->decimal('revenue', 15, 2)->default(0);
            $table->decimal('expenses', 15, 2)->default(0);
            $table->decimal('profit_loss', 15, 2)->default(0);
            $table->decimal('fee_collection', 15, 2)->default(0);
            $table->decimal('salary_expenses', 15, 2)->default(0);
            $table->decimal('operational_expenses', 15, 2)->default(0);
            $table->json('breakdown')->nullable(); // Detailed breakdown
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['school_id', 'month_year']);
            $table->index(['month_year', 'school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_finances');
    }
};
