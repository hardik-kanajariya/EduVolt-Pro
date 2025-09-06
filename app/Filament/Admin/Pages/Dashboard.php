<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\SchoolSwitcher;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            SchoolSwitcher::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
