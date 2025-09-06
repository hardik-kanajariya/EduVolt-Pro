<?php

namespace App\Filament\Student\Widgets;

use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\FeeInstallment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return [];
        }

        // Calculate attendance percentage
        $totalAttendanceDays = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->count();

        $presentDays = Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->whereMonth('date', now()->month)
            ->count();

        $attendancePercentage = $totalAttendanceDays > 0 ?
            round(($presentDays / $totalAttendanceDays) * 100, 1) : 0;

        // Pending assignments
        $pendingAssignments = Assignment::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->count();

        // Latest grade average
        $averageGrade = Grade::where('student_id', $student->id)
            ->whereMonth('created_at', now()->month)
            ->avg('marks_obtained');

        // Pending fee amount
        $pendingFees = FeeInstallment::whereHas('studentFeeAssignment', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('status', 'pending')
            ->sum('amount');

        return [
            Stat::make('Attendance', $attendancePercentage . '%')
                ->chart([85, 87, 89, 88, 90, 92, 91])
                ->color($attendancePercentage >= 75 ? 'success' : 'danger'),

            Stat::make('Pending Assignments', $pendingAssignments)
                ->chart([5, 4, 6, 3, 2, 4, 3])
                ->color($pendingAssignments == 0 ? 'success' : 'warning'),

            Stat::make('Average Grade', round($averageGrade, 1) . '%')
                ->chart([78, 82, 85, 83, 87, 89, 86])
                ->color($averageGrade >= 60 ? 'success' : 'danger'),

            Stat::make('Pending Fees', '₹' . number_format($pendingFees))
                ->chart([5000, 4500, 4000, 3500, 3000, 2500, 2000])
                ->color($pendingFees == 0 ? 'success' : 'warning'),
        ];
    }
}
