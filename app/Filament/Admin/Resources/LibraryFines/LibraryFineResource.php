<?php

namespace App\Filament\Admin\Resources\LibraryFines;

use App\Filament\Admin\Resources\LibraryFines\Pages\CreateLibraryFine;
use App\Filament\Admin\Resources\LibraryFines\Pages\EditLibraryFine;
use App\Filament\Admin\Resources\LibraryFines\Pages\ListLibraryFines;
use App\Filament\Admin\Resources\LibraryFines\Schemas\LibraryFineForm;
use App\Filament\Admin\Resources\LibraryFines\Tables\LibraryFinesTable;
use App\Models\LibraryFine;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class LibraryFineResource extends Resource
{
    protected static ?string $model = LibraryFine::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Library Management';

    public static function form(Form $form): Form
    {
        return LibraryFineForm::configure($form);
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
