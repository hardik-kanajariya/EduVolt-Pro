<?php

namespace App\Filament\Faculty\Resources\MyGradesResource\Pages;

use App\Filament\Faculty\Resources\MyGradesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyGrades extends ViewRecord
{
    protected static string $resource = MyGradesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to a student in a class the teacher teaches
        $user = Auth::user();
        if ($user && $user->isTeacher()) {
            $record = $this->getRecord();
            if ($record && $record->student && $record->student->schoolClass) {
                $isAssigned = $record->student->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists();
                if (!$isAssigned || $record->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized');
                }
            }
        }

        return $data;
    }
}
