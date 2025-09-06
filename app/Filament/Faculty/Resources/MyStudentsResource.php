<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyStudentsResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyStudentsResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'My Students';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'My Students';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'student');
            })
            ->when($user && $user->isTeacher(), function (Builder $query) use ($user) {
                // Teachers can only see students from classes they are assigned to teach
                $query->whereHas('student.schoolClass.classTeachers', function (Builder $q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                    ->where('school_id', $user->school_id);
            })
            ->when($user && $user->hasAnyRole(['school_admin', 'principal']), function (Builder $query) use ($user) {
                // School admins and principals can see all students in their school
                $query->where('school_id', $user->school_id);
            })
            ->with(['school', 'student.schoolClass']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name'),
                        Forms\Components\TextInput::make('last_name'),
                        Forms\Components\TextInput::make('email'),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('student.admission_number'),
                        Forms\Components\TextInput::make('student.schoolClass.name'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.admission_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->getStateUsing(fn(User $record): string => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.schoolClass.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.schoolClass.section')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class')
                    ->relationship('student.schoolClass', 'name')
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for viewing resources
            ])
            ->defaultSort('student.admission_number');
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
            'index' => Pages\ListMyStudents::route('/'),
            'view' => Pages\ViewMyStudent::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isTeacher() || $user->hasAnyRole(['school_admin', 'principal']));
    }

    public static function canCreate(): bool
    {
        // Teachers cannot create students
        return false;
    }

    public static function canEdit($record): bool
    {
        // Teachers cannot edit student information
        return false;
    }

    public static function canDelete($record): bool
    {
        // Teachers cannot delete students
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
            // Teachers can view only students from classes they are assigned to
            return $record->student &&
                $record->student->schoolClass &&
                $record->student->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists() &&
                $user->school_id === $record->school_id;
        }

        return false;
    }
}
