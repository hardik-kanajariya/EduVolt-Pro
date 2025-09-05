<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Create exam with subjects
     */
    public function createExamWithSubjects(array $examData, array $subjects): Exam
    {
        return DB::transaction(function () use ($examData, $subjects) {
            $exam = Exam::create($examData);

            foreach ($subjects as $subjectData) {
                $exam->examSubjects()->create($subjectData);
            }

            return $exam;
        });
    }

    /**
     * Get exam results for a class
     */
    public function getClassExamResults(int $examId, int $classId): Collection
    {
        return ExamMark::whereHas('examSubject', function ($query) use ($examId, $classId) {
            $query->where('exam_id', $examId)
                ->where('class_id', $classId);
        })
            ->with(['student', 'examSubject.subject'])
            ->get()
            ->groupBy('student_id');
    }

    /**
     * Get student exam results
     */
    public function getStudentExamResults(int $studentId, int $examId): Collection
    {
        return ExamMark::whereHas('examSubject', function ($query) use ($examId) {
            $query->where('exam_id', $examId);
        })
            ->where('student_id', $studentId)
            ->with(['examSubject.subject'])
            ->get();
    }

    /**
     * Calculate exam statistics
     */
    public function calculateExamStatistics(int $examSubjectId): array
    {
        $marks = ExamMark::where('exam_subject_id', $examSubjectId)
            ->where('is_absent', false)
            ->pluck('marks_obtained');

        if ($marks->isEmpty()) {
            return [
                'total_students' => 0,
                'appeared_students' => 0,
                'passed_students' => 0,
                'failed_students' => 0,
                'average_marks' => 0,
                'highest_marks' => 0,
                'lowest_marks' => 0,
                'pass_percentage' => 0,
            ];
        }

        $examSubject = ExamSubject::find($examSubjectId);
        $totalStudents = ExamMark::where('exam_subject_id', $examSubjectId)->count();
        $appearedStudents = $marks->count();
        $passedStudents = ExamMark::where('exam_subject_id', $examSubjectId)
            ->where('is_passed', true)
            ->count();

        return [
            'total_students' => $totalStudents,
            'appeared_students' => $appearedStudents,
            'passed_students' => $passedStudents,
            'failed_students' => $appearedStudents - $passedStudents,
            'average_marks' => round($marks->average(), 2),
            'highest_marks' => $marks->max(),
            'lowest_marks' => $marks->min(),
            'pass_percentage' => $appearedStudents > 0 ? round(($passedStudents / $appearedStudents) * 100, 2) : 0,
        ];
    }

    /**
     * Generate report card data for a student
     */
    public function generateReportCard(int $studentId, int $examId): array
    {
        $student = Student::with(['schoolClass', 'user'])->find($studentId);
        $exam = Exam::find($examId);

        $results = $this->getStudentExamResults($studentId, $examId);

        $totalMarks = 0;
        $obtainedMarks = 0;
        $subjectResults = [];

        foreach ($results as $result) {
            $subjectResults[] = [
                'subject' => $result->examSubject->subject->name,
                'max_marks' => $result->examSubject->max_marks,
                'obtained_marks' => $result->marks_obtained,
                'percentage' => $result->percentage,
                'grade' => $result->grade,
                'is_passed' => $result->is_passed,
                'is_absent' => $result->is_absent,
            ];

            if (!$result->is_absent) {
                $totalMarks += $result->examSubject->max_marks;
                $obtainedMarks += $result->marks_obtained;
            }
        }

        $overallPercentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $overallGrade = $this->calculateGrade($overallPercentage);

        return [
            'student' => $student,
            'exam' => $exam,
            'subject_results' => $subjectResults,
            'overall' => [
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $overallPercentage,
                'grade' => $overallGrade,
            ],
        ];
    }

    /**
     * Get exam rankings for a class
     */
    public function getClassRankings(int $examId, int $classId): Collection
    {
        $studentsWithMarks = DB::table('students')
            ->select(
                'students.id',
                'students.name',
                'students.admission_number',
                DB::raw('SUM(exam_marks.marks_obtained) as total_marks'),
                DB::raw('SUM(exam_subjects.max_marks) as total_max_marks'),
                DB::raw('ROUND((SUM(exam_marks.marks_obtained) / SUM(exam_subjects.max_marks)) * 100, 2) as percentage')
            )
            ->join('exam_marks', 'students.id', '=', 'exam_marks.student_id')
            ->join('exam_subjects', 'exam_marks.exam_subject_id', '=', 'exam_subjects.id')
            ->where('students.class_id', $classId)
            ->where('exam_subjects.exam_id', $examId)
            ->where('exam_marks.is_absent', false)
            ->groupBy('students.id', 'students.name', 'students.admission_number')
            ->orderBy('percentage', 'desc')
            ->get();

        return $studentsWithMarks->map(function ($student, $index) {
            $student->rank = $index + 1;
            $student->grade = $this->calculateGrade($student->percentage);
            return $student;
        });
    }

    /**
     * Get subject-wise performance analysis
     */
    public function getSubjectPerformanceAnalysis(int $examId): Collection
    {
        return ExamSubject::where('exam_id', $examId)
            ->with(['subject'])
            ->get()
            ->map(function ($examSubject) {
                $stats = $this->calculateExamStatistics($examSubject->id);
                return [
                    'subject' => $examSubject->subject->name,
                    'class' => $examSubject->schoolClass->name,
                    'statistics' => $stats,
                ];
            });
    }

    /**
     * Mark students absent for an exam subject
     */
    public function markAbsentStudents(int $examSubjectId, array $studentIds): void
    {
        foreach ($studentIds as $studentId) {
            ExamMark::updateOrCreate(
                [
                    'exam_subject_id' => $examSubjectId,
                    'student_id' => $studentId,
                ],
                [
                    'marks_obtained' => 0,
                    'percentage' => 0,
                    'grade' => 'F',
                    'is_passed' => false,
                    'is_absent' => true,
                    'entered_by' => auth()->user()->id,
                ]
            );
        }
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 30) return 'D';
        return 'F';
    }

    /**
     * Check if exam can be published
     */
    public function canPublishExam(int $examId): array
    {
        $exam = Exam::find($examId);

        if (!$exam) {
            return ['can_publish' => false, 'reasons' => ['Exam not found']];
        }

        $reasons = [];

        // Check if exam has subjects
        $subjectCount = $exam->examSubjects()->count();
        if ($subjectCount === 0) {
            $reasons[] = 'No subjects assigned to this exam';
        }

        // Check if all subjects have marks entered
        $subjectsWithoutMarks = $exam->examSubjects()
            ->whereDoesntHave('examMarks')
            ->count();

        if ($subjectsWithoutMarks > 0) {
            $reasons[] = "Marks not entered for {$subjectsWithoutMarks} subjects";
        }

        return [
            'can_publish' => empty($reasons),
            'reasons' => $reasons,
        ];
    }
}
