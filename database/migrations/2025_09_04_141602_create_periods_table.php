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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "1st Period", "Break", "Lunch"
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['academic', 'break', 'lunch', 'assembly'])->default('academic');
            $table->integer('duration_minutes');
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_time', 'end_time']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
