<?php

namespace App\Filament\Faculty\Resources\MyAssignmentsResource\Pages;

use App\Filament\Faculty\Resources\MyAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMyAssignments extends EditRecord
{
    protected static string $resource = MyAssignmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to a class the teacher teaches
        $user = Auth::user();
        if ($user && $user->isTeacher()) {
            $record = $this->getRecord();
            if ($record && $record->schoolClass) {
                $isAssigned = $record->schoolClass->classTeachers()->where('teacher_id', $user->id)->exists();
                if (!$isAssigned || $record->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized');
                }
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
