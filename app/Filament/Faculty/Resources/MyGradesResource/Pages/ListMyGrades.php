<?php

namespace App\Filament\Faculty\Resources\MyGradesResource\Pages;

use App\Filament\Faculty\Resources\MyGradesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyGrades extends ListRecords
{
    protected static string $resource = MyGradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
