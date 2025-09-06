<?php

namespace App\Filament\Admin\Resources\FeeSettingsResource\Pages;

use App\Filament\Admin\Resources\FeeSettingsResource;
use Filament\Resources\Pages\ListRecords;

class ListFeeSettings extends ListRecords
{
    protected static string $resource = FeeSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
