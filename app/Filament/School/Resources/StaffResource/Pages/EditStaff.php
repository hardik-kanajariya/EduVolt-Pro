<?php

namespace App\Filament\School\Resources\StaffResource\Pages;

use App\Filament\School\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

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

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Update role if changed
        if ($this->record && isset($this->data['roles'])) {
            $this->record->syncRoles([$this->data['roles']]);
        }
    }
}
