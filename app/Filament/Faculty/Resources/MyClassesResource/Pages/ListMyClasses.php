<?php

namespace App\Filament\Faculty\Resources\MyClassesResource\Pages;

use App\Filament\Faculty\Resources\MyClassesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyClasses extends ListRecords
{
    protected static string $resource = MyClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since teachers can't create classes
        ];
    }
}
