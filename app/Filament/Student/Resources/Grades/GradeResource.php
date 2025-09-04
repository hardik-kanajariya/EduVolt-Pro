<?php

namespace App\Filament\Student\Resources\Grades;

use App\Filament\Student\Resources\Grades\Pages\ListGrades;
use App\Filament\Student\Resources\Grades\Pages\ViewGrade;
use App\Filament\Student\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $recordTitleAttribute = 'exam_name';
    
    protected static ?string $label = 'My Grades';
    
    protected static ?string $pluralLabel = 'My Grades';

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrades::route('/'),
            'view' => ViewGrade::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Restrict to grades of the current student
        $user = Auth::user();
        if ($user && $user->student) {
            $query->where('student_id', $user->student->id);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create grades
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit grades
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete grades
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
