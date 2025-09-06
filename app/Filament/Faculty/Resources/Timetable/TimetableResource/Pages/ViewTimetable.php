<?php

namespace App\Filament\Faculty\Resources\Timetable\TimetableResource\Pages;

use App\Filament\Faculty\Resources\Timetable\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTimetable extends ViewRecord
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
