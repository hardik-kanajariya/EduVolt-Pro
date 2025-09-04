<?php

namespace App\Filament\Admin\Resources\AcademicReportResource\Pages;

use App\Filament\Admin\Resources\AcademicReportResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAcademicReport extends CreateRecord
{
 protected static string $resource = AcademicReportResource::class;

 protected function mutateFormDataBeforeCreate(array $data): array
 {
 $data['created_by'] = Auth::id();
 $data['status'] = 'pending';

 // Parse recipients from textarea to array
 if (!empty($data['recipients'])) {
 $recipients = array_filter(array_map('trim', explode("\n", $data['recipients'])));
 $data['recipients'] = json_encode($recipients);
 }

 return $data;
 }

 protected function getRedirectUrl(): string
 {
 return $this->getResource()::getUrl('index');
 }
}
