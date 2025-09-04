<?php

namespace App\Filament\Admin\Resources\BookReservations;

use App\Filament\Admin\Resources\BookReservations\Pages\CreateBookReservation;
use App\Filament\Admin\Resources\BookReservations\Pages\EditBookReservation;
use App\Filament\Admin\Resources\BookReservations\Pages\ListBookReservations;
use App\Filament\Admin\Resources\BookReservations\Schemas\BookReservationForm;
use App\Filament\Admin\Resources\BookReservations\Tables\BookReservationsTable;
use App\Models\BookReservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookReservationResource extends Resource
{
    protected static ?string $model = BookReservation::class;

    

    public static function form(Schema $schema): Schema
    {
        return BookReservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookReservationsTable::configure($table);
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
            'index' => ListBookReservations::route('/'),
            'create' => CreateBookReservation::route('/create'),
            'edit' => EditBookReservation::route('/{record}/edit'),
        ];
    }
}
