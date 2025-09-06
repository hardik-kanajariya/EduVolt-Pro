<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\Timetable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FacultyStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $totalTeachers = Teacher::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalStaff = Staff::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalSubjects = Subject::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $scheduledClasses = Timetable::whereHas('schoolClass', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
            ->where('day_of_week', now()->dayOfWeek)
            ->count();

        return [
            Stat::make('Total Teachers', $totalTeachers)
                ->chart([5, 10, 8, 12, 15, 18, 20])
                ->color('success'),

            Stat::make('Total Staff', $totalStaff)
                ->chart([2, 4, 3, 5, 6, 7, 8])
                ->color('info'),

            Stat::make('Subjects Offered', $totalSubjects)
                ->chart([8, 10, 12, 14, 16, 18, 20])
                ->color('warning'),

            Stat::make('Today\'s Classes', $scheduledClasses)
                ->chart([6, 8, 10, 12, 14, 16, 18])
                ->color('primary'),
        ];
    }
}
