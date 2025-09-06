<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyClassesResource\Pages;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyClassesResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'My Classes';

    protected static ?string $modelLabel = 'Class';

    protected static ?string $pluralModelLabel = 'My Classes';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user && $user->isTeacher(), function (Builder $query) use ($user) {
                $teacherId = $user->teacher?->id;
                if (!$teacherId) {
                    return $query->whereRaw('1 = 0');
                }

                // Teachers can only see classes they are assigned to teach
                $query->where(function ($q) use ($teacherId) {
                    $q->where('class_teacher_id', $teacherId)
                        ->orWhereHas('teacherSubjects', function ($subQuery) use ($teacherId) {
                            $subQuery->where('teacher_id', $teacherId)->where('status', 'active');
                        });
                })->where('school_id', $user->school_id);
            })
            ->when($user && ($user->isSchoolAdmin() || $user->isPrincipal()), function (Builder $query) use ($user) {
                // School admins and principals can see all classes in their school
                $query->where('school_id', $user->school_id);
            })
            ->with(['school', 'academicYear']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Class Information')
                    ->schema([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('section'),
                        Forms\Components\TextInput::make('room_number'),
                        Forms\Components\TextInput::make('capacity'),
                        Forms\Components\Textarea::make('description'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section')
                    ->label('Section')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Academic Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Room')
                    ->searchable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'completed' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->relationship('academicYear', 'name')
                    ->label('Academic Year'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for viewing resources
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListMyClasses::route('/'),
            'view' => Pages\ViewMyClass::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isTeacher() || $user->hasAnyRole(['school_admin', 'principal']));
    }

    public static function canCreate(): bool
    {
        // Teachers cannot create classes, only view their assigned ones
        return false;
    }

    public static function canEdit($record): bool
    {
        // Teachers cannot edit class information
        return false;
    }

    public static function canDelete($record): bool
    {
        // Teachers cannot delete classes
        return false;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasAnyRole(['school_admin', 'principal'])) {
            return $user->school_id === $record->school_id;
        }

        if ($user->isTeacher()) {
            // Teachers can view only classes they are assigned to
            return $record->classTeachers()->where('teacher_id', $user->id)->exists() &&
                $user->school_id === $record->school_id;
        }

        return false;
    }
}
