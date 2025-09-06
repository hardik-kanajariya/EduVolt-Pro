<?php

namespace App\Filament\Student\Pages;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\FeeInstallment;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.student.pages.student-dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Student Dashboard';
    }

    public function getStudent()
    {
        return Auth::user()->student;
    }

    public function getRecentGrades()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        return Grade::where('student_id', $student->id)
            ->with(['subject'])
            ->orderBy('exam_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getAttendanceStats()
    {
        $student = $this->getStudent();
        if (!$student) return ['percentage' => 0, 'present' => 0, 'total' => 0];

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $total = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        if ($total === 0) return ['percentage' => 0, 'present' => 0, 'total' => 0];

        $present = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'present')
            ->count();

        return [
            'percentage' => round(($present / $total) * 100, 1),
            'present' => $present,
            'total' => $total
        ];
    }

    public function getUpcomingAssignments()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        return Assignment::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->where('status', 'published')
            ->with(['subject'])
            ->orderBy('due_date')
            ->take(5)
            ->get();
    }

    public function getPendingAssignments()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        $assignmentIds = Assignment::where('class_id', $student->class_id)
            ->where('status', 'published')
            ->pluck('id');

        $submittedIds = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignmentIds)
            ->pluck('assignment_id');

        return Assignment::whereIn('id', $assignmentIds)
            ->whereNotIn('id', $submittedIds)
            ->where('due_date', '>=', now())
            ->with(['subject'])
            ->orderBy('due_date')
            ->get();
    }

    public function getOverallPerformance()
    {
        $student = $this->getStudent();
        if (!$student) return ['average' => 0, 'grade' => '-', 'performance_level' => 'No data'];

        $grades = Grade::where('student_id', $student->id)
            ->whereMonth('exam_date', now()->month)
            ->whereYear('exam_date', now()->year)
            ->get();

        if ($grades->isEmpty()) {
            return ['average' => 0, 'grade' => '-', 'performance_level' => 'No data'];
        }

        $average = $grades->avg('percentage');
        $grade = $this->calculateGradeFromPercentage($average);
        $performanceLevel = $this->getPerformanceLevel($average);

        return [
            'average' => round($average, 1),
            'grade' => $grade,
            'performance_level' => $performanceLevel
        ];
    }

    public function getSubjectPerformance()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        $subjects = $student->schoolClass->subjects ?? collect();
        $performance = [];

        foreach ($subjects as $subject) {
            $grades = Grade::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->orderBy('exam_date', 'desc')
                ->take(3)
                ->get();

            if ($grades->isNotEmpty()) {
                $average = $grades->avg('percentage');
                $performance[] = [
                    'subject' => $subject,
                    'average' => round($average, 1),
                    'grade' => $this->calculateGradeFromPercentage($average),
                    'latest_grade' => $grades->first()->grade,
                    'total_exams' => $grades->count()
                ];
            }
        }

        return collect($performance);
    }

    public function getPendingFees()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        return FeeInstallment::whereHas('studentFeeAssignment', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(3)
            ->get();
    }

    public function getAcademicCalendar()
    {
        $student = $this->getStudent();
        if (!$student) return collect();

        $events = collect();

        // Add upcoming assignment due dates
        $assignments = Assignment::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->where('status', 'published')
            ->with(['subject'])
            ->orderBy('due_date')
            ->get();

        foreach ($assignments as $assignment) {
            $events->push([
                'type' => 'assignment',
                'title' => $assignment->title,
                'subject' => $assignment->subject->name,
                'date' => $assignment->due_date,
                'color' => 'blue'
            ]);
        }

        // Add fee due dates
        $fees = $this->getPendingFees();
        foreach ($fees as $fee) {
            $events->push([
                'type' => 'fee',
                'title' => 'Fee Due: ' . $fee->installment_name,
                'subject' => 'Finance',
                'date' => $fee->due_date,
                'color' => 'red'
            ]);
        }

        return $events->sortBy('date');
    }

    protected function calculateGradeFromPercentage($percentage)
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C+',
            $percentage >= 40 => 'C',
            $percentage >= 33 => 'D',
            default => 'F',
        };
    }

    protected function getPerformanceLevel($percentage)
    {
        return match (true) {
            $percentage >= 90 => 'Excellent',
            $percentage >= 75 => 'Good',
            $percentage >= 60 => 'Satisfactory',
            $percentage >= 40 => 'Needs Improvement',
            default => 'Poor',
        };
    }
}
