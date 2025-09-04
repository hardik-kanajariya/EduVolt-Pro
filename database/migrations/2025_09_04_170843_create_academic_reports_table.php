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
        Schema::create('academic_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('report_type', [
                'student_progress',
                'class_performance',
                'attendance_summary',
                'assignment_analysis',
                'exam_results',
                'behavioral_report',
                'comprehensive',
                'parent_report',
                'teacher_report',
                'admin_dashboard'
            ]);

            // Report scope
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('term')->nullable();

            // Date range
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();

            // Generation details
            $table->enum('status', ['pending', 'generating', 'completed', 'failed', 'scheduled'])->default('pending');
            $table->string('file_path')->nullable();
            $table->enum('file_format', ['pdf', 'excel', 'csv', 'html', 'json'])->default('pdf');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->nullable();

            // Configuration
            $table->json('parameters')->nullable(); // Report-specific parameters
            $table->json('summary_data')->nullable(); // Generated report summary

            // Scheduling
            $table->boolean('is_scheduled')->default(false);
            $table->enum('schedule_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'annually'])->nullable();
            $table->timestamp('next_generation')->nullable();
            $table->json('recipients')->nullable(); // Email recipients for scheduled reports

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['report_type', 'status']);
            $table->index(['academic_year_id', 'class_id']);
            $table->index(['is_scheduled', 'next_generation']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_reports');
    }
};
