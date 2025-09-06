<?php

namespace App\Filament\Faculty\Resources\Timetable;

use App\Filament\Faculty\Resources\Timetable\TimetableResource\Pages;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Schedule';
    protected static ?string $navigationLabel = 'My Timetable';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
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

                                Forms\Components\Select::make('subject_id')
                                    ->label('Subject')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->options(function () {
                                        $teacher = Auth::user()->teacher;
                                        if (!$teacher) return [];

                                        return $teacher->subjects()->pluck('name', 'id');
                                    }),

                                Forms\Components\Select::make('day_of_week')
                                    ->label('Day of Week')
                                    ->options([
                                        'monday' => 'Monday',
                                        'tuesday' => 'Tuesday',
                                        'wednesday' => 'Wednesday',
                                        'thursday' => 'Thursday',
                                        'friday' => 'Friday',
                                        'saturday' => 'Saturday',
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('period_id')
                                    ->label('Period')
                                    ->relationship('period', 'name')
                                    ->required()
                                    ->options(Period::orderBy('start_time')->pluck('name', 'id')),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->required(),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('room_number')
                                    ->label('Room Number')
                                    ->maxLength(20),

                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'lecture' => 'Lecture',
                                        'practical' => 'Practical',
                                        'tutorial' => 'Tutorial',
                                        'lab' => 'Lab',
                                        'exam' => 'Exam',
                                        'assembly' => 'Assembly',
                                    ])
                                    ->default('lecture')
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
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
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.name')
                    ->label('Period')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time_display')
                    ->label('Time')
                    ->state(fn($record) => $record->start_time->format('H:i') . ' - ' . $record->end_time->format('H:i'))
                    ->sortable(['start_time', 'end_time']),

                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'lecture' => 'primary',
                        'practical' => 'info',
                        'tutorial' => 'success',
                        'lab' => 'warning',
                        'exam' => 'danger',
                        'assembly' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                    ]),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name'),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'lecture' => 'Lecture',
                        'practical' => 'Practical',
                        'tutorial' => 'Tutorial',
                        'lab' => 'Lab',
                        'exam' => 'Exam',
                        'assembly' => 'Assembly',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('day_of_week');
    }

    public static function getEloquentQuery(): Builder
    {
        $teacher = Auth::user()->teacher;

        return parent::getEloquentQuery()
            ->when($teacher, function ($query) use ($teacher) {
                // Only show timetable entries for this teacher
                return $query->where('teacher_id', $teacher->id);
            })
            ->with(['schoolClass', 'subject', 'period']);
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
            'index' => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'view' => Pages\ViewTimetable::route('/{record}'),
            'edit' => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }
}
