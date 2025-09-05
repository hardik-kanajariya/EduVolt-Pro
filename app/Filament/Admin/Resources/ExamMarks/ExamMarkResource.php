<?php

namespace App\Filament\Admin\Resources\ExamMarks;

use App\Filament\Admin\Resources\ExamMarks\Pages\CreateExamMark;
use App\Filament\Admin\Resources\ExamMarks\Pages\EditExamMark;
use App\Filament\Admin\Resources\ExamMarks\Pages\ListExamMarks;
use App\Filament\Admin\Resources\ExamMarks\Pages\ViewExamMark;
use App\Models\ExamMark;
use App\Models\ExamSubject;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExamMarkResource extends Resource
{
    protected static ?string $model = ExamMark::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Exam Marks';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Exam Mark Details')
                    ->schema([
                        Forms\Components\Select::make('exam_subject_id')
                            ->label('Exam Subject')
                            ->relationship('examSubject')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->exam->name . ' - ' . $record->subject->name . ' (' . $record->schoolClass->name . ')')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('marks_obtained')
                            ->label('Marks Obtained')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $examSubject = ExamSubject::find($get('exam_subject_id'));
                                if ($examSubject && $state !== null) {
                                    $percentage = ($state / $examSubject->max_marks) * 100;
                                    $set('percentage', round($percentage, 2));
                                    
                                    // Calculate grade based on percentage
                                    $grade = self::calculateGrade($percentage);
                                    $set('grade', $grade);
                                    
                                    // Set pass/fail status
                                    $set('is_passed', $state >= $examSubject->passing_marks);
                                }
                            }),

                        Forms\Components\TextInput::make('percentage')
                            ->label('Percentage')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('grade')
                            ->label('Grade')
                            ->disabled(),

                        Forms\Components\Toggle::make('is_passed')
                            ->label('Passed')
                            ->disabled(),

                        Forms\Components\Toggle::make('is_absent')
                            ->label('Absent')
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state) {
                                    $set('marks_obtained', 0);
                                    $set('percentage', 0);
                                    $set('grade', 'F');
                                    $set('is_passed', false);
                                }
                            }),

                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('entered_by')
                            ->default(Auth::id() ?? null),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('examSubject.exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('examSubject.subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('marks_obtained')
                    ->label('Marks')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->is_absent ? 'Absent' : $state . '/' . $record->examSubject->max_marks),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->is_absent ? 'Absent' : $state . '%'),

                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_passed')
                    ->label('Passed'),

                Tables\Columns\BooleanColumn::make('is_absent')
                    ->label('Absent'),

                Tables\Columns\TextColumn::make('enteredBy.name')
                    ->label('Entered By')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam')
                    ->relationship('examSubject.exam', 'name')
                    ->searchable(),

                SelectFilter::make('subject')
                    ->relationship('examSubject.subject', 'name')
                    ->searchable(),

                SelectFilter::make('class')
                    ->relationship('student.schoolClass', 'name')
                    ->searchable(),

                SelectFilter::make('is_passed')
                    ->label('Result')
                    ->options([
                        '1' => 'Passed',
                        '0' => 'Failed',
                    ]),

                SelectFilter::make('is_absent')
                    ->label('Attendance')
                    ->options([
                        '0' => 'Present',
                        '1' => 'Absent',
                    ]),

                Filter::make('marks_range')
                    ->form([
                        Forms\Components\TextInput::make('min_marks')
                            ->label('Minimum Marks')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_marks')
                            ->label('Maximum Marks')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_marks'],
                                fn (Builder $query, $marks): Builder => $query->where('marks_obtained', '>=', $marks),
                            )
                            ->when(
                                $data['max_marks'],
                                fn (Builder $query, $marks): Builder => $query->where('marks_obtained', '<=', $marks),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => ListExamMarks::route('/'),
            'create' => CreateExamMark::route('/create'),
            'view' => ViewExamMark::route('/{record}'),
            'edit' => EditExamMark::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['examSubject.exam', 'examSubject.subject', 'student.schoolClass', 'enteredBy']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
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
