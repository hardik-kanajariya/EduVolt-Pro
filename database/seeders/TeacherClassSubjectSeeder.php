<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherClassSubject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\School;

class TeacherClassSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools
        $schools = School::all();

        foreach ($schools as $school) {
            // Get current academic year for this school
            $academicYear = AcademicYear::where('school_id', $school->id)
                ->where('status', 'active')
                ->first();

            if (!$academicYear) {
                continue;
            }

            // Get all teachers for this school
            $teachers = Teacher::where('school_id', $school->id)
                ->where('status', 'active')
                ->get();

            // Get all classes for this school and academic year
            $classes = SchoolClass::where('school_id', $school->id)
                ->where('academic_year_id', $academicYear->id)
                ->where('status', 'active')
                ->get();

            // Get all subjects for this school
            $subjects = Subject::where('school_id', $school->id)
                ->where('status', 'active')
                ->get();

            if ($teachers->isEmpty() || $classes->isEmpty() || $subjects->isEmpty()) {
                continue;
            }

            // Assign teachers to classes and subjects
            foreach ($classes as $class) {
                // Ensure the class teacher is assigned to teach at least one subject
                if ($class->class_teacher_id) {
                    $classTeacher = $teachers->find($class->class_teacher_id);
                    if ($classTeacher) {
                        // Assign the class teacher to teach 2-3 random subjects to this class
                        $subjectsToAssign = $subjects->random(min(3, $subjects->count()));

                        foreach ($subjectsToAssign as $subject) {
                            TeacherClassSubject::firstOrCreate([
                                'teacher_id' => $classTeacher->id,
                                'class_id' => $class->id,
                                'subject_id' => $subject->id,
                                'school_id' => $school->id,
                                'academic_year_id' => $academicYear->id,
                            ], [
                                'status' => 'active',
                            ]);
                        }
                    }
                }

                // Assign other teachers to teach remaining subjects
                $remainingSubjects = $subjects->diff($subjectsToAssign ?? collect());
                foreach ($remainingSubjects as $subject) {
                    // Randomly assign a teacher to this subject for this class
                    $randomTeacher = $teachers->random();

                    TeacherClassSubject::firstOrCreate([
                        'teacher_id' => $randomTeacher->id,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'school_id' => $school->id,
                        'academic_year_id' => $academicYear->id,
                    ], [
                        'status' => 'active',
                    ]);
                }
            }
        }

        $this->command->info('Teacher-Class-Subject assignments created successfully!');
    }
}
