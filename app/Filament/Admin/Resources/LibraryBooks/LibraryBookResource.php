<?php

namespace App\Filament\Admin\Resources\LibraryBooks;

use App\Filament\Admin\Resources\LibraryBooks\Pages\CreateLibraryBook;
use App\Filament\Admin\Resources\LibraryBooks\Pages\EditLibraryBook;
use App\Filament\Admin\Resources\LibraryBooks\Pages\ListLibraryBooks;
use App\Filament\Admin\Resources\LibraryBooks\Schemas\LibraryBookForm;
use App\Filament\Admin\Resources\LibraryBooks\Tables\LibraryBooksTable;
use App\Models\LibraryBook;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LibraryBookResource extends Resource
{
    protected static ?string $model = LibraryBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?string $modelLabel = 'Library Book';

    protected static ?string $pluralModelLabel = 'Library Books';

    public static function form(Form $form): Form
    {
        return LibraryBookForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return LibraryBooksTable::configure($table);
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
            'index' => ListLibraryBooks::route('/'),
            'create' => CreateLibraryBook::route('/create'),
            'edit' => EditLibraryBook::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
