<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyAttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Attendance';

    protected static ?string $modelLabel = 'Attendance';

    protected static ?string $pluralModelLabel = 'Attendance Records';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user && $user->isTeacher(), function (Builder $query) use ($user) {
                // Teachers can only see attendance for classes they teach
                $query->whereHas('schoolClass.classTeachers', function (Builder $q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                    ->whereHas('schoolClass', function (Builder $q) use ($user) {
                        $q->where('school_id', $user->school_id);
                    });
            })
            ->when($user && $user->hasAnyRole(['school_admin', 'principal']), function (Builder $query) use ($user) {
                // School admins and principals can see all attendance in their school
                $query->whereHas('schoolClass', function (Builder $q) use ($user) {
                    $q->where('school_id', $user->school_id);
                });
            })
            ->with(['student.user', 'schoolClass']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Information')
                    ->schema([
                        Forms\Components\Select::make('school_class_id')
                            ->relationship('schoolClass', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'admission_number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'excused' => 'Excused',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('remarks')
                            ->maxLength(500),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.admission_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.user.first_name')
                    ->getStateUsing(
                        fn(Attendance $record): string =>
                        $record->student->user->first_name . ' ' . $record->student->user->last_name
                    )
                    ->searchable(['student.user.first_name', 'student.user.last_name']),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
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
            'create' => Pages\CreateMyAttendance::route('/create'),
            'view' => Pages\ViewMyAttendance::route('/{record}'),
            'edit' => Pages\EditMyAttendance::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isTeacher() || $user->hasAnyRole(['school_admin', 'principal']));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->isTeacher();
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasAnyRole(['school_admin', 'principal'])) {
            return $record->schoolClass->school_id === $user->school_id;
        }

        if ($user->isTeacher()) {
            // Teachers can edit only attendance for classes they teach
            return $record->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists() &&
                $record->schoolClass->school_id === $user->school_id;
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasAnyRole(['school_admin', 'principal'])) {
            return $record->schoolClass->school_id === $user->school_id;
        }

        if ($user->isTeacher()) {
            // Teachers can delete only attendance for classes they teach
            return $record->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists() &&
                $record->schoolClass->school_id === $user->school_id;
        }

        return false;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasAnyRole(['school_admin', 'principal'])) {
            return $record->schoolClass->school_id === $user->school_id;
        }

        if ($user->isTeacher()) {
            // Teachers can view only attendance for classes they teach
            return $record->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists() &&
                $record->schoolClass->school_id === $user->school_id;
        }

        return false;
    }
}
