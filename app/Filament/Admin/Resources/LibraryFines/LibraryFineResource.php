<?php

namespace App\Filament\Admin\Resources\LibraryFines;

use App\Filament\Admin\Resources\LibraryFines\Pages\CreateLibraryFine;
use App\Filament\Admin\Resources\LibraryFines\Pages\EditLibraryFine;
use App\Filament\Admin\Resources\LibraryFines\Pages\ListLibraryFines;
use App\Filament\Admin\Resources\LibraryFines\Schemas\LibraryFineForm;
use App\Filament\Admin\Resources\LibraryFines\Tables\LibraryFinesTable;
use App\Models\LibraryFine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LibraryFineResource extends Resource
{
    protected static ?string $model = LibraryFine::class;

    

    public static function form(Schema $schema): Schema
    {
        return LibraryFineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryFinesTable::configure($table);
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
            'index' => ListLibraryFines::route('/'),
            'create' => CreateLibraryFine::route('/create'),
            'edit' => EditLibraryFine::route('/{record}/edit'),
        ];
    }
}
