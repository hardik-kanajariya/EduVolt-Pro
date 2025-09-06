<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Tuition, Transport, Library, Sports, etc.
            $table->string('code')->unique(); // TUITION, TRANSPORT, etc.
            $table->text('description')->nullable();
            $table->enum('type', ['mandatory', 'optional', 'conditional'])->default('mandatory');
            $table->json('applicable_classes')->nullable(); // Which classes this applies to
            $table->boolean('is_recurring')->default(true); // Monthly, Yearly, etc.
            $table->enum('frequency', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time'])->default('monthly');
            $table->integer('due_day')->default(1); // Day of month when due
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->integer('late_fee_days')->default(7); // Grace period
            $table->enum('late_fee_type', ['fixed', 'percentage'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
            $table->unique(['school_id', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_categories');
    }
};
