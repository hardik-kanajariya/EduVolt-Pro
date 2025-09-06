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
        Schema::table('timetables', function (Blueprint $table) {
            $table->foreignId('school_id')->after('id')->constrained()->onDelete('cascade');

            // Update the unique constraint to include school_id
            $table->dropUnique('timetable_unique');
            $table->unique(['school_id', 'class_id', 'day_of_week', 'period_id', 'academic_year_id'], 'timetable_school_unique');

            // Add index for better performance
            $table->index(['school_id', 'teacher_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropUnique('timetable_school_unique');
            $table->dropIndex(['school_id', 'teacher_id', 'day_of_week']);
            $table->dropColumn('school_id');

            // Restore original unique constraint
            $table->unique(['class_id', 'day_of_week', 'period_id', 'academic_year_id'], 'timetable_unique');
        });
    }
};
