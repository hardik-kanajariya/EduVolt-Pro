<?php

namespace App\Filament\Admin\Resources\FeePaymentResource\Pages;

use App\Filament\Admin\Resources\FeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeePayment extends ViewRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => $this->record->status === 'pending'),
            Actions\Action::make('printReceipt')
                ->label('Print Receipt')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function () {
                    $this->record->markAsPrinted();
                    // TODO: Generate and download PDF receipt
                }),
        ];
    }
}
