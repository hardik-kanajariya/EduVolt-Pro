<?php

namespace App\Filament\Parent\Pages;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ParentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.parent.pages.parent-dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Parent Dashboard';
    }

    public function getChildren()
    {
        return Student::whereHas('user', function ($query) {
            $query->where('email', Auth::user()->email);
        })->with(['user', 'schoolClass', 'school'])->get();
    }

    public function getChildrenStats()
    {
        $children = $this->getChildren();
        $stats = [];

        foreach ($children as $child) {
            $stats[$child->id] = [
                'child' => $child,
                'recent_grades' => $this->getRecentGrades($child),
                'attendance_percentage' => $this->getAttendancePercentage($child),
                'pending_fees' => $this->getPendingFees($child),
                'upcoming_assignments' => $this->getUpcomingAssignments($child),
                'overall_performance' => $this->getOverallPerformance($child),
            ];
        }

        return $stats;
    }

    protected function getRecentGrades($student)
    {
        return Grade::where('student_id', $student->id)
            ->with(['subject'])
            ->orderBy('exam_date', 'desc')
            ->take(5)
            ->get();
    }

    protected function getAttendancePercentage($student)
    {
        $total = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        if ($total === 0) return 0;

        $present = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 1);
    }

    protected function getPendingFees($student)
    {
        return FeeInstallment::whereHas('studentFeeAssignment', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(3)
            ->get();
    }

    protected function getUpcomingAssignments($student)
    {
        return Assignment::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->where('status', 'published')
            ->orderBy('due_date')
            ->take(5)
            ->get();
    }

    protected function getOverallPerformance($student)
    {
        $grades = Grade::where('student_id', $student->id)
            ->whereMonth('exam_date', now()->month)
            ->get();

        if ($grades->isEmpty()) {
            return [
                'average' => 0,
                'grade' => '-',
                'performance_level' => 'No data',
                'trend' => 'stable'
            ];
        }

        $average = $grades->avg('percentage');
        $grade = $this->calculateGradeFromPercentage($average);
        $performanceLevel = $this->getPerformanceLevel($average);

        // Calculate trend by comparing with previous month
        $previousGrades = Grade::where('student_id', $student->id)
            ->whereMonth('exam_date', now()->subMonth()->month)
            ->whereYear('exam_date', now()->subMonth()->year)
            ->get();

        $trend = 'stable';
        if ($previousGrades->isNotEmpty()) {
            $previousAverage = $previousGrades->avg('percentage');
            $diff = $average - $previousAverage;

            if ($diff > 5) $trend = 'improving';
            elseif ($diff < -5) $trend = 'declining';
        }

        return [
            'average' => round($average, 1),
            'grade' => $grade,
            'performance_level' => $performanceLevel,
            'trend' => $trend
        ];
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

    public function getTotalPendingFees()
    {
        $children = $this->getChildren();
        $total = 0;

        foreach ($children as $child) {
            $pendingFees = FeeInstallment::whereHas('studentFeeAssignment', function ($query) use ($child) {
                $query->where('student_id', $child->id);
            })
                ->where('status', 'pending')
                ->sum('balance_amount');

            $total += $pendingFees;
        }

        return $total;
    }

    public function getTotalUpcomingAssignments()
    {
        $children = $this->getChildren();
        $total = 0;

        foreach ($children as $child) {
            $assignments = Assignment::where('class_id', $child->class_id)
                ->where('due_date', '>=', now())
                ->where('status', 'published')
                ->count();

            $total += $assignments;
        }

        return $total;
    }

    public function getRecentPayments()
    {
        $children = $this->getChildren();
        $childrenIds = $children->pluck('id');

        return FeePayment::whereHas('feeInstallment.studentFeeAssignment', function ($query) use ($childrenIds) {
            $query->whereIn('student_id', $childrenIds);
        })
            ->with(['feeInstallment.studentFeeAssignment.student.user'])
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();
    }
}
