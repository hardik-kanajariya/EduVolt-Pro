<?php

namespace App\Filament\Faculty\Resources\MyAttendanceResource\Pages;

use App\Filament\Faculty\Resources\MyAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMyAttendance extends EditRecord
{
    protected static string $resource = MyAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
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
                if (!$isAssigned || $record->schoolClass->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized');
                }
            }
        }

        return $data;
    }
}
