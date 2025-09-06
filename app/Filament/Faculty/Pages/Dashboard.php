<?php

namespace App\Filament\Faculty\Pages;

use App\Filament\Faculty\Widgets\TeacherStatsWidget;
use App\Filament\Faculty\Widgets\MySchedule;
use App\Filament\Faculty\Widgets\MyAssignmentsWidget;
use App\Filament\Faculty\Widgets\QuickActionsWidget;
use App\Filament\Faculty\Widgets\ClassAttendanceWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            TeacherStatsWidget::class,
            QuickActionsWidget::class,
            MySchedule::class,
            ClassAttendanceWidget::class,
            MyAssignmentsWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
