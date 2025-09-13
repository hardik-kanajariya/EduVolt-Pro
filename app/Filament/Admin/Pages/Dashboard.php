<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\SchoolSwitcher;
use App\Filament\Admin\Widgets\StudentStatsWidget;
use App\Filament\Admin\Widgets\FacultyStatsWidget;
use App\Filament\Admin\Widgets\FinancialStatsWidget;
use App\Filament\Admin\Widgets\AttendanceChartWidget;
use App\Filament\Admin\Widgets\RecentActivitiesWidget;
use App\Filament\Admin\Widgets\LibraryStatsWidget;
use App\Filament\Admin\Widgets\AcademicPerformanceWidget;
use App\Filament\Admin\Widgets\CommunicationWidget;
use App\Filament\Admin\Widgets\FeeCollectionWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            // SchoolSwitcher::class,
            StudentStatsWidget::class,
            FacultyStatsWidget::class,
            // FinancialStatsWidget::class,
            AttendanceChartWidget::class,
            // FeeCollectionWidget::class,
            LibraryStatsWidget::class,
            // AcademicPerformanceWidget::class,
            RecentActivitiesWidget::class,
            // CommunicationWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
