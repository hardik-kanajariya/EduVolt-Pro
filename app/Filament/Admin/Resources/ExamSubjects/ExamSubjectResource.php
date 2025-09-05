<?php

namespace App\Filament\Admin\Resources\ExamSubjects;

use App\Filament\Admin\Resources\ExamSubjects\Pages\CreateExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\EditExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\ListExamSubjects;
use App\Filament\Admin\Resources\ExamSubjects\Pages\ViewExamSubject;
use App\Models\ExamSubject;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ExamSubjectResource extends Resource
{
    protected static ?string $model = ExamSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Exam Subjects';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Exam Subject Details')
                    ->schema([
                        Forms\Components\Select::make('exam_id')
                            ->label('Exam')
                            ->relationship('exam', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->relationship('schoolClass', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('exam_date')
                            ->required(),

                        Forms\Components\TimePicker::make('start_time')
                            ->required(),

                        Forms\Components\TimePicker::make('end_time')
                            ->required()
                            ->after('start_time'),

                        Forms\Components\TextInput::make('max_marks')
                            ->label('Maximum Marks')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(100),

                        Forms\Components\TextInput::make('passing_marks')
                            ->label('Passing Marks')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(40),

                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Duration (Minutes)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(180),

                        Forms\Components\TextInput::make('room_number')
                            ->label('Room/Hall'),

                        Forms\Components\Textarea::make('instructions')
                            ->label('Subject-specific Instructions')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => $state . ' min')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_marks')
                    ->label('Max Marks')
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room')
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('examMarks_count')
                    ->label('Students')
                    ->counts('examMarks')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'name')
                    ->searchable(),

                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->searchable(),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable(),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('manage_marks')
                    ->label('Manage Marks')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.exam-marks.index', ['exam_subject' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('exam_date', 'asc');
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
            'index' => ListExamSubjects::route('/'),
            'create' => CreateExamSubject::route('/create'),
            'view' => ViewExamSubject::route('/{record}'),
            'edit' => EditExamSubject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['exam', 'schoolClass', 'subject']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('exam_date', today())->count();
    }
}
