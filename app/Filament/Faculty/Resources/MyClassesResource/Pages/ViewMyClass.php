<?php

namespace App\Filament\Faculty\Resources\MyClassesResource\Pages;

use App\Filament\Faculty\Resources\MyClassesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyClass extends ViewRecord
{
    protected static string $resource = MyClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit or delete actions for teachers
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the user's school and is assigned to teacher
        $user = Auth::user();
        if ($user && $user->isTeacher()) {
            $record = $this->getRecord();
            if ($record) {
                // Check if teacher is assigned to this class
                $isAssigned = $record->classTeachers()->where('teacher_id', $user->id)->exists();
                if (!$isAssigned || $record->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized');
                }
            }
        }

        return $data;
    }
}
