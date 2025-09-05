<?php

namespace App\Filament\Admin\Resources\SchoolClasses\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SchoolClassesTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('class_identifier')
 ->label('Class')
 ->state(function ($record): string {
 return $record->display_name ?: "{$record->name}-{$record->section}";
 })
 ->searchable(['name', 'section', 'display_name'])
 ->sortable()
 ->weight('bold')
 ->icon('heroicon-o-academic-cap')
 ->iconColor('primary'),

 TextColumn::make('school.name')
 ->label('School')
 ->sortable()
 ->searchable()
 ->badge()
 ->color('info')
 ->icon('heroicon-o-building-office-2')
 ->limit(25),

 TextColumn::make('academicYear.name')
 ->label('Academic Year')
 ->sortable()
 ->badge()
 ->color('warning')
 ->icon('heroicon-o-calendar')
 ->toggleable(),

 TextColumn::make('grade_level')
 ->label('Level')
 ->badge()
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'kindergarten' => ' KG',
 'primary' => ' Primary',
 'middle' => ' Middle',
 'secondary' => ' Secondary',
 'higher_secondary' => ' Higher Sec',
 default => ucfirst($state ?? 'General'),
 })
 ->color(fn(?string $state): string => match ($state) {
 'kindergarten' => 'pink',
 'primary' => 'success',
 'middle' => 'info',
 'secondary' => 'warning',
 'higher_secondary' => 'danger',
 default => 'gray',
 }),

 TextColumn::make('stream')
 ->label('Stream')
 ->badge()
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'general' => ' General',
 'science' => ' Science',
 'commerce' => ' Commerce',
 'arts' => ' Arts',
 'vocational' => ' Vocational',
 'special' => ' Special',
 default => ucfirst($state ?? 'General'),
 })
 ->color(fn(?string $state): string => match ($state) {
 'science' => 'success',
 'commerce' => 'info',
 'arts' => 'purple',
 'vocational' => 'warning',
 'special' => 'pink',
 default => 'gray',
 })
 ->toggleable(),

 TextColumn::make('classTeacher.user.name')
 ->label('Class Teacher')
 ->sortable()
 ->searchable()
 ->placeholder('Not assigned')
 ->icon('heroicon-o-user')
 ->iconColor('success')
 ->limit(20),

 TextColumn::make('room_number')
 ->label('Room')
 ->searchable()
 ->badge()
 ->color('purple')
 ->placeholder('Not assigned')
 ->icon('heroicon-o-home')
 ->alignCenter(),

 TextColumn::make('student_occupancy')
 ->label('Students')
 ->state(function ($record): string {
 $studentCount = $record->students?->count() ?? 0;
 $capacity = $record->capacity ?? 0;
 return "{$studentCount}/{$capacity}";
 })
 ->description(function ($record): string {
 $studentCount = $record->students?->count() ?? 0;
 $capacity = $record->capacity ?? 0;
 if ($capacity > 0) {
 $percentage = round(($studentCount / $capacity) * 100);
 return "{$percentage}% occupied";
 }
 return 'No capacity set';
 })
 ->alignCenter()
 ->badge()
 ->color(function ($record): string {
 $studentCount = $record->students?->count() ?? 0;
 $capacity = $record->capacity ?? 0;
 if ($capacity === 0) return 'gray';

 $percentage = ($studentCount / $capacity) * 100;
 if ($percentage >= 95) return 'danger';
 if ($percentage >= 80) return 'warning';
 if ($percentage >= 60) return 'success';
 return 'info';
 })
 ->icon('heroicon-o-users'),

 TextColumn::make('fee_amount')
 ->label('Monthly Fee')
 ->numeric()
 ->prefix('')
 ->sortable()
 ->placeholder('Not set')
 ->toggleable(isToggledHiddenByDefault: true)
 ->icon('heroicon-o-currency-rupee')
 ->iconColor('success'),

 TextColumn::make('subject_count')
 ->label('Subjects')
 ->state(fn($record): int => $record->subjects?->count() ?? 0)
 ->alignCenter()
 ->badge()
 ->color('info')
 ->icon('heroicon-o-book-open')
 ->tooltip('Number of subjects taught'),

 IconColumn::make('has_timetable')
 ->label('Timetable')
 ->boolean()
 ->state(fn($record): bool => is_array($record->timetable_slots) && count($record->timetable_slots) > 0)
 ->trueIcon('heroicon-o-clock')
 ->falseIcon('heroicon-o-clock')
 ->trueColor('success')
 ->falseColor('gray')
 ->tooltip(
 fn($record): string =>
 is_array($record->timetable_slots) && count($record->timetable_slots) > 0
 ? 'Timetable configured'
 : 'No timetable set'
 )
 ->alignCenter(),

 TextColumn::make('status')
 ->label('Status')
 ->badge()
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'active' => ' Active',
 'inactive' => ' Inactive',
 'completed' => ' Completed',
 'suspended' => ' Suspended',
 default => ucfirst($state ?? 'Active'),
 })
 ->color(fn(?string $state): string => match ($state) {
 'active' => 'success',
 'inactive' => 'gray',
 'completed' => 'info',
 'suspended' => 'warning',
 default => 'gray',
 })
 ->sortable(),

 TextColumn::make('created_at')
 ->label('Created')
 ->dateTime('M j, Y g:i A')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true)
 ->description(fn($record): string => $record->created_at->diffForHumans()),

 TextColumn::make('updated_at')
 ->label('Updated')
 ->dateTime('M j, Y g:i A')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true)
 ->description(fn($record): string => $record->updated_at->diffForHumans()),
 ])
 ->filters([
 TrashedFilter::make(),

 SelectFilter::make('status')
 ->label('Class Status')
 ->options([
 'active' => ' Active',
 'inactive' => ' Inactive',
 'completed' => ' Completed',
 'suspended' => ' Suspended',
 ])
 ->multiple()
 ->default(['active']),

 SelectFilter::make('grade_level')
 ->label('Grade Level')
 ->options([
 'kindergarten' => ' Kindergarten',
 'primary' => ' Primary (1-5)',
 'middle' => ' Middle (6-8)',
 'secondary' => ' Secondary (9-10)',
 'higher_secondary' => ' Higher Secondary (11-12)',
 ])
 ->multiple(),

 SelectFilter::make('stream')
 ->label('Stream/Track')
 ->options([
 'general' => ' General',
 'science' => ' Science',
 'commerce' => ' Commerce',
 'arts' => ' Arts/Humanities',
 'vocational' => ' Vocational',
 'special' => ' Special Education',
 ])
 ->multiple(),

 SelectFilter::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->searchable()
 ->preload(),

 SelectFilter::make('academic_year_id')
 ->label('Academic Year')
 ->relationship('academicYear', 'name')
 ->searchable()
 ->preload(),

 Filter::make('has_teacher')
 ->label('Has Class Teacher')
 ->query(fn(Builder $query): Builder => $query->whereNotNull('class_teacher_id'))
 ->toggle(),

 Filter::make('full_capacity')
 ->label('Full Capacity (95%+)')
 ->query(function (Builder $query): Builder {
 return $query->whereHas('students', function ($q) {
 $q->havingRaw('COUNT(*) >= (SELECT capacity * 0.95 FROM school_classes WHERE school_classes.id = students.class_id)');
 });
 })
 ->toggle(),

 Filter::make('needs_teacher')
 ->label('Needs Teacher Assignment')
 ->query(fn(Builder $query): Builder => $query->whereNull('class_teacher_id'))
 ->toggle(),

 Filter::make('has_timetable')
 ->label('Has Timetable')
 ->query(fn(Builder $query): Builder => $query->whereNotNull('timetable_slots'))
 ->toggle(),
 ])
 ->actions([
 ViewAction::make()
 ->icon('heroicon-o-eye')
 ->color('info'),
 EditAction::make()
 ->icon('heroicon-o-pencil-square')
 ->color('warning'),
 ])
 ->toolbarActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ForceDeleteBulkAction::make(),
 RestoreBulkAction::make(),
 ]),
 ])
 ->defaultSort('name')
 ->striped()
 ->poll('30s')
 ->emptyStateHeading('No classes found')
 ->emptyStateDescription('Start by creating your first class.')
 ->emptyStateIcon('heroicon-o-academic-cap')
 ->recordUrl(fn($record) => null)
 ->searchPlaceholder('Search classes by name, section, teacher...')
 ->paginationPageOptions([10, 25, 50, 100])
 ->defaultPaginationPageOption(25);
 }
}
