<?php

namespace App\Filament\Faculty\Resources\Timetable\TimetableResource\Pages;

use App\Filament\Faculty\Resources\Timetable\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimetable extends EditRecord
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
