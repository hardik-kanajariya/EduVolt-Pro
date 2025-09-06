<?php

namespace App\Filament\Admin\Resources\BulkEmailResource\Pages;

use App\Filament\Admin\Resources\BulkEmailResource;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBulkEmail extends ViewRecord
{
    protected static string $resource = BulkEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn(): bool => $this->record->status === 'draft'),

            Actions\Action::make('send_now')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn(): bool => in_array($this->record->status, ['draft', 'scheduled']))
                ->action(function () {
                    $emailService = app(EmailService::class);
                    $this->record->update(['scheduled_at' => null]);
                    $emailService->processBulkEmail($this->record);
                })
                ->requiresConfirmation()
                ->modalHeading('Send Campaign Now')
                ->modalDescription('Are you sure you want to send this campaign immediately?'),

            Actions\Action::make('duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function () {
                    $newCampaign = $this->record->replicate();
                    $newCampaign->name = $this->record->name . ' (Copy)';
                    $newCampaign->status = 'draft';
                    $newCampaign->sent_count = 0;
                    $newCampaign->failed_count = 0;
                    $newCampaign->started_at = null;
                    $newCampaign->completed_at = null;
                    $newCampaign->save();

                    return redirect(BulkEmailResource::getUrl('edit', ['record' => $newCampaign]));
                }),
        ];
    }
}
