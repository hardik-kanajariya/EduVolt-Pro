<?php

namespace App\Filament\Admin\Resources\EmailLogResource\Pages;

use App\Filament\Admin\Resources\EmailLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailLogs extends ListRecords
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for logs - they're created automatically
        ];
    }
}
