<?php

namespace App\Filament\Student\Pages;

use App\Filament\Student\Widgets\StudentStatsWidget;
use App\Filament\Student\Widgets\UpcomingAssignmentsWidget;
use App\Filament\Student\Widgets\NotificationsWidget;
use App\Filament\Student\Widgets\GradeProgressWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StudentStatsWidget::class,
            NotificationsWidget::class,
            GradeProgressWidget::class,
            UpcomingAssignmentsWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
