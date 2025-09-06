<?php

namespace App\Filament\Faculty\Resources\LibraryResource\Pages;

use App\Filament\Faculty\Resources\LibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibrary extends ListRecords
{
    protected static string $resource = LibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
