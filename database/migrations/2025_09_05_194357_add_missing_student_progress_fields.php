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
            // Add missing fields that are in the model but not in database
            if (!Schema::hasColumn('student_progress', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('student_id')->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('student_progress', 'class_id')) {
                $table->foreignId('class_id')->nullable()->after('academic_year_id')->constrained('classes')->onDelete('cascade');
            }

            if (!Schema::hasColumn('student_progress', 'gpa')) {
                $table->decimal('gpa', 5, 2)->nullable()->after('letter_grade');
            }

            if (!Schema::hasColumn('student_progress', 'total_assignments')) {
                $table->integer('total_assignments')->default(0)->after('gpa');
            }

            if (!Schema::hasColumn('student_progress', 'submitted_assignments')) {
                $table->integer('submitted_assignments')->default(0)->after('total_assignments');
            }

            if (!Schema::hasColumn('student_progress', 'late_submissions')) {
                $table->integer('late_submissions')->default(0)->after('submitted_assignments');
            }

            if (!Schema::hasColumn('student_progress', 'total_exams')) {
                $table->integer('total_exams')->default(0)->after('late_submissions');
            }

            if (!Schema::hasColumn('student_progress', 'exams_taken')) {
                $table->integer('exams_taken')->default(0)->after('total_exams');
            }

            if (!Schema::hasColumn('student_progress', 'exams_passed')) {
                $table->integer('exams_passed')->default(0)->after('exams_taken');
            }

            if (!Schema::hasColumn('student_progress', 'total_classes')) {
                $table->integer('total_classes')->default(0)->after('exams_passed');
            }

            if (!Schema::hasColumn('student_progress', 'classes_attended')) {
                $table->integer('classes_attended')->default(0)->after('total_classes');
            }

            if (!Schema::hasColumn('student_progress', 'classes_absent')) {
                $table->integer('classes_absent')->default(0)->after('classes_attended');
            }

            if (!Schema::hasColumn('student_progress', 'classes_late')) {
                $table->integer('classes_late')->default(0)->after('classes_absent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['class_id']);

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
            ]);
        });
    }
};
