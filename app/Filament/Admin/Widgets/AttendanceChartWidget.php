<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Weekly Attendance Overview';

    protected function getData(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $startOfWeek = Carbon::now()->startOfWeek();
        $data = [];
        $labels = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
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
                    'label' => 'Attendance %',
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
