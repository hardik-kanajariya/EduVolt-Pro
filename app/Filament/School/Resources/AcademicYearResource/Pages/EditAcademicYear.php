<?php

namespace App\Filament\School\Resources\AcademicYearResource\Pages;

use App\Filament\School\Resources\AcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

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

        // If this is set as current, make sure to unset other current academic years
        if (isset($data['is_current']) && $data['is_current']) {
            \App\Models\AcademicYear::where('school_id', $data['school_id'])
                ->where('id', '!=', $this->getRecord()->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        return $data;
    }
}
