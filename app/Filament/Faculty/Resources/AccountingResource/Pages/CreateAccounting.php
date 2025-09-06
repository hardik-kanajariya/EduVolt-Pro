<?php

namespace App\Filament\Faculty\Resources\AccountingResource\Pages;

use App\Filament\Faculty\Resources\AccountingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAccounting extends CreateRecord
{
    protected static string $resource = AccountingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user) {
            $data['school_id'] = $user->school_id;
            $data['transaction_date'] = $data['transaction_date'] ?? now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
