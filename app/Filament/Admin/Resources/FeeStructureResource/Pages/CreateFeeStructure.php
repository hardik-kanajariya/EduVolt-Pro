<?php

namespace App\Filament\Admin\Resources\FeeStructureResource\Pages;

use App\Filament\Admin\Resources\FeeStructureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeStructure extends CreateRecord
{
    protected static string $resource = FeeStructureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate final amount
        $data['final_amount'] = ($data['amount'] ?? 0) - ($data['discount_amount'] ?? 0) + ($data['additional_charges'] ?? 0);

        return $data;
    }
}
