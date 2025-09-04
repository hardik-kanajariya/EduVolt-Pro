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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id');
            $table->string('employee_id')->unique();
            $table->string('qualification');
            $table->integer('experience_years')->default(0);
            $table->date('join_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
            $table->text('specialization')->nullable();
            $table->json('certifications')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();

            $table->index(['employee_id']);
            $table->index(['school_id', 'status']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
