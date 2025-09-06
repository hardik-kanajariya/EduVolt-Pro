<?php

namespace App\Filament\Admin\Resources\FeeCategoryResource\Pages;

use App\Filament\Admin\Resources\FeeCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeeCategory extends ViewRecord
{
    protected static string $resource = FeeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
