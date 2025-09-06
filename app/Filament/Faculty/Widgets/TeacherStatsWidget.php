<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Student;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Timetable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return [];
        }

        $myStudents = Student::whereIn('class_id', function ($query) use ($teacher) {
            $query->select('class_id')
                ->from('timetables')
                ->where('teacher_id', $teacher->id)
                ->distinct();
        })->count();

        $myAssignments = Assignment::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->count();

        $todayClasses = Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', now()->dayOfWeek)
            ->count();

        $pendingSubmissions = Assignment::where('teacher_id', $teacher->id)
            ->where('due_date', '>=', now())
            ->withCount(['submissions' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->get()
            ->sum('submissions_count');

        return [
            Stat::make('My Students', $myStudents)
                ->chart([10, 15, 20, 25, 30, 28, 32])
                ->color('success'),

            Stat::make('Active Assignments', $myAssignments)
                ->chart([2, 4, 3, 5, 6, 7, 8])
                ->color('info'),

            Stat::make('Today\'s Classes', $todayClasses)
                ->chart([3, 4, 2, 5, 4, 3, 6])
                ->color('warning'),

            Stat::make('Pending Reviews', $pendingSubmissions)
                ->chart([15, 12, 18, 14, 16, 13, 20])
                ->color('danger'),
        ];
    }
}
