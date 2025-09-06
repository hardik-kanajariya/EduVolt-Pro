<?php

namespace App\Filament\Faculty\Resources\LibraryResource\Pages;

use App\Filament\Faculty\Resources\LibraryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLibrary extends CreateRecord
{
    protected static string $resource = LibraryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user) {
            $data['school_id'] = $user->school_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
