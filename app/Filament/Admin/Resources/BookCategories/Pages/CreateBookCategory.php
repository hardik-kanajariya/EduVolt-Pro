<?php

namespace App\Filament\Admin\Resources\BookCategories\Pages;

use App\Filament\Admin\Resources\BookCategories\BookCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookCategory extends CreateRecord
{
 protected static string $resource = BookCategoryResource::class;
}
