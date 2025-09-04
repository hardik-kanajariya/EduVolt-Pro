<?php

namespace App\Filament\Admin\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubjectsTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('school.name')
 ->label('School')
 ->searchable()
 ->sortable()
 ->toggleable(),

 TextColumn::make('name')
 ->label('Subject Name')
 ->searchable()
 ->sortable()
 ->weight('bold')
 ->description(fn($record) => $record->description)
 ->limit(30)
 ->tooltip(fn($record) => $record->description),

 TextColumn::make('code')
 ->label('Subject Code')
 ->searchable()
 ->badge()
 ->color('gray')
 ->copyable()
 ->copyMessage('Subject code copied!')
 ->copyMessageDuration(1500),

 TextColumn::make('type')
 ->label('Type')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'core' => 'success',
 'elective' => 'info',
 'extra_curricular' => 'warning',
 default => 'gray',
 })
 ->searchable()
 ->sortable(),

 TextColumn::make('credits')
 ->label('Credits')
 ->numeric()
 ->sortable()
 ->alignCenter()
 ->badge()
 ->color('primary'),

 TextColumn::make('teachers_count')
 ->label('Teachers')
 ->counts('teachers')
 ->badge()
 ->color('info')
 ->alignCenter(),

 TextColumn::make('classes_count')
 ->label('Classes')
 ->counts('classes')
 ->badge()
 ->color('warning')
 ->alignCenter(),

 TextColumn::make('status')
 ->label('Status')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'active' => 'success',
 'inactive' => 'danger',
 default => 'gray',
 }),

 TextColumn::make('created_at')
 ->label('Created')
 ->dateTime('M j, Y')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('updated_at')
 ->label('Updated')
 ->dateTime('M j, Y')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 TrashedFilter::make(),
 
 SelectFilter::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->searchable()
 ->preload(),

 SelectFilter::make('type')
 ->label('Subject Type')
 ->options([
 'core' => 'Core Subject',
 'elective' => 'Elective Subject',
 'extra_curricular' => 'Extra-Curricular',
 ])
 ->multiple(),

 SelectFilter::make('status')
 ->label('Status')
 ->options([
 'active' => 'Active',
 'inactive' => 'Inactive',
 ])
 ->multiple(),

 TernaryFilter::make('has_teachers')
 ->label('Has Teachers')
 ->queries(
 true: fn($query) => $query->whereHas('teachers'),
 false: fn($query) => $query->whereDoesntHave('teachers'),
 ),
 ])
 ->recordActions([
 ViewAction::make(),
 EditAction::make(),
 ])
 ->bulkActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ForceDeleteBulkAction::make(),
 RestoreBulkAction::make(),
 ])
 ])
 ->defaultSort('name')
 ->persistSortInSession()
 ->persistSearchInSession()
 ->persistFiltersInSession()
 ->striped()
 ->searchOnBlur()
 ->deferLoading()
 ->poll('60s');
 }
}
