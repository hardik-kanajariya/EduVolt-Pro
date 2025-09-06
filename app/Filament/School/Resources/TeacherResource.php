<?php

namespace App\Filament\School\Resources;

use App\Models\Teacher;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $recordTitleAttribute = 'employee_id';

    protected static ?string $navigationLabel = 'Teachers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\Teachers\Schemas\TeacherForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Teachers\Tables\TeachersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\Teachers\RelationManagers\SubjectsRelationManager::class,
            \App\Filament\Admin\Resources\Teachers\RelationManagers\AssignedClassesRelationManager::class,
            \App\Filament\Admin\Resources\Teachers\RelationManagers\AssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\School\Resources\TeacherResource\Pages\ListTeachers::route('/'),
            'create' => \App\Filament\School\Resources\TeacherResource\Pages\CreateTeacher::route('/create'),
            'view' => \App\Filament\School\Resources\TeacherResource\Pages\ViewTeacher::route('/{record}'),
            'edit' => \App\Filament\School\Resources\TeacherResource\Pages\EditTeacher::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Super admins can see all teachers
        if ($user && $user->isSuperAdmin()) {
            return parent::getEloquentQuery()
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
        }

        // School admins can only see their school's teachers
        return parent::getEloquentQuery()
            ->where('school_id', $user->school_id)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return static::getModel()::where('status', 'active')->count();
        }

        return static::getModel()::where('school_id', $user->school_id)
            ->where('status', 'active')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->hasRole('school_admin') || $user->hasRole('principal'));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->hasRole('school_admin') || $user->hasRole('principal'));
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        return $user && ($user->hasRole('school_admin') || $user->hasRole('principal')) &&
            $record->school_id === $user->school_id;
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        return $user && ($user->hasRole('school_admin') || $user->hasRole('principal')) &&
            $record->school_id === $user->school_id;
    }
}
