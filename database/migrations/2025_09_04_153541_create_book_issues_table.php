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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained('library_books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade'); // Librarian who issued
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null'); // Librarian who received
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost'])->default('issued');
            $table->enum('condition_at_issue', ['excellent', 'good', 'fair', 'poor'])->default('excellent');
            $table->enum('condition_at_return', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('issue_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->date('last_renewal_date')->nullable();
            $table->timestamps();
            
            $table->index(['due_date', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index(['school_id', 'issue_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
