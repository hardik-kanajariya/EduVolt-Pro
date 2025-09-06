<?php

namespace App\Filament\Faculty\Widgets;

use App\Models\Assignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MyAssignmentsWidget extends BaseWidget
{
    protected static ?string $heading = 'My Recent Assignments';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return $table->query(Assignment::query()->whereRaw('1 = 0'));
        }

        return $table
            ->query(
                Assignment::query()
                    ->where('teacher_id', $teacher->id)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class'),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'overdue' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn(Assignment $record): string => route('filament.faculty.resources.assignments.view', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
