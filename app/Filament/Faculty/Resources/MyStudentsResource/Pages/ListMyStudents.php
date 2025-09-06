<?php

namespace App\Filament\Faculty\Resources\MyStudentsResource\Pages;

use App\Filament\Faculty\Resources\MyStudentsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyStudents extends ListRecords
{
    protected static string $resource = MyStudentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since teachers can't create students
        ];
    }
}
