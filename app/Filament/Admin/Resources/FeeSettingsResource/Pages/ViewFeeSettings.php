<?php

namespace App\Filament\Admin\Resources\FeeSettingsResource\Pages;

use App\Filament\Admin\Resources\FeeSettingsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFeeSettings extends ViewRecord
{
    protected static string $resource = FeeSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}
