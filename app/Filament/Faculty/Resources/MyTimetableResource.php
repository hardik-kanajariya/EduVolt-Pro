<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyTimetableResource\Pages;
use App\Models\Timetable;
use Filament\Forms;
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

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Timetable';

    protected static ?string $slug = 'my-timetable';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->isTeacher()) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('teacher_id', $user->id)
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Teachers can only view their timetable, not edit
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'primary' => 'Monday',
                        'success' => 'Tuesday',
                        'warning' => 'Wednesday',
                        'info' => 'Thursday',
                        'danger' => 'Friday',
                        'secondary' => 'Saturday',
                        'gray' => 'Sunday',
                    ]),

                TextColumn::make('period')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),

                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('academicYear.year')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ]),

                SelectFilter::make('class_id')
                    ->relationship('schoolClass', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $classIds = $user->assignedClasses()->pluck('school_classes.id');
                            $query->whereIn('id', $classIds)
                                ->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('day')
            ->defaultSort('period')
            ->bulkActions([]);
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
        return $user && $user->isTeacher();
    }

    public static function canCreate(): bool
    {
        return false; // Teachers cannot create timetable entries
    }

    public static function canEdit($record): bool
    {
        return false; // Teachers cannot edit timetable entries
    }

    public static function canDelete($record): bool
    {
        return false; // Teachers cannot delete timetable entries
    }
}
