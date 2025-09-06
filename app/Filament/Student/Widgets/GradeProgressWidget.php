<?php

namespace App\Filament\Student\Widgets;

use App\Models\Grade;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GradeProgressWidget extends ChartWidget
{
    protected static ?string $heading = 'Grade Progress';

    protected function getData(): array
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return ['datasets' => [], 'labels' => []];
        }

        $grades = Grade::where('student_id', $student->id)
            ->with('subject')
            ->latest()
            ->take(6)
            ->get()
            ->reverse();

        $labels = $grades->pluck('subject.name')->toArray();
        $data = $grades->pluck('marks_obtained')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Marks Obtained',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 101, 101)',
                        'rgb(251, 191, 36)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
