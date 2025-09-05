<?php

namespace App\Filament\Admin\Resources\StudentProgress;

use App\Filament\Admin\Resources\StudentProgress\Pages\ListStudentProgress;
use App\Filament\Admin\Resources\StudentProgress\Pages\CreateStudentProgress;
use App\Filament\Admin\Resources\StudentProgress\Pages\EditStudentProgress;
use App\Filament\Admin\Resources\StudentProgress\Pages\ViewStudentProgress;
use App\Models\StudentProgress;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicYear;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StudentProgressResource extends Resource
{
    protected static ?string $model = StudentProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationLabel = 'Student Progress';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Progress Details')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->searchable()
                            ->preload()
                            ->options(fn () => Student::with('user')
                                ->get()
                                ->mapWithKeys(fn (Student $student) => [
                                    $student->id => $student->user?->name ?? 'Unknown',
                                ])
                                ->toArray()
                            )
                            ->getSearchResultsUsing(fn (string $search) => Student::query()
                                ->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                                ->with('user')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn (Student $student) => [
                                    $student->id => $student->user?->name ?? 'Unknown',
                                ])
                                ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => Student::with('user')->find($value)?->user?->name)
                            ->required(),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('Academic Year')
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('term')
                            ->options([
                                'first' => 'First Term',
                                'second' => 'Second Term',
                                'third' => 'Third Term',
                                'annual' => 'Annual',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('total_marks')
                            ->label('Total Marks')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Forms\Components\TextInput::make('obtained_marks')
                            ->label('Obtained Marks')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $totalMarks = $get('total_marks');
                                if ($totalMarks && $state !== null) {
                                    $percentage = ($state / $totalMarks) * 100;
                                    $set('percentage', round($percentage, 2));

                                    // Calculate grade
                                    $grade = self::calculateGrade($percentage);
                                    $set('grade', $grade);
                                }
                            }),

                        Forms\Components\TextInput::make('percentage')
                            ->label('Percentage')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('grade')
                            ->label('Grade')
                            ->disabled(),

                        Forms\Components\TextInput::make('attendance_percentage')
                            ->label('Attendance Percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\Select::make('performance_level')
                            ->label('Performance Level')
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'satisfactory' => 'Satisfactory',
                                'needs_improvement' => 'Needs Improvement',
                                'poor' => 'Poor',
                            ]),

                        Forms\Components\Textarea::make('teacher_comments')
                            ->label('Teacher Comments')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('areas_of_improvement')
                            ->label('Areas of Improvement')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('strengths')
                            ->label('Strengths')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('last_updated_at')
                            ->label('Last Updated')
                            ->required()
                            ->default(today()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('term')
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('obtained_marks')
                    ->label('Marks')
                    ->formatStateUsing(fn($state, $record) => $state . '/' . $record->total_marks)
                    ->sortable(),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('performance_level')
                    ->label('Performance')
                    ->colors([
                        'success' => 'excellent',
                        'info' => 'good',
                        'warning' => 'satisfactory',
                        'danger' => ['needs_improvement', 'poor'],
                    ]),

                Tables\Columns\TextColumn::make('attendance_percentage')
                    ->label('Attendance')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_updated_at')
                    ->label('Last Updated')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'name')
                    ->searchable(),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable(),

                SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'name')
                    ->searchable(),

                SelectFilter::make('term')
                    ->options([
                        'first' => 'First Term',
                        'second' => 'Second Term',
                        'third' => 'Third Term',
                        'annual' => 'Annual',
                    ]),

                SelectFilter::make('performance_level')
                    ->label('Performance Level')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'satisfactory' => 'Satisfactory',
                        'needs_improvement' => 'Needs Improvement',
                        'poor' => 'Poor',
                    ]),

                Filter::make('class')
                    ->form([
                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->options(\App\Models\SchoolClass::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['class_id'],
                            fn(Builder $query, $classId): Builder => $query->whereHas('student', function (Builder $query) use ($classId) {
                                $query->where('class_id', $classId);
                            })
                        );
                    }),

                Filter::make('percentage_range')
                    ->form([
                        Forms\Components\TextInput::make('min_percentage')
                            ->label('Minimum Percentage')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_percentage')
                            ->label('Maximum Percentage')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_percentage'],
                                fn(Builder $query, $percentage): Builder => $query->where('percentage', '>=', $percentage),
                            )
                            ->when(
                                $data['max_percentage'],
                                fn(Builder $query, $percentage): Builder => $query->where('percentage', '<=', $percentage),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_updated_at', 'desc');
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
            'index' => ListStudentProgress::route('/'),
            'create' => CreateStudentProgress::route('/create'),
            'view' => ViewStudentProgress::route('/{record}'),
            'edit' => EditStudentProgress::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student.schoolClass', 'academicYear', 'subject']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('last_updated_at', today())->count();
    }

    private static function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 30) return 'D';
        return 'F';
    }
}
