<?php

namespace App\Filament\Student\Widgets;

use App\Models\Assignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingAssignmentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Assignments';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return $table->query(Assignment::query()->whereRaw('1 = 0'));
        }

        return $table
            ->query(
                Assignment::query()
                    ->where('class_id', $student->class_id)
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn($record) => $record->due_date->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('submission_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($student) {
                        $submission = $record->submissions()
                            ->where('student_id', $student->id)
                            ->first();
                        return $submission ? 'Submitted' : 'Pending';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Submitted' => 'success',
                        'Pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('marks')
                    ->label('Total Marks'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn(Assignment $record): string => route('filament.student.resources.assignments.view', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated([5, 10]);
    }
}
