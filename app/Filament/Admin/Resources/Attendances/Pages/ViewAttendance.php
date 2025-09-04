<?php

namespace App\Filament\Admin\Resources\Attendances\Pages;

use App\Filament\Admin\Resources\Attendances\AttendanceResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Notifications\Notification;

class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_present')
                ->label('Mark Present')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status !== 'present')
                ->action(function () {
                    $this->record->update(['status' => 'present']);
                    Notification::make()
                        ->title('Attendance Updated')
                        ->body('Student marked as present')
                        ->success()
                        ->send();
                }),

            Action::make('mark_absent')
                ->label('Mark Absent')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->status !== 'absent')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'absent']);
                    Notification::make()
                        ->title('Attendance Updated')
                        ->body('Student marked as absent')
                        ->warning()
                        ->send();
                }),

            Action::make('mark_late')
                ->label('Mark Late')
                ->icon('heroicon-m-clock')
                ->color('warning')
                ->visible(fn() => $this->record->status !== 'late')
                ->action(function () {
                    $this->record->update(['status' => 'late']);
                    Notification::make()
                        ->title('Attendance Updated')
                        ->body('Student marked as late')
                        ->warning()
                        ->send();
                }),

            EditAction::make()
                ->icon('heroicon-m-pencil-square'),

            DeleteAction::make()
                ->icon('heroicon-m-trash'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Future: Add attendance statistics widget
        ];
    }
}
