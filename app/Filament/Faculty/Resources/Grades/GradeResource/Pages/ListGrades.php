<?php

namespace App\Filament\Faculty\Resources\Grades\GradeResource\Pages;

use App\Filament\Faculty\Resources\Grades\GradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGrades extends ListRecords
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
