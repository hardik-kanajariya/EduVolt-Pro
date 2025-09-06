<?php

namespace App\Filament\Admin\Resources\StudentFeeAssignmentResource\Pages;

use App\Filament\Admin\Resources\StudentFeeAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentFeeAssignment extends CreateRecord
{
    protected static string $resource = StudentFeeAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
