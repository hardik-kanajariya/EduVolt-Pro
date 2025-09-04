<?php

namespace App\Filament\Admin\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StaffTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('user_id')
 ->numeric()
 ->sortable(),
 TextColumn::make('school_id')
 ->numeric()
 ->sortable(),
 TextColumn::make('employee_id')
 ->searchable(),
 TextColumn::make('position')
 ->searchable(),
 TextColumn::make('department')
 ->searchable(),
 TextColumn::make('join_date')
 ->date()
 ->sortable(),
 TextColumn::make('salary')
 ->numeric()
 ->sortable(),
 TextColumn::make('employment_type')
 ->searchable(),
 TextColumn::make('status'),
 TextColumn::make('created_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 TextColumn::make('updated_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 TextColumn::make('deleted_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 TrashedFilter::make(),
 ])
 ->recordActions([
 EditAction::make(),
 ])
 ->toolbarActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ForceDeleteBulkAction::make(),
 RestoreBulkAction::make(),
 ]),
 ]);
 }
}
