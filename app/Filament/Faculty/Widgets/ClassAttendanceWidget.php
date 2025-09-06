<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassAttendanceWidget extends ChartWidget
{
    protected static ?string $heading = 'Class Attendance Trends';

    protected function getData(): array
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return ['datasets' => [], 'labels' => []];
        }

        // Get classes taught by this teacher
        $classIds = \App\Models\Timetable::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('class_id');

        $startOfWeek = Carbon::now()->startOfWeek();
        $data = [];
        $labels = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $labels[] = $date->format('M d');

            $totalStudents = Student::whereIn('class_id', $classIds)
                ->where('status', 'active')
                ->count();

            $presentStudents = Attendance::whereIn('student_id', function ($query) use ($classIds) {
                $query->select('id')
                    ->from('students')
                    ->whereIn('class_id', $classIds);
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
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
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
