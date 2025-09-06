<?php

namespace App\Filament\Admin\Resources\StudentFeeAssignmentResource\Pages;

use App\Filament\Admin\Resources\StudentFeeAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentFeeAssignment extends EditRecord
{
    protected static string $resource = StudentFeeAssignmentResource::class;

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
}
