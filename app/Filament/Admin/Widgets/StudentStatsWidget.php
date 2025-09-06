<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $currentAcademicYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();

        $totalStudents = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $newAdmissions = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereMonth('admission_date', now()->month)
            ->count();

        $totalClasses = SchoolClass::where('school_id', $schoolId)
            ->where('academic_year_id', $currentAcademicYear?->id)
            ->count();

        $maleStudents = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->where('gender', 'male');
            })
            ->count();

        $femaleStudents = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->where('gender', 'female');
            })
            ->count();

        return [
            Stat::make('Total Students', $totalStudents)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('New Admissions', $newAdmissions)
                ->chart([1, 3, 2, 4, 1, 2, 3])
                ->color('info'),

            Stat::make('Total Classes', $totalClasses)
                ->chart([1, 1, 2, 1, 1, 1, 2])
                ->color('warning'),

            Stat::make('Gender Ratio', $maleStudents . ':' . $femaleStudents)
                ->chart([1, 1, 1, 1, 1, 1, 1])
                ->color('primary'),
        ];
    }
}
