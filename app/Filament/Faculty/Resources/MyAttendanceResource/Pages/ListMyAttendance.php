<?php

namespace App\Filament\Faculty\Resources\MyAttendanceResource\Pages;

use App\Filament\Faculty\Resources\MyAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAttendance extends ListRecords
{
    protected static string $resource = MyAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
