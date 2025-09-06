<?php

namespace App\Filament\Admin\Resources\FeeStructureResource\Pages;

use App\Filament\Admin\Resources\FeeStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeeStructure extends ViewRecord
{
    protected static string $resource = FeeStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
