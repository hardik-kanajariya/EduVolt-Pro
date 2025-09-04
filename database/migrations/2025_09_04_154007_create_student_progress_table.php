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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');

            // Academic metrics
            $table->decimal('assignment_average', 5, 2)->nullable();
            $table->decimal('exam_average', 5, 2)->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->decimal('overall_grade', 5, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->decimal('gpa', 3, 2)->nullable();

            // Progress tracking
            $table->integer('total_assignments')->default(0);
            $table->integer('submitted_assignments')->default(0);
            $table->integer('late_submissions')->default(0);
            $table->integer('total_exams')->default(0);
            $table->integer('exams_taken')->default(0);
            $table->integer('exams_passed')->default(0);

            // Attendance metrics
            $table->integer('total_classes')->default(0);
            $table->integer('classes_attended')->default(0);
            $table->integer('classes_absent')->default(0);
            $table->integer('classes_late')->default(0);

            // Performance indicators
            $table->enum('performance_trend', ['improving', 'declining', 'stable', 'excellent', 'needs_attention'])->nullable();
            $table->decimal('previous_grade', 5, 2)->nullable();
            $table->decimal('grade_change', 5, 2)->nullable();

            // Behavioral indicators
            $table->integer('behavioral_score')->nullable();
            $table->json('achievements')->nullable(); // Array of achievements/awards
            $table->json('areas_of_concern')->nullable(); // Areas needing improvement

            // Teacher feedback
            $table->text('teacher_comments')->nullable();
            $table->enum('effort_level', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();
            $table->enum('participation_level', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();

            // Timestamps and tracking
            $table->timestamp('last_updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->date('reporting_period_start');
            $table->date('reporting_period_end');

            $table->timestamps();

            // Indexes for performance
            $table->index(['student_id', 'academic_year_id']);
            $table->index(['academic_year_id', 'subject_id']);
            $table->index(['class_id', 'subject_id']);
            $table->index('performance_trend');
            $table->index('overall_grade');

            // Unique constraint to prevent duplicate records
            $table->unique(['student_id', 'academic_year_id', 'subject_id', 'reporting_period_start'], 'unique_progress_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
