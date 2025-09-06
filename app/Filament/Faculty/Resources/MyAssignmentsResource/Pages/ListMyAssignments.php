<?php

namespace App\Filament\Faculty\Resources\MyAssignmentsResource\Pages;

use App\Filament\Faculty\Resources\MyAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAssignments extends ListRecords
{
    protected static string $resource = MyAssignmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
