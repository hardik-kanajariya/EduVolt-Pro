<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\MyTimetableResource\Pages;
use App\Models\Timetable;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MyTimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'My Timetable';

    protected static ?string $slug = 'my-timetable';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent() || !$user->student) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('class_id', $user->student->class_id)
            ->where('school_id', $user->school_id)
            ->with(['subject', 'teacher']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Students can only view timetable, not edit it
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start_time')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('room_number')
                    ->label('Room')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('period_number')
                    ->label('Period')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('Day')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday',
                    ]),

                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for students
            ])
            ->defaultSort('day_of_week', 'asc')
            ->groups([
                Tables\Grouping\Group::make('day_of_week')
                    ->label('Day')
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
            'index' => Pages\ListMyTimetable::route('/'),
            'view' => Pages\ViewMyTimetable::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isStudent() && $user->student;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create timetable entries
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit timetable entries
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete timetable entries
    }
}
