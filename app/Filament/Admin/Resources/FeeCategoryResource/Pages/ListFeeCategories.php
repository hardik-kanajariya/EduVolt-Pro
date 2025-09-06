<?php

namespace App\Filament\Admin\Resources\FeeCategoryResource\Pages;

use App\Filament\Admin\Resources\FeeCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeCategories extends ListRecords
{
    protected static string $resource = FeeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Fee Category')
                ->icon('heroicon-o-plus'),
        ];
    }
}
