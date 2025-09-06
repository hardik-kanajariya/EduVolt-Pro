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
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('school_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->after('school_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('exam_id')->after('teacher_id')->nullable()->constrained()->onDelete('set null');

            // Add indexes for better performance
            $table->index(['school_id', 'class_id', 'subject_id']);
            $table->index(['teacher_id', 'exam_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['exam_id']);
            $table->dropIndex(['school_id', 'class_id', 'subject_id']);
            $table->dropIndex(['teacher_id', 'exam_date']);
            $table->dropColumn(['school_id', 'teacher_id', 'exam_id']);
        });
    }
};
