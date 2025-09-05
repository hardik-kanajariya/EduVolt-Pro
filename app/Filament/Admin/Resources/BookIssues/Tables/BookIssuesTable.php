<?php

namespace App\Filament\Admin\Resources\BookIssues\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookIssuesTable
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
 TextColumn::make('issued_by')
 ->numeric()
 ->sortable(),
 TextColumn::make('returned_by')
 ->numeric()
 ->sortable(),
 TextColumn::make('issue_date')
 ->date()
 ->sortable(),
 TextColumn::make('due_date')
 ->date()
 ->sortable(),
 TextColumn::make('return_date')
 ->date()
 ->sortable(),
 TextColumn::make('status'),
 TextColumn::make('condition_at_issue'),
 TextColumn::make('condition_at_return'),
 TextColumn::make('renewal_count')
 ->numeric()
 ->sortable(),
 TextColumn::make('last_renewal_date')
 ->date()
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
