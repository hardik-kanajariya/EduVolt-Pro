<?php

namespace App\Filament\Parent\Resources\Students;

use App\Filament\Parent\Resources\Students\Pages\ListStudents;
use App\Filament\Parent\Resources\Students\Pages\ViewStudent;
use App\Filament\Parent\Resources\Students\Tables\StudentsTable;
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

    protected static ?string $label = 'My Children';

    protected static ?string $pluralLabel = 'My Children';

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'view' => ViewStudent::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Restrict to children of the current parent
        $user = Auth::user();
        if ($user) {
            // Assuming parent email matches parent_email in students table
            $query->where('parent_email', $user->email);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Parents cannot create student records
    }

    public static function canEdit($record): bool
    {
        return false; // Parents cannot edit student records
    }

    public static function canDelete($record): bool
    {
        return false; // Parents cannot delete student records
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
