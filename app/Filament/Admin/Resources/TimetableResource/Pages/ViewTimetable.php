<?php

namespace App\Filament\Admin\Resources\TimetableResource\Pages;

use App\Filament\Admin\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTimetable extends ViewRecord
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
