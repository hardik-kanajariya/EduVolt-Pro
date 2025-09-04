<?php

namespace App\Filament\Admin\Resources\LibraryBooks\Pages;

use App\Filament\Admin\Resources\LibraryBooks\LibraryBookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLibraryBooks extends ListRecords
{
    protected static string $resource = LibraryBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
