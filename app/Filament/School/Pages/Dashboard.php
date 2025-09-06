<?php

namespace App\Filament\School\Pages;

use App\Filament\School\Widgets\SchoolOverviewWidget;
use App\Filament\School\Widgets\SchoolAttendanceWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            SchoolOverviewWidget::class,
            SchoolAttendanceWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
