<?php

namespace App\Filament\School\Resources\TeacherResource\Pages;

use App\Filament\School\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the user's school
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $record = $this->getRecord();
            if ($record && $record->school_id !== $user->school_id) {
                abort(403, 'Unauthorized');
            }
        }

        return $data;
    }
}
