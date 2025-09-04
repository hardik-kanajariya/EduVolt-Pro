<?php

namespace App\Filament\Admin\Resources\AcademicReportResource\Pages;

use App\Filament\Admin\Resources\AcademicReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAcademicReport extends EditRecord
{
    protected static string $resource = AcademicReportResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert recipients JSON to textarea format
        if (!empty($data['recipients'])) {
            $recipients = json_decode($data['recipients'], true);
            if (is_array($recipients)) {
                $data['recipients'] = implode("\n", $recipients);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();
        
        // Parse recipients from textarea to array
        if (!empty($data['recipients'])) {
            $recipients = array_filter(array_map('trim', explode("\n", $data['recipients'])));
            $data['recipients'] = json_encode($recipients);
        }

        return $data;
    }
}
