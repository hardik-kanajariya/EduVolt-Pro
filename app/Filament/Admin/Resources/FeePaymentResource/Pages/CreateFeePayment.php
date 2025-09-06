<?php

namespace App\Filament\Admin\Resources\FeePaymentResource\Pages;

use App\Filament\Admin\Resources\FeePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeePayment extends CreateRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate net amount
        $data['net_amount'] = ($data['total_amount'] ?? 0)
            + ($data['late_fee_amount'] ?? 0)
            - ($data['discount_amount'] ?? 0)
            + ($data['adjustment_amount'] ?? 0);

        return $data;
    }
}
