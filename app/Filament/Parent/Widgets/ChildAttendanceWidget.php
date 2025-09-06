<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Student;
use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChildAttendanceWidget extends ChartWidget
{
    protected static ?string $heading = 'Children Attendance Overview';

    public $selectedChild = null;

    protected function getData(): array
    {
        $user = Auth::user();

        // Get children for this parent
        $children = Student::whereHas('user', function ($query) use ($user) {
            $query->where('parent_email', $user->email);
        })->orWhere('parent_email', $user->email)->get();

        if ($children->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $labels = [];
        $datasets = [];

        // Generate labels for the week
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $labels[] = $date->format('M d');
        }

        // Generate data for each child
        $colors = [
            ['bg' => 'rgba(59, 130, 246, 0.1)', 'border' => 'rgb(59, 130, 246)'],
            ['bg' => 'rgba(16, 185, 129, 0.1)', 'border' => 'rgb(16, 185, 129)'],
            ['bg' => 'rgba(245, 101, 101, 0.1)', 'border' => 'rgb(245, 101, 101)'],
            ['bg' => 'rgba(251, 191, 36, 0.1)', 'border' => 'rgb(251, 191, 36)'],
        ];

        foreach ($children->take(4) as $index => $child) {
            $data = [];

            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);

                $attendance = Attendance::where('student_id', $child->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();

                $data[] = $attendance && $attendance->status === 'present' ? 1 : 0;
            }

            $datasets[] = [
                'label' => $child->user->name,
                'data' => $data,
                'backgroundColor' => $colors[$index]['bg'],
                'borderColor' => $colors[$index]['border'],
                'borderWidth' => 2,
                'fill' => true,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
