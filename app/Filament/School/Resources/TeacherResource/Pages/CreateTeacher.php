<?php

namespace App\Filament\School\Resources\TeacherResource\Pages;

use App\Filament\School\Resources\TeacherResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure school_id is set for school admin users
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $data['school_id'] = $user->school_id;
        }

        return $data;
    }
}
