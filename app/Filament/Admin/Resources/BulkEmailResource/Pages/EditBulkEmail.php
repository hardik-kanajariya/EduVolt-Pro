<?php

namespace App\Filament\Admin\Resources\BulkEmailResource\Pages;

use App\Filament\Admin\Resources\BulkEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkEmail extends EditRecord
{
    protected static string $resource = BulkEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn(): bool => $this->record->status === 'draft'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert recipient criteria to form format
        if (isset($data['recipient_criteria'])) {
            $criteria = $data['recipient_criteria'];
            $formCriteria = [];

            if (isset($criteria['roles'])) {
                $formCriteria[] = [
                    'type' => 'role',
                    'roles' => $criteria['roles'],
                ];
            }

            if (isset($criteria['class'])) {
                $formCriteria[] = [
                    'type' => 'class',
                    'class_name' => $criteria['class'],
                ];
            }

            if (isset($criteria['emails'])) {
                $formCriteria[] = [
                    'type' => 'email_list',
                    'email_list' => implode("\n", $criteria['emails']),
                ];
            }

            $data['recipient_criteria_form'] = $formCriteria;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert form criteria back to proper format
        if (isset($data['recipient_criteria_form'])) {
            $criteria = [];
            foreach ($data['recipient_criteria_form'] as $criterion) {
                switch ($criterion['type']) {
                    case 'role':
                        $criteria['roles'] = $criterion['roles'] ?? [];
                        break;
                    case 'class':
                        $criteria['class'] = $criterion['class_name'] ?? '';
                        break;
                    case 'email_list':
                        $emails = array_map(
                            'trim',
                            preg_split('/[,\n\r]+/', $criterion['email_list'] ?? '')
                        );
                        $criteria['emails'] = array_filter($emails);
                        break;
                    case 'all_students':
                        $criteria['roles'] = ['student'];
                        break;
                    case 'all_parents':
                        $criteria['roles'] = ['parent'];
                        break;
                    case 'all_faculty':
                        $criteria['roles'] = ['faculty'];
                        break;
                }
            }
            $data['recipient_criteria'] = $criteria;
        }

        unset($data['recipient_criteria_form']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Rebuild recipient list if criteria changed
        $this->record->buildRecipientList();
    }
}
