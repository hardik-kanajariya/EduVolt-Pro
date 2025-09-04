<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Staff Member Created')
            ->body('The new staff member has been added successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate employee_id if not provided
        if (empty($data['employee_id'])) {
            $year = date('Y');
            $lastStaff = \App\Models\Staff::whereYear('created_at', $year)
                ->orderBy('created_at', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastStaff && preg_match('/EMP-\d{4}-(\d+)/', $lastStaff->employee_id, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }

            $data['employee_id'] = "EMP-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // Auto-format employee_id if provided without prefix
        if (isset($data['employee_id']) && !str_contains($data['employee_id'], '-')) {
            $year = date('Y');
            $number = str_pad($data['employee_id'], 3, '0', STR_PAD_LEFT);
            $data['employee_id'] = "EMP-{$year}-{$number}";
        }

        // Set default join_date if not provided
        if (empty($data['join_date'])) {
            $data['join_date'] = now()->toDateString();
        }

        // Set default status
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send welcome notification
        Notification::make()
            ->title('Welcome New Staff Member!')
            ->body("Welcome {$this->record->user->name} to the team as {$this->record->position}")
            ->success()
            ->persistent()
            ->send();

        // Log the creation
        Log::info('New staff member created', [
            'staff_id' => $this->record->id,
            'employee_id' => $this->record->employee_id,
            'name' => $this->record->user->name,
            'position' => $this->record->position,
            'department' => $this->record->department,
            'created_by' => Auth::id(),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Create Staff Member')
                ->icon('heroicon-m-plus-circle'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-m-x-mark'),

            Action::make('create_and_create_another')
                ->label('Create & Create Another')
                ->icon('heroicon-m-plus')
                ->color('gray')
                ->action('create')
                ->after(function () {
                    $this->redirect($this->getResource()::getUrl('create'));
                }),
        ];
    }
}
