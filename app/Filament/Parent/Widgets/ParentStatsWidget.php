<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\FeeInstallment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParentStatsWidget extends BaseWidget
{
    public $selectedChild = null;

    protected function getStats(): array
    {
        $user = Auth::user();

        // Get children for this parent
        $children = Student::whereHas('user', function ($query) use ($user) {
            $query->where('parent_email', $user->email);
        })->orWhere('parent_email', $user->email)->get();

        if ($children->isEmpty()) {
            return [];
        }

        // Use first child if none selected
        $student = $this->selectedChild ?
            $children->find($this->selectedChild) :
            $children->first();

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

        // Total children count
        $totalChildren = $children->count();

        return [
            Stat::make('Total Children', $totalChildren)
                ->chart([1, 1, 1, 1, 1, 1, 1])
                ->color('info'),

            Stat::make($student->user->name . ' Attendance', $attendancePercentage . '%')
                ->chart([85, 87, 89, 88, 90, 92, 91])
                ->color($attendancePercentage >= 75 ? 'success' : 'danger'),

            Stat::make('Average Grade', round($averageGrade, 1) . '%')
                ->chart([78, 82, 85, 83, 87, 89, 86])
                ->color($averageGrade >= 60 ? 'success' : 'danger'),

            Stat::make('Pending Fees', '₹' . number_format($pendingFees))
                ->chart([5000, 4500, 4000, 3500, 3000, 2500, 2000])
                ->color($pendingFees == 0 ? 'success' : 'warning'),
        ];
    }
}
