<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\MyAttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class MyAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Attendance';

    protected static ?string $slug = 'my-attendance';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 2;

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
            // Students can only view attendance, not edit it
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'primary' => 'excused',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('in_time')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('out_time')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('remarks')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('markedBy.name')
                    ->label('Marked By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                    ]),

                Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from_date'),
                        \Filament\Forms\Components\DatePicker::make('to_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Filter::make('this_month')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                    ),

                Filter::make('this_week')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereBetween('date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for students
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListMyAttendance::route('/'),
            'view' => Pages\ViewMyAttendance::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isStudent() && $user->student;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create attendance records
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit attendance records
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete attendance records
    }
}
