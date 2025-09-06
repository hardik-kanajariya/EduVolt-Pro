<?php

namespace App\Filament\Faculty\Resources\Timetable\TimetableResource\Pages;

use App\Filament\Faculty\Resources\Timetable\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimetables extends ListRecords
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
