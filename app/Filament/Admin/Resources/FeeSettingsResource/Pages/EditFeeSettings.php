<?php

namespace App\Filament\Admin\Resources\FeeSettingsResource\Pages;

use App\Filament\Admin\Resources\FeeSettingsResource;
use Filament\Resources\Pages\EditRecord;

class EditFeeSettings extends EditRecord
{
    protected static string $resource = FeeSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
