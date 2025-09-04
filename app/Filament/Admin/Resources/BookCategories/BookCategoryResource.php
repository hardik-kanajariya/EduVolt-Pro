<?php

namespace App\Filament\Admin\Resources\BookCategories;

use App\Filament\Admin\Resources\BookCategories\Pages\CreateBookCategory;
use App\Filament\Admin\Resources\BookCategories\Pages\EditBookCategory;
use App\Filament\Admin\Resources\BookCategories\Pages\ListBookCategories;
use App\Filament\Admin\Resources\BookCategories\Schemas\BookCategoryForm;
use App\Filament\Admin\Resources\BookCategories\Tables\BookCategoriesTable;
use App\Models\BookCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

class BookCategoryResource extends Resource
{
    protected static ?string $model = BookCategory::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-folder';

    protected static string | UnitEnum | null $navigationGroup = 'Library Management';

    protected static ?string $modelLabel = 'Book Category';

    protected static ?string $pluralModelLabel = 'Book Categories';

    public static function form(Schema $schema): Schema
    {
        return BookCategoryForm::configure($schema);
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
