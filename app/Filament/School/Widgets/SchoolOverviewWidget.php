<?php

namespace App\Filament\School\Widgets;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Staff;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SchoolOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $totalStudents = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalTeachers = Teacher::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $totalClasses = SchoolClass::where('school_id', $schoolId)
            ->count();

        $totalStaff = Staff::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        return [
            Stat::make('Total Students', $totalStudents)
                ->chart([100, 120, 140, 160, 180, 200, 220])
                ->color('success'),

            Stat::make('Total Teachers', $totalTeachers)
                ->chart([10, 12, 15, 18, 20, 22, 25])
                ->color('info'),

            Stat::make('Total Classes', $totalClasses)
                ->chart([8, 10, 12, 14, 16, 18, 20])
                ->color('warning'),

            Stat::make('Total Staff', $totalStaff)
                ->chart([5, 6, 8, 10, 12, 14, 16])
                ->color('primary'),
        ];
    }
}
