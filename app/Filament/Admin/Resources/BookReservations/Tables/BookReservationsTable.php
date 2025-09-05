<?php

namespace App\Filament\Admin\Resources\BookReservations\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookReservationsTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('school.name')
 ->searchable(),
 TextColumn::make('book.title')
 ->searchable(),
 TextColumn::make('student.id')
 ->searchable(),
 TextColumn::make('reservation_date')
 ->date()
 ->sortable(),
 TextColumn::make('expiry_date')
 ->date()
 ->sortable(),
 TextColumn::make('status'),
 TextColumn::make('fulfilled_at')
 ->dateTime()
 ->sortable(),
 TextColumn::make('fulfilled_by')
 ->numeric()
 ->sortable(),
 TextColumn::make('created_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 TextColumn::make('updated_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 //
 ])
 ->actions([
 EditAction::make(),
 ])
 ->bulkActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ]),
 ]);
 }
}
