<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-m-eye')
                ->color('info'),

            Action::make('generate_report')
                ->label('Generate Report')
                ->icon('heroicon-m-document-text')
                ->color('success')
                ->action(function () {
                    Notification::make()
                        ->title('Staff Report Generated')
                        ->body('Employment report has been generated successfully.')
                        ->success()
                        ->send();
                }),

            Action::make('send_notification')
                ->label('Send Notification')
                ->icon('heroicon-m-bell')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Textarea::make('message')
                        ->label('Message')
                        ->required()
                        ->placeholder('Enter notification message...')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    Notification::make()
                        ->title('Notification Sent')
                        ->body("Message sent to {$this->record->user->name}: {$data['message']}")
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->icon('heroicon-m-trash')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete this staff member? This action cannot be undone.'),

            ForceDeleteAction::make()
                ->icon('heroicon-m-x-mark')
                ->requiresConfirmation(),

            RestoreAction::make()
                ->icon('heroicon-m-arrow-uturn-left'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Staff Updated')
            ->body('The staff member has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-format employee_id if needed
        if (isset($data['employee_id']) && !str_contains($data['employee_id'], '-')) {
            $year = date('Y');
            $number = str_pad($data['employee_id'], 3, '0', STR_PAD_LEFT);
            $data['employee_id'] = "EMP-{$year}-{$number}";
        }

        // Log status changes for audit purposes
        if (isset($data['status']) && $this->record->status !== $data['status']) {
            Log::info('Staff status changed', [
                'staff_id' => $this->record->id,
                'employee_id' => $this->record->employee_id,
                'old_status' => $this->record->status,
                'new_status' => $data['status'],
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Send notification for important status changes
        if ($this->record->wasChanged('status')) {
            $statusChange = $this->record->getChanges()['status'] ?? null;

            if (in_array($statusChange, ['terminated', 'resigned', 'on_leave'])) {
                Notification::make()
                    ->title('Status Change Alert')
                    ->body("Staff member {$this->record->user->name} status changed to: " . ucfirst($statusChange))
                    ->warning()
                    ->persistent()
                    ->send();
            }
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Update Staff')
                ->icon('heroicon-m-check-circle'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-m-x-mark'),

            Action::make('save_and_continue')
                ->label('Save & Continue Editing')
                ->icon('heroicon-m-arrow-path')
                ->color('gray')
                ->action('save')
                ->after(function () {
                    Notification::make()
                        ->title('Saved')
                        ->body('Staff information saved. Continue editing.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
