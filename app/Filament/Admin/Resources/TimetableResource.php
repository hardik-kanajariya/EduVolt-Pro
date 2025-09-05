<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TimetableResource\Pages;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Timetables';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Timetable Details')
                    ->schema([
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

                        Forms\Components\Select::make('teacher_id')
                            ->label('Teacher')
                            ->relationship('teacher', 'user.name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('period_id')
                            ->label('Period')
                            ->relationship('period', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('day_of_week')
                            ->label('Day of Week')
                            ->options([
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ])
                            ->required(),

                        Forms\Components\TimePicker::make('start_time')
                            ->required(),

                        Forms\Components\TimePicker::make('end_time')
                            ->required()
                            ->after('start_time'),

                        Forms\Components\TextInput::make('room_number')
                            ->label('Room/Classroom'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Textarea::make('notes')
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
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.name')
                    ->label('Period')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room')
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->searchable(),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable(),

                SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->relationship('teacher', 'user.name')
                    ->searchable(),

                SelectFilter::make('day_of_week')
                    ->label('Day of Week')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday',
                    ]),

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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('day_of_week', 'asc');
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['schoolClass', 'subject', 'teacher', 'period']);
    }
}
