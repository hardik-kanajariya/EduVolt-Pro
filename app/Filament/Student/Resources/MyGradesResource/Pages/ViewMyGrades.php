<?php

namespace App\Filament\Student\Resources\MyGradesResource\Pages;

use App\Filament\Student\Resources\MyGradesResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyGrades extends ViewRecord
{
    protected static string $resource = MyGradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No header actions for students
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the current student
        $user = Auth::user();
        if ($user && $user->isStudent() && $user->student) {
            $record = $this->getRecord();
            if ($record && ($record->student_id !== $user->student->id || $record->school_id !== $user->school_id)) {
                abort(403, 'Unauthorized');
            }
        }

        return $data;
    }
}
