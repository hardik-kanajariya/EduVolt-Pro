<?php

namespace App\Services;

use App\Models\StudentProgress;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentProgressService
{
    /**
     * Update progress for a specific student, academic year, subject, and term
     */
    public function updateStudentProgress(
        int $studentId,
        int $academicYearId,
        int $subjectId,
        int $classId,
        string $term = 'first'
    ): StudentProgress {
        DB::beginTransaction();

        try {
            $progress = StudentProgress::firstOrCreate([
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId,
                'subject_id' => $subjectId,
                'class_id' => $classId,
                'term' => $term,
            ]);

            // Calculate all metrics
            $this->calculateAssignmentMetrics($progress);
            $this->calculateExamMetrics($progress);
            $this->calculateAttendanceMetrics($progress);
            $this->calculateOverallGrade($progress);
            $this->calculatePerformanceTrend($progress);
            $this->calculateBehavioralScore($progress);

            $progress->last_updated_at = now();
            $progress->updated_by = Auth::id();
            $progress->save();

            DB::commit();

            Log::info('Student progress updated successfully', [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'term' => $term,
                'overall_grade' => $progress->overall_grade,
            ]);

            return $progress;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update student progress', [
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update progress for all students in a class
     */
    public function updateClassProgress(
        int $classId,
        int $academicYearId,
        int $subjectId,
        string $term = 'first'
    ): int {
        $class = SchoolClass::with('students')->findOrFail($classId);
        $updated = 0;

        foreach ($class->students as $student) {
            try {
                $this->updateStudentProgress(
                    $student->id,
                    $academicYearId,
                    $subjectId,
                    $classId,
                    $term
                );
                $updated++;
            } catch (\Exception $e) {
                Log::warning('Failed to update progress for student', [
                    'student_id' => $student->id,
                    'class_id' => $classId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $updated;
    }

    /**
     * Update progress for all subjects of a student
     */
    public function updateStudentAllSubjects(
        int $studentId,
        int $academicYearId,
        string $term = 'first'
    ): int {
        $student = Student::with(['classes', 'classes.subjects'])->findOrFail($studentId);
        $updated = 0;

        foreach ($student->classes as $class) {
            foreach ($class->subjects as $subject) {
                try {
                    $this->updateStudentProgress(
                        $studentId,
                        $academicYearId,
                        $subject->id,
                        $class->id,
                        $term
                    );
                    $updated++;
                } catch (\Exception $e) {
                    Log::warning('Failed to update progress for subject', [
                        'student_id' => $studentId,
                        'subject_id' => $subject->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $updated;
    }

    /**
     * Calculate assignment-related metrics
     */
    protected function calculateAssignmentMetrics(StudentProgress $progress): void
    {
        // Get assignments for this subject and class
        $assignments = Assignment::where('subject_id', $progress->subject_id)
            ->where('class_id', $progress->class_id)
            ->get();

        $progress->total_assignments = $assignments->count();

        // Get student submissions
        $submissions = AssignmentSubmission::where('student_id', $progress->student_id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get();

        $progress->submitted_assignments = $submissions->count();
        $progress->late_submissions = $submissions->where('is_late', true)->count();

        // Calculate assignment average
        $graded_submissions = $submissions->whereNotNull('grade');
        if ($graded_submissions->count() > 0) {
            $progress->setAttribute('assignment_average', round($graded_submissions->avg('grade'), 2));
        } else {
            $progress->setAttribute('assignment_average', 0);
        }
    }

    /**
     * Calculate exam-related metrics
     */
    protected function calculateExamMetrics(StudentProgress $progress): void
    {
        // Get exams for this subject and class
        $exams = Exam::where('subject_id', $progress->subject_id)
            ->where('class_id', $progress->class_id)
            ->get();

        $progress->total_exams = $exams->count();

        // Get student exam marks
        $examMarks = ExamMark::where('student_id', $progress->student_id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->get();

        $progress->exams_taken = $examMarks->count();
        $progress->exams_passed = $examMarks->where('marks', '>=', 50)->count(); // Assuming 50 is pass mark

        // Calculate exam average
        if ($examMarks->count() > 0) {
            $progress->setAttribute('exam_average', round($examMarks->avg('marks'), 2));
        } else {
            $progress->setAttribute('exam_average', 0);
        }
    }

    /**
     * Calculate attendance-related metrics
     */
    protected function calculateAttendanceMetrics(StudentProgress $progress): void
    {
        // Get attendance records for this student and subject
        $attendance = Attendance::where('student_id', $progress->student_id)
            ->where('subject_id', $progress->subject_id)
            ->get();

        $progress->total_classes = $attendance->count();
        $progress->classes_attended = $attendance->where('status', 'present')->count();
        $progress->classes_absent = $attendance->where('status', 'absent')->count();
        $progress->classes_late = $attendance->where('status', 'late')->count();

        // Calculate attendance percentage
        if ($progress->total_classes > 0) {
            $progress->setAttribute('attendance_percentage', round(($progress->classes_attended / $progress->total_classes) * 100, 2));
        } else {
            $progress->setAttribute('attendance_percentage', 0);
        }
    }

    /**
     * Calculate overall grade using weighted average
     */
    protected function calculateOverallGrade(StudentProgress $progress): void
    {
        // Weighted calculation: 40% assignments, 60% exams
        $assignmentWeight = 0.4;
        $examWeight = 0.6;

        $overall = ($progress->assignment_average * $assignmentWeight) +
            ($progress->exam_average * $examWeight);

        $progress->setAttribute('overall_grade', round($overall, 2));

        // Calculate letter grade and GPA
        $this->calculateLetterGradeAndGPA($progress);
    }

    /**
     * Calculate letter grade and GPA based on overall grade
     */
    protected function calculateLetterGradeAndGPA(StudentProgress $progress): void
    {
        $grade = $progress->overall_grade;

        $progress->letter_grade = match (true) {
            $grade >= 90 => 'A+',
            $grade >= 85 => 'A',
            $grade >= 80 => 'A-',
            $grade >= 75 => 'B+',
            $grade >= 70 => 'B',
            $grade >= 65 => 'B-',
            $grade >= 60 => 'C+',
            $grade >= 55 => 'C',
            $grade >= 50 => 'C-',
            $grade >= 45 => 'D',
            default => 'F',
        };

        $progress->setAttribute('gpa', match (true) {
            $grade >= 90 => '4.00',
            $grade >= 85 => '3.70',
            $grade >= 80 => '3.30',
            $grade >= 75 => '3.00',
            $grade >= 70 => '2.70',
            $grade >= 65 => '2.30',
            $grade >= 60 => '2.00',
            $grade >= 55 => '1.70',
            $grade >= 50 => '1.30',
            $grade >= 45 => '1.00',
            default => '0.00',
        });
    }

    /**
     * Calculate performance trend
     */
    protected function calculatePerformanceTrend(StudentProgress $progress): void
    {
        // Get previous term progress
        $previousProgress = StudentProgress::where('student_id', $progress->student_id)
            ->where('subject_id', $progress->subject_id)
            ->where('academic_year_id', $progress->academic_year_id)
            ->where('term', '!=', $progress->term)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($previousProgress) {
            $progress->setAttribute('previous_grade', $previousProgress->overall_grade);
            $progress->setAttribute('grade_change', round($progress->overall_grade - $previousProgress->overall_grade, 2));

            $progress->performance_trend = match (true) {
                $progress->overall_grade >= 90 => 'excellent',
                $progress->grade_change > 10 => 'improving',
                $progress->grade_change < -10 => 'declining',
                $progress->overall_grade < 50 => 'needs_attention',
                default => 'stable',
            };
        } else {
            $progress->performance_trend = match (true) {
                $progress->overall_grade >= 90 => 'excellent',
                $progress->overall_grade < 50 => 'needs_attention',
                default => 'stable',
            };
        }
    }

    /**
     * Calculate behavioral score based on attendance and submissions
     */
    protected function calculateBehavioralScore(StudentProgress $progress): void
    {
        $score = 0;

        // Attendance contribution (40%)
        if ($progress->attendance_percentage >= 95) {
            $score += 40;
        } elseif ($progress->attendance_percentage >= 90) {
            $score += 35;
        } elseif ($progress->attendance_percentage >= 80) {
            $score += 30;
        } elseif ($progress->attendance_percentage >= 70) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Assignment submission rate contribution (30%)
        $submissionRate = $progress->total_assignments > 0
            ? ($progress->submitted_assignments / $progress->total_assignments) * 100
            : 0;

        if ($submissionRate >= 100) {
            $score += 30;
        } elseif ($submissionRate >= 90) {
            $score += 25;
        } elseif ($submissionRate >= 80) {
            $score += 20;
        } elseif ($submissionRate >= 70) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Punctuality contribution (20%) - based on late submissions and tardiness
        $lateRate = $progress->submitted_assignments > 0
            ? ($progress->late_submissions / $progress->submitted_assignments) * 100
            : 0;
        $lateClassRate = $progress->total_classes > 0
            ? ($progress->classes_late / $progress->total_classes) * 100
            : 0;

        $avgLateRate = ($lateRate + $lateClassRate) / 2;

        if ($avgLateRate <= 5) {
            $score += 20;
        } elseif ($avgLateRate <= 10) {
            $score += 15;
        } elseif ($avgLateRate <= 20) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Academic performance contribution (10%)
        if ($progress->overall_grade >= 90) {
            $score += 10;
        } elseif ($progress->overall_grade >= 80) {
            $score += 8;
        } elseif ($progress->overall_grade >= 70) {
            $score += 6;
        } elseif ($progress->overall_grade >= 60) {
            $score += 4;
        } else {
            $score += 2;
        }

        $progress->behavioral_score = min(100, $score);
    }

    /**
     * Generate insights and achievements
     */
    public function generateInsights(StudentProgress $progress): array
    {
        $insights = [];
        $achievements = [];
        $concerns = [];

        // Attendance insights
        if ($progress->attendance_percentage >= 95) {
            $achievements[] = 'Excellent Attendance';
        } elseif ($progress->attendance_percentage < 75) {
            $concerns[] = 'Poor Attendance';
            $insights[] = 'Student needs to improve attendance to succeed academically.';
        }

        // Academic performance insights
        if ($progress->overall_grade >= 90) {
            $achievements[] = 'Academic Excellence';
        } elseif ($progress->grade_change > 15) {
            $achievements[] = 'Significant Improvement';
        } elseif ($progress->grade_change < -15) {
            $concerns[] = 'Declining Performance';
            $insights[] = 'Recent decline in academic performance requires attention.';
        }

        // Assignment insights
        $submissionRate = $progress->total_assignments > 0
            ? ($progress->submitted_assignments / $progress->total_assignments) * 100
            : 0;

        if ($submissionRate == 100) {
            $achievements[] = 'Perfect Assignment Submission';
        } elseif ($submissionRate < 80) {
            $concerns[] = 'Incomplete Assignments';
            $insights[] = 'Student needs support with assignment completion.';
        }

        // Behavioral insights
        if ($progress->behavioral_score >= 90) {
            $achievements[] = 'Exemplary Behavior';
        } elseif ($progress->behavioral_score < 60) {
            $concerns[] = 'Behavioral Issues';
            $insights[] = 'Student behavior and engagement need improvement.';
        }

        return [
            'insights' => $insights,
            'achievements' => $achievements,
            'concerns' => $concerns,
        ];
    }

    /**
     * Get progress statistics for a class
     */
    public function getClassStatistics(int $classId, int $academicYearId, string $term = 'first'): array
    {
        $progressRecords = StudentProgress::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('term', $term)
            ->get();

        if ($progressRecords->isEmpty()) {
            return [
                'total_students' => 0,
                'average_grade' => 0,
                'pass_rate' => 0,
                'attendance_rate' => 0,
                'top_performers' => [],
                'struggling_students' => [],
            ];
        }

        $totalStudents = $progressRecords->count();
        $averageGrade = $progressRecords->avg('overall_grade');
        $passRate = ($progressRecords->where('overall_grade', '>=', 50)->count() / $totalStudents) * 100;
        $attendanceRate = $progressRecords->avg('attendance_percentage');

        $topPerformers = $progressRecords->sortByDesc('overall_grade')->take(5);
        $strugglingStudents = $progressRecords->where('overall_grade', '<', 50)->sortBy('overall_grade');

        return [
            'total_students' => $totalStudents,
            'average_grade' => round($averageGrade, 2),
            'pass_rate' => round($passRate, 2),
            'attendance_rate' => round($attendanceRate, 2),
            'top_performers' => $topPerformers->values(),
            'struggling_students' => $strugglingStudents->values(),
        ];
    }
}
