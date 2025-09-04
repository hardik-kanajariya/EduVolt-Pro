<?php

namespace App\Filament\Admin\Resources\BookIssues\Pages;

use App\Filament\Admin\Resources\BookIssues\BookIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookIssues extends ListRecords
{
    protected static string $resource = BookIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
