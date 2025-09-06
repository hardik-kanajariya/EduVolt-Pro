<?php

namespace App\Filament\Parent\Resources\ChildAssignments;

use App\Filament\Parent\Resources\ChildAssignments\Pages;
use App\Models\Assignment;
use App\Models\Student;
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

class ChildAssignmentsResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Children\'s Assignments';

    protected static ?string $slug = 'child-assignments';

    protected static ?string $navigationGroup = 'Child Progress';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Get children associated with the parent's email
        $childrenIds = Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->pluck('id');

        $childrenClassIds = Student::whereIn('id', $childrenIds)->pluck('class_id');

        return parent::getEloquentQuery()
            ->whereIn('class_id', $childrenClassIds)
            ->with(['subject', 'createdBy', 'class', 'submissions' => function ($query) use ($childrenIds) {
                $query->whereIn('student_id', $childrenIds);
            }]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Parents can only view assignments, not edit them
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('class.name')
                    ->label('Class')
                    ->sortable(),

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
                    ->label('Children\'s Status')
                    ->getStateUsing(function ($record) {
                        $user = Auth::user();
                        $childrenIds = Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($query) use ($user) {
                                $query->where('email', $user->email);
                            })
                            ->pluck('id');

                        $submissions = $record->submissions->whereIn('student_id', $childrenIds);

                        if ($submissions->isEmpty()) {
                            return $record->due_date < now() ? 'overdue' : 'pending';
                        }

                        $submittedCount = $submissions->where('status', 'submitted')->count();
                        $totalChildren = $childrenIds->count();

                        if ($submittedCount == $totalChildren) {
                            return 'all_submitted';
                        } elseif ($submittedCount > 0) {
                            return 'partially_submitted';
                        } else {
                            return 'pending';
                        }
                    })
                    ->colors([
                        'success' => 'all_submitted',
                        'info' => 'partially_submitted',
                        'warning' => 'pending',
                        'danger' => 'overdue',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'all_submitted' => 'All Submitted',
                        'partially_submitted' => 'Partially Submitted',
                        'pending' => 'Pending',
                        'overdue' => 'Overdue',
                        default => ucfirst($state),
                    }),

                TextColumn::make('children_marks')
                    ->label('Children\'s Marks')
                    ->getStateUsing(function ($record) {
                        $user = Auth::user();
                        $childrenIds = Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($query) use ($user) {
                                $query->where('email', $user->email);
                            })
                            ->pluck('id');

                        $submissions = $record->submissions->whereIn('student_id', $childrenIds);
                        $marksData = [];

                        foreach ($submissions as $submission) {
                            if ($submission->marks_obtained !== null) {
                                $student = Student::find($submission->student_id);
                                $marksData[] = $student->user->name . ': ' . $submission->marks_obtained . '/' . $record->total_marks;
                            }
                        }

                        return $marksData ? implode(', ', $marksData) : '-';
                    })
                    ->limit(100)
                    ->toggleable(),

                TextColumn::make('createdBy.name')
                    ->label('Teacher')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject'),

                SelectFilter::make('class_id')
                    ->label('Class')
                    ->options(function () {
                        $user = Auth::user();
                        $childrenClassIds = Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($query) use ($user) {
                                $query->where('email', $user->email);
                            })
                            ->with('class')
                            ->get()
                            ->pluck('class.name', 'class.id')
                            ->unique();
                        return $childrenClassIds;
                    }),

                Filter::make('submission_status')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'submitted' => 'Has Submissions',
                                'overdue' => 'Overdue',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['status']) {
                            return $query;
                        }

                        $user = Auth::user();
                        $childrenIds = Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($q) use ($user) {
                                $q->where('email', $user->email);
                            })
                            ->pluck('id');

                        return match ($data['status']) {
                            'submitted' => $query->whereHas('submissions', function ($subQuery) use ($childrenIds) {
                                $subQuery->whereIn('student_id', $childrenIds)
                                    ->where('status', 'submitted');
                            }),
                            'overdue' => $query->where('due_date', '<', now()),
                            'pending' => $query->whereDoesntHave('submissions', function ($subQuery) use ($childrenIds) {
                                $subQuery->whereIn('student_id', $childrenIds)
                                    ->where('status', 'submitted');
                            }),
                            default => $query,
                        };
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
            ])
            ->bulkActions([
                // No bulk actions for parents
            ])
            ->defaultSort('due_date', 'asc')
            ->groups([
                Tables\Grouping\Group::make('class.name')
                    ->label('Class')
                    ->collapsible(),
                Tables\Grouping\Group::make('subject.name')
                    ->label('Subject')
                    ->collapsible(),
            ]);
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
            'index' => Pages\ListChildAssignments::route('/'),
            'view' => Pages\ViewChildAssignment::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->isParent()) {
            return false;
        }

        // Verify user has children
        return Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->exists();
    }

    public static function canCreate(): bool
    {
        return false; // Parents cannot create assignments
    }

    public static function canEdit($record): bool
    {
        return false; // Parents cannot edit assignments
    }

    public static function canDelete($record): bool
    {
        return false; // Parents cannot delete assignments
    }
}
