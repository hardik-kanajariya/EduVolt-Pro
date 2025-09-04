<?php

namespace App\Filament\Student\Resources\Assignments;

use App\Filament\Student\Resources\Assignments\Pages\ListAssignments;
use App\Filament\Student\Resources\Assignments\Pages\ViewAssignment;
use App\Filament\Student\Resources\Assignments\Tables\AssignmentsTable;
use App\Models\Assignment;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $recordTitleAttribute = 'title';
    
    protected static ?string $label = 'My Assignments';
    
    protected static ?string $pluralLabel = 'My Assignments';

    public static function table(Table $table): Table
    {
        return AssignmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssignments::route('/'),
            'view' => ViewAssignment::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Restrict to assignments for the current student's class
        $user = Auth::user();
        if ($user && $user->student) {
            $query->where('class_id', $user->student->class_id)
                  ->where('status', 'published'); // Only show published assignments
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create assignments
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit assignments
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete assignments
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
