<?php

namespace App\Filament\Admin\Resources\AssignmentSubmissionResource\Pages;

use App\Filament\Admin\Resources\AssignmentSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditAssignmentSubmission extends EditRecord
{
    protected static string $resource = AssignmentSubmissionResource::class;

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
        if ($data['status'] === 'graded' && !$this->record->graded_at) {
            $data['graded_at'] = now();
            $data['graded_by'] = Auth::id();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->status === 'graded') {
            Notification::make()
                ->title('Assignment graded successfully')
                ->success()
                ->send();
        }
    }
}
