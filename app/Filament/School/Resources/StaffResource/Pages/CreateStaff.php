<?php

namespace App\Filament\School\Resources\StaffResource\Pages;

use App\Filament\School\Resources\StaffResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure school_id is set for school admin users
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $data['school_id'] = $user->school_id;
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Assign the selected role to the user
        if ($this->record && isset($this->data['roles'])) {
            $this->record->assignRole($this->data['roles']);
        }
    }
}
