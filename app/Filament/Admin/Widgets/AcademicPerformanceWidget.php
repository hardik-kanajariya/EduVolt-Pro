<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Exam;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AcademicPerformanceWidget extends ChartWidget
{
    protected static ?string $heading = 'Academic Performance Overview';

    protected function getData(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        // Get average grades by subject for the last 6 subjects with grades
        $subjectGrades = Grade::whereHas('student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->with('subject')
            ->selectRaw('subject_id, AVG(marks_obtained) as avg_marks, AVG(total_marks) as avg_total')
            ->groupBy('subject_id')
            ->latest()
            ->take(6)
            ->get();

        $labels = $subjectGrades->pluck('subject.name')->toArray();
        $data = $subjectGrades->map(function ($grade) {
            return $grade->avg_total > 0 ?
                round(($grade->avg_marks / $grade->avg_total) * 100, 1) : 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Average Performance %',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
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
        return 'doughnut';
    }
}
