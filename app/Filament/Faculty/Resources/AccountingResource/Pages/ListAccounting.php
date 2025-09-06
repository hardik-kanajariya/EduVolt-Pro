<?php

namespace App\Filament\Faculty\Resources\AccountingResource\Pages;

use App\Filament\Faculty\Resources\AccountingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccounting extends ListRecords
{
    protected static string $resource = AccountingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
