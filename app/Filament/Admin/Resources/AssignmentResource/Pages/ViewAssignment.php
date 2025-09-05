<?php

namespace App\Filament\Admin\Resources\AssignmentResource\Pages;

use App\Filament\Admin\Resources\AssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignment extends ViewRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
