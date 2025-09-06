<?php

namespace App\Filament\Admin\Resources\SchoolFinanceResource\Pages;

use App\Filament\Admin\Resources\SchoolFinanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolFinance extends EditRecord
{
    protected static string $resource = SchoolFinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate profit/loss
        $data['profit_loss'] = ($data['revenue'] ?? 0) - ($data['expenses'] ?? 0);

        return $data;
    }
}
