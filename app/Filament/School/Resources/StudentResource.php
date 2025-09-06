<?php

namespace App\Filament\School\Resources;

use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $recordTitleAttribute = 'admission_number';

    protected static ?string $navigationLabel = 'Students';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\Students\Schemas\StudentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Students\Tables\StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\Students\RelationManagers\GradesRelationManager::class,
            \App\Filament\Admin\Resources\Students\RelationManagers\AttendancesRelationManager::class,
            \App\Filament\Admin\Resources\Students\RelationManagers\AssignmentSubmissionsRelationManager::class,
            \App\Filament\Admin\Resources\Students\RelationManagers\ProgressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\School\Resources\StudentResource\Pages\ListStudents::route('/'),
            'create' => \App\Filament\School\Resources\StudentResource\Pages\CreateStudent::route('/create'),
            'view' => \App\Filament\School\Resources\StudentResource\Pages\ViewStudent::route('/{record}'),
            'edit' => \App\Filament\School\Resources\StudentResource\Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Super admins can see all students
        if ($user && $user->isSuperAdmin()) {
            return parent::getEloquentQuery()
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
        }

        // School admins can only see their school's students
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
        return $user && ($user->isSuperAdmin() || $user->hasAnyRole(['school_admin', 'principal']));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->hasAnyRole(['school_admin', 'principal']));
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->hasAnyRole(['school_admin', 'principal']) &&
            $record->school_id === $user->school_id;
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->hasAnyRole(['school_admin', 'principal']) &&
            $record->school_id === $user->school_id;
    }
}
