<?php

namespace App\Filament\Parent\Resources\ChildGrades;

use App\Filament\Parent\Resources\ChildGrades\Pages;
use App\Models\Grade;
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

class ChildGradesResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Children\'s Grades';

    protected static ?string $slug = 'child-grades';

    protected static ?string $navigationGroup = 'Child Progress';

    protected static ?int $navigationSort = 1;

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

        return parent::getEloquentQuery()
            ->whereIn('student_id', $childrenIds)
            ->with(['student.user', 'subject', 'student.class']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Parents can only view grades, not edit them
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Child Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.class.name')
                    ->label('Class')
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('exam_type')
                    ->sortable()
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('exam_name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('obtained_marks')
                    ->label('Marks')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->obtained_marks . '/' . $record->total_marks
                    )
                    ->sortable(),

                TextColumn::make('percentage')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger'
                    }),

                BadgeColumn::make('grade')
                    ->colors([
                        'success' => ['A+', 'A', 'A-'],
                        'info' => ['B+', 'B', 'B-'],
                        'warning' => ['C+', 'C', 'C-'],
                        'danger' => ['D', 'F'],
                    ])
                    ->sortable(),

                TextColumn::make('exam_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('remarks')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Child')
                    ->options(function () {
                        $user = Auth::user();
                        return Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($query) use ($user) {
                                $query->where('email', $user->email);
                            })
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id');
                    }),

                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject'),

                SelectFilter::make('exam_type')
                    ->options([
                        'test' => 'Test',
                        'quiz' => 'Quiz',
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'assignment' => 'Assignment',
                        'project' => 'Project',
                    ]),

                Filter::make('grade_range')
                    ->form([
                        \Filament\Forms\Components\Select::make('grade_filter')
                            ->options([
                                'excellent' => 'Excellent (90%+)',
                                'good' => 'Good (75-89%)',
                                'average' => 'Average (60-74%)',
                                'below_average' => 'Below Average (<60%)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['grade_filter'],
                            function (Builder $query, $filter) {
                                return match ($filter) {
                                    'excellent' => $query->where('percentage', '>=', 90),
                                    'good' => $query->whereBetween('percentage', [75, 89]),
                                    'average' => $query->whereBetween('percentage', [60, 74]),
                                    'below_average' => $query->where('percentage', '<', 60),
                                    default => $query,
                                };
                            }
                        );
                    }),

                Filter::make('recent_exams')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('exam_date', '>=', now()->subMonth())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for parents
            ])
            ->defaultSort('exam_date', 'desc')
            ->groups([
                Tables\Grouping\Group::make('student.user.name')
                    ->label('Child')
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
            'index' => Pages\ListChildGrades::route('/'),
            'view' => Pages\ViewChildGrade::route('/{record}'),
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
        return false; // Parents cannot create grades
    }

    public static function canEdit($record): bool
    {
        return false; // Parents cannot edit grades
    }

    public static function canDelete($record): bool
    {
        return false; // Parents cannot delete grades
    }
}
