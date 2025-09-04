<?php

namespace App\Filament\Admin\Resources\Exams\Tables;

use App\Models\AcademicYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ExamsTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('name')
 ->searchable()
 ->sortable(),

 TextColumn::make('type')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'final' => 'danger',
 'midterm' => 'warning',
 'unit_test' => 'info',
 'quarterly' => 'primary',
 'half_yearly' => 'success',
 'annual' => 'danger',
 default => 'secondary',
 }),

 TextColumn::make('academicYear.name')
 ->label('Academic Year')
 ->sortable(),

 TextColumn::make('school.name')
 ->label('School')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('start_date')
 ->date()
 ->sortable(),

 TextColumn::make('end_date')
 ->date()
 ->sortable(),

 TextColumn::make('total_marks')
 ->suffix(' marks')
 ->sortable(),

 TextColumn::make('status')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'draft' => 'gray',
 'scheduled' => 'info',
 'ongoing' => 'warning',
 'completed' => 'success',
 'cancelled' => 'danger',
 default => 'secondary',
 }),

 IconColumn::make('is_published')
 ->boolean()
 ->label('Published'),

 TextColumn::make('created_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 SelectFilter::make('type')
 ->options([
 'midterm' => 'Mid-term',
 'final' => 'Final',
 'unit_test' => 'Unit Test',
 'quarterly' => 'Quarterly',
 'half_yearly' => 'Half Yearly',
 'annual' => 'Annual',
 ]),

 SelectFilter::make('status')
 ->options([
 'draft' => 'Draft',
 'scheduled' => 'Scheduled',
 'ongoing' => 'Ongoing',
 'completed' => 'Completed',
 'cancelled' => 'Cancelled',
 ]),

 SelectFilter::make('academic_year_id')
 ->label('Academic Year')
 ->relationship('academicYear', 'name'),

 Filter::make('published')
 ->query(fn(Builder $query): Builder => $query->where('is_published', true))
 ->toggle(),

 Filter::make('upcoming')
 ->query(fn(Builder $query): Builder => $query->upcoming())
 ->toggle(),

 Filter::make('ongoing')
 ->query(fn(Builder $query): Builder => $query->ongoing())
 ->toggle(),
 ])
 ->recordActions([
 Action::make('publish')
 ->icon('heroicon-o-eye')
 ->color('success')
 ->action(function ($record) {
 $record->update([
 'is_published' => true,
 'published_at' => now(),
 ]);
 })
 ->visible(fn($record) => !$record->is_published),

 Action::make('unpublish')
 ->icon('heroicon-o-eye-slash')
 ->color('danger')
 ->action(function ($record) {
 $record->update([
 'is_published' => false,
 'published_at' => null,
 ]);
 })
 ->visible(fn($record) => $record->is_published),

 EditAction::make(),
 DeleteAction::make(),
 ])
 ->toolbarActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ]),
 ])
 ->defaultSort('created_at', 'desc');
 }
}
