<?php

namespace App\Filament\School\Resources\AcademicYearResource\Pages;

use App\Filament\School\Resources\AcademicYearResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure school_id is set for school admin users
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->school_id) {
            $data['school_id'] = $user->school_id;
        }

        // If this is set as current, make sure to unset other current academic years
        if (isset($data['is_current']) && $data['is_current']) {
            \App\Models\AcademicYear::where('school_id', $data['school_id'])
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        return $data;
    }
}
