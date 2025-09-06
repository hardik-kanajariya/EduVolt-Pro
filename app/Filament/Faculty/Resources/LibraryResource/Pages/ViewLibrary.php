<?php

namespace App\Filament\Faculty\Resources\LibraryResource\Pages;

use App\Filament\Faculty\Resources\LibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewLibrary extends ViewRecord
{
    protected static string $resource = LibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure the record belongs to the current school
        $user = Auth::user();
        if ($user) {
            $record = $this->getRecord();
            if ($record && $record->school_id !== $user->school_id) {
                abort(403, 'Unauthorized');
            }
        }

        return $data;
    }
}
