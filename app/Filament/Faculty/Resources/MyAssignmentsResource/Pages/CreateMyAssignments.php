<?php

namespace App\Filament\Faculty\Resources\MyAssignmentsResource\Pages;

use App\Filament\Faculty\Resources\MyAssignmentsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyAssignments extends CreateRecord
{
    protected static string $resource = MyAssignmentsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user) {
            $data['school_id'] = $user->school_id;
            $data['teacher_id'] = $user->teacher?->id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
