<?php

namespace App\Filament\Student\Resources\MyGradesResource\Pages;

use App\Filament\Student\Resources\MyGradesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyGrades extends ListRecords
{
    protected static string $resource = MyGradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No header actions for students
        ];
    }
}
