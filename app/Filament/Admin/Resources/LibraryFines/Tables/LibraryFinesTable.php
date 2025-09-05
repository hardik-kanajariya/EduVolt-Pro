<?php

namespace App\Filament\Admin\Resources\LibraryFines\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryFinesTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('school.name')
 ->searchable(),
 TextColumn::make('bookIssue.id')
 ->searchable(),
 TextColumn::make('student.id')
 ->searchable(),
 TextColumn::make('amount')
 ->numeric()
 ->sortable(),
 TextColumn::make('type'),
 TextColumn::make('fine_date')
 ->date()
 ->sortable(),
 TextColumn::make('status'),
 TextColumn::make('paid_amount')
 ->numeric()
 ->sortable(),
 TextColumn::make('paid_date')
 ->date()
 ->sortable(),
 TextColumn::make('collected_by')
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
