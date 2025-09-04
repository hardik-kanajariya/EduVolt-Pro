<?php

namespace App\Filament\Admin\Resources\BookCategories\Pages;

use App\Filament\Admin\Resources\BookCategories\BookCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookCategory extends EditRecord
{
 protected static string $resource = BookCategoryResource::class;

 protected function getHeaderActions(): array
 {
 return [
 DeleteAction::make(),
 ];
 }
}
