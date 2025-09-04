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
        Schema::table('student_progress', function (Blueprint $table) {
            // Add missing performance trend
            if (!Schema::hasColumn('student_progress', 'performance_trend')) {
                $table->enum('performance_trend', ['improving', 'declining', 'stable', 'excellent', 'needs_attention'])->nullable()->after('classes_late');
            }

            // Performance indicators
            if (!Schema::hasColumn('student_progress', 'previous_grade')) {
                $table->decimal('previous_grade', 5, 2)->nullable()->after('performance_trend');
                $table->decimal('grade_change', 5, 2)->nullable()->after('previous_grade');
            }

            // Behavioral indicators
            if (!Schema::hasColumn('student_progress', 'behavioral_score')) {
                $table->integer('behavioral_score')->nullable()->after('grade_change');
                $table->json('achievements')->nullable()->after('behavioral_score');
                $table->json('areas_of_concern')->nullable()->after('achievements');
            }

            // Enhanced feedback
            if (!Schema::hasColumn('student_progress', 'effort_level')) {
                $table->enum('effort_level', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable()->after('teacher_remarks');
                $table->enum('participation_level', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable()->after('effort_level');
            }

            // Timestamps and tracking
            if (!Schema::hasColumn('student_progress', 'last_updated_at')) {
                $table->timestamp('last_updated_at')->nullable()->after('participation_level');
                $table->foreignId('updated_by')->nullable()->constrained('users')->after('last_updated_at');
                $table->date('reporting_period_start')->nullable()->after('updated_by');
                $table->date('reporting_period_end')->nullable()->after('reporting_period_start');
            }

            // Rename teacher_remarks to teacher_comments if exists
            try {
                if (Schema::hasColumn('student_progress', 'teacher_remarks') && !Schema::hasColumn('student_progress', 'teacher_comments')) {
                    $table->renameColumn('teacher_remarks', 'teacher_comments');
                }
            } catch (Exception $e) {
                // If rename fails, ignore and continue
            }

            // Additional indexes for performance
            $table->index('performance_trend', 'idx_performance_trend');
            $table->index('overall_grade', 'idx_overall_grade');
        });
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['student_id', 'academic_year_id']);
            $table->dropIndex(['academic_year_id', 'subject_id']);
            $table->dropIndex(['class_id', 'subject_id']);
            $table->dropIndex(['performance_trend']);
            $table->dropIndex(['overall_grade']);

            // Drop columns
            $table->dropColumn([
                'academic_year_id',
                'class_id',
                'gpa',
                'total_assignments',
                'submitted_assignments',
                'late_submissions',
                'total_exams',
                'exams_taken',
                'exams_passed',
                'total_classes',
                'classes_attended',
                'classes_absent',
                'classes_late',
                'previous_grade',
                'grade_change',
                'behavioral_score',
                'achievements',
                'areas_of_concern',
                'effort_level',
                'participation_level',
                'last_updated_at',
                'updated_by',
                'reporting_period_start',
                'reporting_period_end'
            ]);
        });
    }
};
