<?php

namespace App\Filament\Admin\Resources\LibraryFines\Pages;

use App\Filament\Admin\Resources\LibraryFines\LibraryFineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLibraryFines extends ListRecords
{
 protected static string $resource = LibraryFineResource::class;

 protected function getHeaderActions(): array
 {
 return [
 CreateAction::make(),
 ];
 }
}
