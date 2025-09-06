<?php

namespace App\Filament\Faculty\Resources\MyTimetableResource\Pages;

use App\Filament\Faculty\Resources\MyTimetableResource;
use Filament\Resources\Pages\ListRecords;

class ListMyTimetable extends ListRecords
{
    protected static string $resource = MyTimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
