<?php

namespace App\Filament\Faculty\Resources\Grades;

use App\Filament\Faculty\Resources\Grades\GradeResource\Pages;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Grades';
    protected static ?string $navigationLabel = 'Student Grades';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('Student')
                                    ->relationship('student.user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->options(function () {
                                        $teacher = Auth::user()->teacher;
                                        if (!$teacher) return [];

                                        // Get students from classes assigned to this teacher
                                        return Student::whereHas('schoolClass.subjects', function ($query) use ($teacher) {
                                            $query->whereHas('teachers', function ($subQuery) use ($teacher) {
                                                $subQuery->where('teachers.id', $teacher->id);
                                            });
                                        })->with('user')->get()->pluck('user.name', 'id');
                                    }),

                                Forms\Components\Select::make('subject_id')
                                    ->label('Subject')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->options(function () {
                                        $teacher = Auth::user()->teacher;
                                        if (!$teacher) return [];

                                        return $teacher->subjects()->pluck('name', 'id');
                                    }),

                                Forms\Components\Select::make('class_id')
                                    ->label('Class')
                                    ->relationship('schoolClass', 'name')
                                    ->required()
                                    ->options(function () {
                                        $teacher = Auth::user()->teacher;
                                        if (!$teacher) return [];

                                        return SchoolClass::whereHas('subjects.teachers', function ($query) use ($teacher) {
                                            $query->where('teachers.id', $teacher->id);
                                        })->pluck('name', 'id');
                                    }),

                                Forms\Components\Select::make('exam_type')
                                    ->label('Exam Type')
                                    ->options([
                                        'unit_test' => 'Unit Test',
                                        'mid_term' => 'Mid Term',
                                        'final_exam' => 'Final Exam',
                                        'assignment' => 'Assignment',
                                        'practical' => 'Practical',
                                        'project' => 'Project',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('marks_obtained')
                                    ->label('Marks Obtained')
                                    ->numeric()
                                    ->required()
                                    ->rules(['min:0']),

                                Forms\Components\TextInput::make('total_marks')
                                    ->label('Total Marks')
                                    ->numeric()
                                    ->required()
                                    ->rules(['min:1'])
                                    ->default(100),

                                Forms\Components\TextInput::make('percentage')
                                    ->label('Percentage')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('grade')
                                    ->label('Grade')
                                    ->maxLength(2)
                                    ->placeholder('A+, A, B+, etc.'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pass' => 'Pass',
                                        'fail' => 'Fail',
                                        'absent' => 'Absent',
                                        'pending' => 'Pending',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),

                        Forms\Components\DatePicker::make('exam_date')
                            ->label('Exam Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.admission_number')
                    ->label('Admission No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam_type')
                    ->label('Exam Type')
                    ->formatStateUsing(fn($state) => str_replace('_', ' ', ucwords($state)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('marks_display')
                    ->label('Marks')
                    ->state(fn($record) => "{$record->marks_obtained}/{$record->total_marks}")
                    ->sortable(['marks_obtained', 'total_marks']),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->state(fn($record) => $record->total_marks > 0 ? round(($record->marks_obtained / $record->total_marks) * 100, 2) . '%' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color(fn(string $state): string => match (strtoupper($state)) {
                        'A+', 'A' => 'success',
                        'B+', 'B' => 'info',
                        'C+', 'C' => 'warning',
                        'D', 'F' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pass' => 'success',
                        'fail' => 'danger',
                        'absent' => 'secondary',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('exam_date')
                    ->label('Exam Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name'),

                Tables\Filters\SelectFilter::make('exam_type')
                    ->options([
                        'unit_test' => 'Unit Test',
                        'mid_term' => 'Mid Term',
                        'final_exam' => 'Final Exam',
                        'assignment' => 'Assignment',
                        'practical' => 'Practical',
                        'project' => 'Project',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pass' => 'Pass',
                        'fail' => 'Fail',
                        'absent' => 'Absent',
                        'pending' => 'Pending',
                    ]),
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
            ->defaultSort('exam_date', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teacher = Auth::user()->teacher;

        return parent::getEloquentQuery()
            ->when($teacher, function ($query) use ($teacher) {
                // Only show grades for subjects taught by this teacher
                return $query->whereHas('subject.teachers', function ($subQuery) use ($teacher) {
                    $subQuery->where('teachers.id', $teacher->id);
                });
            })
            ->with(['student.user', 'subject', 'schoolClass']);
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
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'view' => Pages\ViewGrade::route('/{record}'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
