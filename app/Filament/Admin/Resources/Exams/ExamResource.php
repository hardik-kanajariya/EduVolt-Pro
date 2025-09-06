<?php

namespace App\Filament\Admin\Resources\Exams;

use App\Filament\Admin\Resources\Exams\Pages\CreateExam;
use App\Filament\Admin\Resources\Exams\Pages\EditExam;
use App\Filament\Admin\Resources\Exams\Pages\ListExams;
use App\Filament\Admin\Resources\Exams\Pages\ViewExam;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\School;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Exams';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Exam Details')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Academic Year')
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('school_id')
                            ->label('School')
                            ->relationship('school', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mid-term Examination'),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Brief description of the examination')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'midterm' => 'Mid-term',
                                'final' => 'Final',
                                'unit_test' => 'Unit Test',
                                'quarterly' => 'Quarterly',
                                'half_yearly' => 'Half Yearly',
                                'annual' => 'Annual',
                                'practice' => 'Practice Test',
                            ]),

                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->minDate(today()),

                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'ongoing' => 'Ongoing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('total_marks')
                            ->label('Total Marks')
                            ->numeric()
                            ->minValue(0)
                            ->default(100),

                        Forms\Components\TextInput::make('passing_marks')
                            ->label('Passing Marks')
                            ->numeric()
                            ->minValue(0)
                            ->default(40),

                        Forms\Components\Textarea::make('instructions')
                            ->label('Exam Instructions')
                            ->rows(4)
                            ->placeholder('Special instructions for students')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publish Exam')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->visible(fn(Forms\Get $get) => $get('is_published')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Grade Scale')
                    ->schema([
                        Forms\Components\Repeater::make('grade_scale')
                            ->schema([
                                Forms\Components\TextInput::make('grade')
                                    ->required()
                                    ->placeholder('A+'),
                                Forms\Components\TextInput::make('min_marks')
                                    ->label('Minimum Marks')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('max_marks')
                                    ->label('Maximum Marks')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('gpa')
                                    ->label('GPA')
                                    ->numeric()
                                    ->step(0.01),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->defaultItems(0)
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('school.name')
                    ->label('School')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'secondary',
                        'scheduled' => 'warning',
                        'ongoing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_marks')
                    ->label('Total Marks')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('examSubjects_count')
                    ->label('Subjects')
                    ->counts('examSubjects')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'name')
                    ->searchable(),

                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable(),

                SelectFilter::make('type')
                    ->options([
                        'midterm' => 'Mid-term',
                        'final' => 'Final',
                        'unit_test' => 'Unit Test',
                        'quarterly' => 'Quarterly',
                        'half_yearly' => 'Half Yearly',
                        'annual' => 'Annual',
                        'practice' => 'Practice Test',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('manage_subjects')
                    ->label('Manage Subjects')
                    ->icon('heroicon-o-book-open')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.exam-subjects.index', ['exam' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
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
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'view' => ViewExam::route('/{record}'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['academicYear', 'school']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'ongoing')->count();
    }
}
