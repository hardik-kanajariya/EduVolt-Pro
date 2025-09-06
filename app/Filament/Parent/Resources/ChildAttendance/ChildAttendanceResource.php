<?php

namespace App\Filament\Parent\Resources\ChildAttendance;

use App\Filament\Parent\Resources\ChildAttendance\Pages;
use App\Models\Attendance;
use App\Models\Student;
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

class ChildAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Children\'s Attendance';

    protected static ?string $slug = 'child-attendance';

    protected static ?string $navigationGroup = 'Attendance';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Get children associated with the parent's email
        $childrenIds = Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('student_id', $childrenIds)
            ->with(['student.user', 'student.class', 'markedBy']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Parents can only view attendance, not edit it
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Child Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.class.name')
                    ->label('Class')
                    ->sortable(),

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
                SelectFilter::make('student_id')
                    ->label('Child')
                    ->options(function () {
                        $user = Auth::user();
                        return Student::where('parent_email', $user->email)
                            ->orWhereHas('user', function ($query) use ($user) {
                                $query->where('email', $user->email);
                            })
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id');
                    }),

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

                Filter::make('absent_days')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('status', 'absent')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for parents
            ])
            ->defaultSort('date', 'desc')
            ->groups([
                Tables\Grouping\Group::make('student.user.name')
                    ->label('Child')
                    ->collapsible(),
                Tables\Grouping\Group::make('status')
                    ->label('Status')
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
            'index' => Pages\ListChildAttendance::route('/'),
            'view' => Pages\ViewChildAttendance::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->isParent()) {
            return false;
        }

        // Verify user has children
        return Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->exists();
    }

    public static function canCreate(): bool
    {
        return false; // Parents cannot create attendance records
    }

    public static function canEdit($record): bool
    {
        return false; // Parents cannot edit attendance records
    }

    public static function canDelete($record): bool
    {
        return false; // Parents cannot delete attendance records
    }
}
