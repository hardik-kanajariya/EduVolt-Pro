<?php

namespace App\Filament\Admin\Resources\StudentFeeAssignmentResource\Pages;

use App\Filament\Admin\Resources\StudentFeeAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentFeeAssignment extends ViewRecord
{
    protected static string $resource = StudentFeeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
