<?php

namespace App\Console\Commands;

use App\Services\StudentProgressService;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateStudentProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'progress:update 
                            {--student_id= : Update progress for specific student}
                            {--class_id= : Update progress for specific class}
                            {--subject_id= : Update progress for specific subject}
                            {--academic_year_id= : Update progress for specific academic year}
                            {--term=first : Term to update (first, second, third, annual)}
                            {--all : Update progress for all students}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update student progress records based on assignments, exams, and attendance';

    protected StudentProgressService $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        parent::__construct();
        $this->progressService = $progressService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting student progress update...');

        $studentId = $this->option('student_id');
        $classId = $this->option('class_id');
        $subjectId = $this->option('subject_id');
        $academicYearId = $this->option('academic_year_id') ?? AcademicYear::where('is_current', true)->first()?->id;
        $term = $this->option('term');
        $updateAll = $this->option('all');

        if (!$academicYearId) {
            $this->error('No current academic year found. Please specify --academic_year_id');
            return 1;
        }

        $updated = 0;

        try {
            if ($updateAll) {
                $updated = $this->updateAllStudents($academicYearId, $term);
            } elseif ($studentId && $subjectId) {
                $updated = $this->updateSpecificStudentSubject($studentId, $academicYearId, $subjectId, $term);
            } elseif ($studentId) {
                $updated = $this->updateStudentAllSubjects($studentId, $academicYearId, $term);
            } elseif ($classId && $subjectId) {
                $updated = $this->updateClassSubject($classId, $academicYearId, $subjectId, $term);
            } elseif ($classId) {
                $updated = $this->updateClassAllSubjects($classId, $academicYearId, $term);
            } else {
                $this->error('Please specify valid options. Use --help for usage information.');
                return 1;
            }

            $this->info("Successfully updated progress for {$updated} student-subject combinations.");

            Log::info('Student progress update completed', [
                'updated_count' => $updated,
                'academic_year_id' => $academicYearId,
                'term' => $term,
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->error("Error updating student progress: {$e->getMessage()}");
            Log::error('Student progress update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }

    /**
     * Update all students in all classes and subjects
     */
    protected function updateAllStudents(int $academicYearId, string $term): int
    {
        $this->info('Updating progress for all students...');

        $classes = SchoolClass::with(['students', 'subjects'])->get();
        $updated = 0;

        $bar = $this->output->createProgressBar($classes->count());
        $bar->start();

        foreach ($classes as $class) {
            foreach ($class->subjects as $subject) {
                $classUpdated = $this->progressService->updateClassProgress(
                    $class->id,
                    $academicYearId,
                    $subject->id,
                    $term
                );
                $updated += $classUpdated;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $updated;
    }

    /**
     * Update specific student's specific subject
     */
    protected function updateSpecificStudentSubject(int $studentId, int $academicYearId, int $subjectId, string $term): int
    {
        $student = Student::with('classes')->findOrFail($studentId);
        $subject = Subject::findOrFail($subjectId);

        $this->info("Updating progress for {$student->name} in {$subject->name}...");

        // Find the class where this student and subject intersect
        $class = $student->classes()->whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subjects.id', $subjectId);
        })->first();

        if (!$class) {
            $this->error("Student {$student->name} is not enrolled in {$subject->name}");
            return 0;
        }

        $this->progressService->updateStudentProgress(
            $studentId,
            $academicYearId,
            $subjectId,
            $class->id,
            $term
        );

        return 1;
    }

    /**
     * Update specific student's all subjects
     */
    protected function updateStudentAllSubjects(int $studentId, int $academicYearId, string $term): int
    {
        $student = Student::findOrFail($studentId);

        $this->info("Updating progress for all subjects of {$student->name}...");

        return $this->progressService->updateStudentAllSubjects($studentId, $academicYearId, $term);
    }

    /**
     * Update specific class and subject
     */
    protected function updateClassSubject(int $classId, int $academicYearId, int $subjectId, string $term): int
    {
        $class = SchoolClass::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);

        $this->info("Updating progress for {$class->name} in {$subject->name}...");

        return $this->progressService->updateClassProgress($classId, $academicYearId, $subjectId, $term);
    }

    /**
     * Update specific class all subjects
     */
    protected function updateClassAllSubjects(int $classId, int $academicYearId, string $term): int
    {
        $class = SchoolClass::with('subjects')->findOrFail($classId);

        $this->info("Updating progress for all subjects in {$class->name}...");

        $updated = 0;
        foreach ($class->subjects as $subject) {
            $updated += $this->progressService->updateClassProgress(
                $classId,
                $academicYearId,
                $subject->id,
                $term
            );
        }

        return $updated;
    }
}
