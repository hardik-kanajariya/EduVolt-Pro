<?php

namespace App\Filament\Admin\Resources\EmailLogResource\Pages;

use App\Filament\Admin\Resources\EmailLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailLog extends ViewRecord
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('retry')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn(): bool => $this->record->status === 'failed')
                ->action(function () {
                    $emailService = app(\App\Services\EmailService::class);
                    $emailService->processSingleEmail($this->record);
                })
                ->requiresConfirmation()
                ->modalHeading('Retry Email')
                ->modalDescription('Are you sure you want to retry sending this email?'),
        ];
    }
}
