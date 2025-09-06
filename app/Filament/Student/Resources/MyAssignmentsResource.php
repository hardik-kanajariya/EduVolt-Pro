<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\MyAssignmentsResource\Pages;
use App\Models\Assignment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class MyAssignmentsResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'My Assignments';

    protected static ?string $slug = 'my-assignments';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent() || !$user->student) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('class_id', $user->student->class_id)
            ->where('school_id', $user->school_id)
            ->with(['subject', 'createdBy', 'submissions' => function ($query) use ($user) {
                $query->where('student_id', $user->student->id);
            }]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Students can only view assignments, not edit them
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(
                        fn($record) =>
                        $record->due_date < now() ? 'danger' : 'primary'
                    ),

                TextColumn::make('total_marks')
                    ->numeric()
                    ->sortable(),

                BadgeColumn::make('submission_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $user = Auth::user();
                        $submission = $record->submissions->where('student_id', $user->student->id)->first();

                        if (!$submission) {
                            return $record->due_date < now() ? 'overdue' : 'pending';
                        }

                        return $submission->status;
                    })
                    ->colors([
                        'success' => 'submitted',
                        'warning' => 'draft',
                        'danger' => 'overdue',
                        'primary' => 'pending',
                        'info' => 'reviewed',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('obtained_marks')
                    ->label('Marks')
                    ->getStateUsing(function ($record) {
                        $user = Auth::user();
                        $submission = $record->submissions->where('student_id', $user->student->id)->first();

                        if (!$submission || !$submission->marks_obtained) {
                            return '-';
                        }

                        return $submission->marks_obtained . '/' . $record->total_marks;
                    })
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('Teacher')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject'),

                Filter::make('status')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'submitted' => 'Submitted',
                                'overdue' => 'Overdue',
                                'reviewed' => 'Reviewed',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['status']) {
                            return $query;
                        }

                        $user = Auth::user();
                        if (!$user || !$user->student) {
                            return $query;
                        }

                        return $query->whereHas('submissions', function ($subQuery) use ($data, $user) {
                            $subQuery->where('student_id', $user->student->id)
                                ->where('status', $data['status']);
                        });
                    }),

                Filter::make('due_this_week')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereBetween('due_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    ),

                Filter::make('overdue')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('due_date', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('submit')
                    ->label('Submit/Edit')
                    ->icon('heroicon-o-paper-airplane')
                    ->url(fn($record) => route('filament.student.resources.my-assignments.submit', $record))
                    ->visible(function ($record) {
                        $user = Auth::user();
                        $submission = $record->submissions->where('student_id', $user->student->id)->first();

                        // Can submit if no submission exists or if submission is in draft
                        return !$submission || $submission->status === 'draft';
                    }),
            ])
            ->bulkActions([
                // No bulk actions for students
            ])
            ->defaultSort('due_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAssignments::route('/'),
            'view' => Pages\ViewMyAssignment::route('/{record}'),
            'submit' => Pages\SubmitAssignment::route('/{record}/submit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isStudent() && $user->student;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create assignments
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit assignments
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete assignments
    }
}
