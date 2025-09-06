<?php

namespace App\Filament\Admin\Resources\SchoolFinanceResource\Pages;

use App\Filament\Admin\Resources\SchoolFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchoolFinances extends ListRecords
{
    protected static string $resource = SchoolFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
