<?php

namespace App\Filament\Admin\Resources\FeeStructureResource\Pages;

use App\Filament\Admin\Resources\FeeStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeStructure extends EditRecord
{
    protected static string $resource = FeeStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate final amount
        $data['final_amount'] = ($data['amount'] ?? 0) - ($data['discount_amount'] ?? 0) + ($data['additional_charges'] ?? 0);

        return $data;
    }
}
