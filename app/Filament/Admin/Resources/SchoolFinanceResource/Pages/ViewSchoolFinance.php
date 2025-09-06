<?php

namespace App\Filament\Admin\Resources\SchoolFinanceResource\Pages;

use App\Filament\Admin\Resources\SchoolFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSchoolFinance extends ViewRecord
{
    protected static string $resource = SchoolFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
