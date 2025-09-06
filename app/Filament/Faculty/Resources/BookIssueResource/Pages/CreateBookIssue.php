<?php

namespace App\Filament\Faculty\Resources\BookIssueResource\Pages;

use App\Filament\Faculty\Resources\BookIssueResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBookIssue extends CreateRecord
{
    protected static string $resource = BookIssueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user) {
            $data['school_id'] = $user->school_id;
            $data['issue_date'] = $data['issue_date'] ?? now();
            $data['due_date'] = $data['due_date'] ?? now()->addDays(14);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
