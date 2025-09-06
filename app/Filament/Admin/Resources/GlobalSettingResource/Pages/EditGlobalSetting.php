<?php

namespace App\Filament\Admin\Resources\GlobalSettingResource\Pages;

use App\Filament\Admin\Resources\GlobalSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGlobalSetting extends EditRecord
{
    protected static string $resource = GlobalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert value to JSON string for editing
        if (isset($data['value'])) {
            $data['value_json'] = json_encode($data['value'], JSON_PRETTY_PRINT);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
