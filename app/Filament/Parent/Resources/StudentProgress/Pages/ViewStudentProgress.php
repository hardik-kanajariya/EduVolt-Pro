<?php

namespace App\Filament\Parent\Resources\StudentProgress\Pages;

use App\Filament\Parent\Resources\StudentProgress\StudentProgressResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentProgress extends ViewRecord
{
    protected static string $resource = StudentProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
