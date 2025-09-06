<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\MyGradesResource\Pages;
use App\Models\Grade;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class MyGradesResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'My Grades';

    protected static ?string $slug = 'my-grades';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        if (!$user || !$user->isStudent() || !$user->student) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('student_id', $user->student->id)
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Students can only view grades, not edit them
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exam_type')
                    ->label('Exam Type')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                TextColumn::make('exam_name')
                    ->label('Exam Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('obtained_marks')
                    ->label('Marks Obtained')
                    ->sortable(),

                TextColumn::make('total_marks')
                    ->label('Total Marks')
                    ->sortable(),

                TextColumn::make('percentage')
                    ->label('Percentage')
                    ->formatStateUsing(fn ($record) => 
                        $record->total_marks > 0 
                            ? round(($record->obtained_marks / $record->total_marks) * 100, 2) . '%'
                            : '0%'
                    )
                    ->sortable(),

                BadgeColumn::make('grade')
                    ->colors([
                        'success' => ['A+', 'A'],
                        'warning' => ['B+', 'B'],
                        'primary' => ['C+', 'C'],
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
                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->student) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('exam_type')
                    ->options([
                        'quiz' => 'Quiz',
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'assignment' => 'Assignment',
                        'test' => 'Test',
                    ]),

                SelectFilter::make('grade')
                    ->options([
                        'A+' => 'A+',
                        'A' => 'A',
                        'B+' => 'B+',
                        'B' => 'B',
                        'C+' => 'C+',
                        'C' => 'C',
                        'D' => 'D',
                        'F' => 'F',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for students
            ])
            ->defaultSort('exam_date', 'desc');
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
            'index' => Pages\ListMyGrades::route('/'),
            'view' => Pages\ViewMyGrades::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isStudent() && $user->student;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create grades
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit grades
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete grades
    }
}
