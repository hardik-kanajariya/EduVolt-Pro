<?php

namespace App\Filament\School\Resources\TeacherResource\Pages;

use App\Filament\School\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure school_id is maintained
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $data['school_id'] = $user->school_id;
        }

        return $data;
    }
}
