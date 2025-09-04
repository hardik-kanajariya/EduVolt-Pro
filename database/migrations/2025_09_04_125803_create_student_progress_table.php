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
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('term'); // 'term1', 'term2', 'annual'
            $table->year('academic_year');
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('assignment_average', 5, 2)->default(0);
            $table->decimal('exam_average', 5, 2)->default(0);
            $table->decimal('overall_grade', 5, 2)->default(0);
            $table->char('letter_grade', 2)->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->enum('conduct', ['excellent', 'good', 'satisfactory', 'needs_improvement'])->default('good');
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_id', 'term', 'academic_year']);
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
