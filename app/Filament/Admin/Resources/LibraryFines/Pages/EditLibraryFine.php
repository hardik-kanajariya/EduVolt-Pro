<?php

namespace App\Filament\Admin\Resources\LibraryFines\Pages;

use App\Filament\Admin\Resources\LibraryFines\LibraryFineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLibraryFine extends EditRecord
{
 protected static string $resource = LibraryFineResource::class;

 protected function getHeaderActions(): array
 {
 return [
 DeleteAction::make(),
 ];
 }
}
