<?php

namespace App\Filament\Faculty\Resources\Students;

use App\Filament\Faculty\Resources\Students\Pages\ListStudents;
use App\Filament\Faculty\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?string $label = 'My Students';

    protected static ?string $pluralLabel = 'My Students';

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Restrict to students in classes taught by the current teacher
        $user = Auth::user();
        if ($user && $user->teacher) {
            $teacherClasses = $user->teacher->subjects()
                ->with('classes')
                ->get()
                ->pluck('classes')
                ->flatten()
                ->pluck('id')
                ->unique();

            $query->whereIn('class_id', $teacherClasses);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Teachers cannot create students
    }

    public static function canEdit($record): bool
    {
        return false; // Teachers cannot edit students
    }

    public static function canDelete($record): bool
    {
        return false; // Teachers cannot delete students
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
