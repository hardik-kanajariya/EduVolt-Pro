<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AttendanceStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->format('Y-m-d');

        // Today's attendance
        $todayPresent = Attendance::where('date', $today)
            ->where('status', 'present')
            ->count();

        $todayTotal = Attendance::where('date', $today)->count();
        $todayPercentage = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 0;

        // This month's average attendance
        $monthlyAttendance = Attendance::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $monthlyTotal = array_sum($monthlyAttendance);
        $monthlyPresent = $monthlyAttendance['present'] ?? 0;
        $monthlyPercentage = $monthlyTotal > 0 ? round(($monthlyPresent / $monthlyTotal) * 100, 1) : 0;

        // Students with low attendance (below 75%)
        $lowAttendanceStudents = Student::whereHas('attendance', function ($query) {
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        })
            ->withCount([
                'attendance as total_days' => function ($query) {
                    $query->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year);
                },
                'attendance as present_days' => function ($query) {
                    $query->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->where('status', 'present');
                }
            ])
            ->get()
            ->filter(function ($student) {
                return $student->total_days > 0 &&
                    (($student->present_days / $student->total_days) * 100) < 75;
            })
            ->count();

        return [
            Stat::make('Today\'s Attendance', "{$todayPresent}/{$todayTotal}")
                ->description("{$todayPercentage}% Present")
                ->descriptionIcon($todayPercentage >= 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayPercentage >= 80 ? 'success' : ($todayPercentage >= 60 ? 'warning' : 'danger')),

            Stat::make('Monthly Average', "{$monthlyPercentage}%")
                ->description('This month\'s attendance')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($monthlyPercentage >= 80 ? 'success' : ($monthlyPercentage >= 60 ? 'warning' : 'danger')),

            Stat::make('Low Attendance', $lowAttendanceStudents)
                ->description('Students below 75%')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowAttendanceStudents > 0 ? 'danger' : 'success'),
        ];
    }
}
