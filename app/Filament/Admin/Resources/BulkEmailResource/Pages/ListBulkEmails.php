<?php

namespace App\Filament\Admin\Resources\BulkEmailResource\Pages;

use App\Filament\Admin\Resources\BulkEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkEmails extends ListRecords
{
    protected static string $resource = BulkEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
