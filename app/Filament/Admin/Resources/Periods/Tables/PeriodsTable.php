<?php

namespace App\Filament\Admin\Resources\Periods\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class PeriodsTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 //
 ])
 ->filters([
 //
 ])
 ->actions([
 EditAction::make(),
 ])
 ->toolbarActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ]),
 ]);
 }
}
