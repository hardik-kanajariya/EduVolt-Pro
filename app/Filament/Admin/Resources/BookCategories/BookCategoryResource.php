<?php

namespace App\Filament\Admin\Resources\BookCategories;

use App\Filament\Admin\Resources\BookCategories\Pages\CreateBookCategory;
use App\Filament\Admin\Resources\BookCategories\Pages\EditBookCategory;
use App\Filament\Admin\Resources\BookCategories\Pages\ListBookCategories;
use App\Filament\Admin\Resources\BookCategories\Schemas\BookCategoryForm;
use App\Filament\Admin\Resources\BookCategories\Tables\BookCategoriesTable;
use App\Models\BookCategory;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class BookCategoryResource extends Resource
{
    protected static ?string $model = BookCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?string $modelLabel = 'Book Category';

    protected static ?string $pluralModelLabel = 'Book Categories';

    public static function form(Form $form): Form
    {
        return BookCategoryForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return BookCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookCategories::route('/'),
            'create' => CreateBookCategory::route('/create'),
            'edit' => EditBookCategory::route('/{record}/edit'),
        ];
    }
}
