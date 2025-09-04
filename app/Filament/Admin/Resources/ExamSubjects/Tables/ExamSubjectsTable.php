<?php

namespace App\Filament\Admin\Resources\ExamSubjects\Tables;

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

class ExamSubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exam_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),

                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('room')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('max_marks')
                    ->suffix(' marks')
                    ->sortable(),

                TextColumn::make('theory_marks')
                    ->suffix(' marks')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('practical_marks')
                    ->suffix(' marks')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Completed'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'name'),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),

                Filter::make('completed')
                    ->query(fn (Builder $query): Builder => $query->where('is_completed', true))
                    ->toggle(),

                Filter::make('today')
                    ->query(fn (Builder $query): Builder => $query->today())
                    ->toggle(),

                Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->upcoming())
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->markAsCompleted();
                    })
                    ->visible(fn ($record) => !$record->is_completed && $record->canStartExam()),

                Action::make('activate')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->action(function ($record) {
                        $record->update(['is_active' => true]);
                    })
                    ->visible(fn ($record) => !$record->is_active),

                Action::make('deactivate')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->update(['is_active' => false]);
                    })
                    ->visible(fn ($record) => $record->is_active),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('exam_date', 'asc');
    }
}
