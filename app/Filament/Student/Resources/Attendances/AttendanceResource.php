<?php

namespace App\Filament\Student\Resources\Attendances;

use App\Filament\Student\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Student\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $label = 'My Attendance';

    protected static ?string $pluralLabel = 'My Attendance';

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Restrict to attendance records of the current student and school
        $user = Auth::user();
        if ($user && $user->student) {
            $query->where('student_id', $user->student->id)
                ->where('school_id', $user->school_id);
        } else {
            // If no student record, return empty query
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Students cannot create attendance records
    }

    public static function canEdit($record): bool
    {
        return false; // Students cannot edit attendance
    }

    public static function canDelete($record): bool
    {
        return false; // Students cannot delete attendance
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
