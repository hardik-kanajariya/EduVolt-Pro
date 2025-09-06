<?php

namespace App\Filament\Faculty\Resources\MyTimetableResource\Pages;

use App\Filament\Faculty\Resources\MyTimetableResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyTimetable extends ViewRecord
{
    protected static string $resource = MyTimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the current teacher
        $user = Auth::user();
        if ($user && $user->isTeacher()) {
            $record = $this->getRecord();
            if ($record && ($record->teacher_id !== $user->id || $record->school_id !== $user->school_id)) {
                abort(403, 'Unauthorized');
            }
        }

        return $data;
    }
}
