<?php

namespace App\Filament\Admin\Resources\SchoolFinanceResource\Pages;

use App\Filament\Admin\Resources\SchoolFinanceResource;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;

class CreateSchoolFinance extends CreateRecord
{
    protected static string $resource = SchoolFinanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default month_year if not provided
        if (empty($data['month_year'])) {
            $data['month_year'] = Carbon::now()->format('Y-m');
        }

        // Calculate profit/loss
        $data['profit_loss'] = ($data['revenue'] ?? 0) - ($data['expenses'] ?? 0);

        return $data;
    }
}
