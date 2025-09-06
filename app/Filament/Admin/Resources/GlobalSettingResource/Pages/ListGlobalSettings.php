<?php

namespace App\Filament\Admin\Resources\GlobalSettingResource\Pages;

use App\Filament\Admin\Resources\GlobalSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGlobalSettings extends ListRecords
{
    protected static string $resource = GlobalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
