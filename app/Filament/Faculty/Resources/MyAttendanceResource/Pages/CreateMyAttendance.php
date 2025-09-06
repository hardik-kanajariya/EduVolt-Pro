<?php

namespace App\Filament\Faculty\Resources\MyAttendanceResource\Pages;

use App\Filament\Faculty\Resources\MyAttendanceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyAttendance extends CreateRecord
{
    protected static string $resource = MyAttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure the attendance is being created for a class the teacher teaches
        $user = Auth::user();
        if ($user && $user->isTeacher() && isset($data['school_class_id'])) {
            $schoolClass = \App\Models\SchoolClass::find($data['school_class_id']);
            if ($schoolClass) {
                $isAssigned = $schoolClass->classTeachers()->where('teacher_id', $user->id)->exists();
                if (!$isAssigned || $schoolClass->school_id !== $user->school_id) {
                    abort(403, 'Unauthorized - You can only create attendance for classes you teach');
                }
            }
        }

        return $data;
    }
}
