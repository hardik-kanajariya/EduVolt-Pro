<?php

namespace App\Filament\Admin\Resources\FeeCategoryResource\Pages;

use App\Filament\Admin\Resources\FeeCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeeCategory extends CreateRecord
{
    protected static string $resource = FeeCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
