<?php

namespace App\Filament\Admin\Resources\ExamMarks\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExamMarksTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 TextColumn::make('examSubject.exam.name')
 ->label('Exam')
 ->searchable()
 ->sortable(),

 TextColumn::make('examSubject.subject.name')
 ->label('Subject')
 ->searchable()
 ->sortable(),

 TextColumn::make('student.first_name')
 ->label('Student')
 ->searchable()
 ->formatStateUsing(fn($record) => "{$record->student->first_name} {$record->student->last_name}")
 ->sortable(),

 TextColumn::make('student.admission_number')
 ->label('Roll No.')
 ->searchable()
 ->sortable(),

 TextColumn::make('theory_marks')
 ->sortable()
 ->toggleable(),

 TextColumn::make('practical_marks')
 ->sortable()
 ->toggleable(),

 TextColumn::make('total_marks')
 ->sortable()
 ->weight('bold'),

 TextColumn::make('grade')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'A+', 'A' => 'success',
 'B+', 'B' => 'primary',
 'C+', 'C' => 'warning',
 'F' => 'danger',
 'AB' => 'gray',
 default => 'secondary',
 }),

 TextColumn::make('percentage')
 ->suffix('%')
 ->sortable(),

 TextColumn::make('status')
 ->badge()
 ->color(fn(string $state): string => match ($state) {
 'Pass' => 'success',
 'Fail' => 'danger',
 'Absent' => 'gray',
 'Pending Verification' => 'warning',
 default => 'secondary',
 }),

 IconColumn::make('is_absent')
 ->boolean()
 ->label('Absent'),

 IconColumn::make('is_verified')
 ->boolean()
 ->label('Verified'),

 TextColumn::make('enteredBy.name')
 ->label('Entered By')
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('verifiedBy.name')
 ->label('Verified By')
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('entered_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 SelectFilter::make('exam_subject_id')
 ->label('Exam Subject')
 ->relationship('examSubject', 'id')
 ->getOptionLabelFromRecordUsing(fn($record) => "{$record->exam->name} - {$record->subject->name}"),

 SelectFilter::make('grade')
 ->options([
 'A+' => 'A+',
 'A' => 'A',
 'B+' => 'B+',
 'B' => 'B',
 'C+' => 'C+',
 'C' => 'C',
 'F' => 'F',
 'AB' => 'Absent',
 ]),

 Filter::make('verified')
 ->query(fn(Builder $query): Builder => $query->verified())
 ->toggle(),

 Filter::make('unverified')
 ->query(fn(Builder $query): Builder => $query->unverified())
 ->toggle(),

 Filter::make('absent')
 ->query(fn(Builder $query): Builder => $query->absent())
 ->toggle(),

 Filter::make('passed')
 ->query(fn(Builder $query): Builder => $query->passed())
 ->toggle(),

 Filter::make('failed')
 ->query(fn(Builder $query): Builder => $query->failed())
 ->toggle(),
 ])
 ->actions([
 Action::make('verify')
 ->icon('heroicon-o-check-badge')
 ->color('success')
 ->action(function ($record) {
 $record->verify(Auth::id(), 'Verified via admin panel');
 })
 ->visible(fn($record) => !$record->is_verified),

 Action::make('unverify')
 ->icon('heroicon-o-x-circle')
 ->color('warning')
 ->action(function ($record) {
 $record->unverify('Unverified via admin panel');
 })
 ->visible(fn($record) => $record->is_verified),

 EditAction::make(),
 DeleteAction::make(),
 ])
 ->bulkActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),

 \Filament\Actions\BulkAction::make('verify')
 ->icon('heroicon-o-check-badge')
 ->color('success')
 ->action(function ($records) {
 $records->each(function ($record) {
 $record->verify(Auth::id(), 'Bulk verified via admin panel');
 });
 }),

 \Filament\Actions\BulkAction::make('unverify')
 ->icon('heroicon-o-x-circle')
 ->color('warning')
 ->action(function ($records) {
 $records->each(function ($record) {
 $record->unverify('Bulk unverified via admin panel');
 });
 }),
 ]),
 ])
 ->defaultSort('entered_at', 'desc');
 }
}
