<?php

namespace App\Filament\Admin\Resources\GlobalSettingResource\Pages;

use App\Filament\Admin\Resources\GlobalSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalSetting extends CreateRecord
{
    protected static string $resource = GlobalSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Parse JSON value
        if (!empty($data['value_json'])) {
            try {
                $data['value'] = json_decode($data['value_json'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON format');
                }
            } catch (\Exception $e) {
                // If it's not valid JSON, treat as string
                $data['value'] = [$data['value_json']];
            }
        } else {
            $data['value'] = [];
        }

        // Remove temporary field
        unset($data['value_json']);

        return $data;
    }
}
