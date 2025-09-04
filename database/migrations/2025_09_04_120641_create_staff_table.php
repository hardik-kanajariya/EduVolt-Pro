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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id');
            $table->string('employee_id')->unique();
            $table->string('position');
            $table->string('department');
            $table->date('join_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
            $table->json('responsibilities')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();
            
            $table->index(['employee_id']);
            $table->index(['school_id', 'department']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
