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
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained('library_books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('reservation_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'fulfilled', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index(['expiry_date', 'status']);
            $table->unique(['book_id', 'student_id', 'status'], 'unique_active_reservation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reservations');
    }
};
