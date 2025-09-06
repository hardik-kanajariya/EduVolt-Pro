<?php

namespace App\Filament\Faculty\Resources\MyStudentsResource\Pages;

use App\Filament\Faculty\Resources\MyStudentsResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyStudent extends ViewRecord
{
    protected static string $resource = MyStudentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit or delete actions for teachers
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the user's school and is a student in their class
        $user = Auth::user();
        if ($user && $user->isTeacher()) {
            $record = $this->getRecord();
            if ($record && $record->student && $record->student->schoolClass) {
                // Check if teacher is assigned to this student's class
                $isAssigned = $record->student->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists();
                if (!$isAssigned || $record->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized');
                }
            }
        }

        return $data;
    }
}
