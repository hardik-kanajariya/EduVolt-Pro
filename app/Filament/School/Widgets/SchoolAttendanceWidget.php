<?php

namespace App\Filament\School\Widgets;

use App\Models\Student;
use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SchoolAttendanceWidget extends ChartWidget
{
    protected static ?string $heading = 'School Attendance Trends';

    protected function getData(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $data = [];
        $labels = [];

        // Get attendance data for the current month
        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            if ($date > now()) break; // Don't show future dates

            $labels[] = $date->format('M d');

            $totalStudents = Student::where('school_id', $schoolId)
                ->where('status', 'active')
                ->count();

            $presentStudents = Attendance::whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
                ->where('date', $date->format('Y-m-d'))
                ->where('status', 'present')
                ->count();

            $attendancePercentage = $totalStudents > 0 ? ($presentStudents / $totalStudents) * 100 : 0;
            $data[] = round($attendancePercentage, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Attendance %',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
