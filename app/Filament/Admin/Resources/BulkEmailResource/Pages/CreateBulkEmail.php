<?php

namespace App\Filament\Admin\Resources\BulkEmailResource\Pages;

use App\Filament\Admin\Resources\BulkEmailResource;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBulkEmail extends CreateRecord
{
    protected static string $resource = BulkEmailResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sender_id'] = Auth::id();
        $data['status'] = $data['scheduled_at'] ? 'scheduled' : 'draft';

        // Convert form criteria to proper format
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

    protected function afterCreate(): void
    {
        // Build recipient list after creation
        $this->record->buildRecipientList();

        // If not scheduled, queue for immediate sending
        if (!$this->record->scheduled_at) {
            $emailService = app(EmailService::class);
            // Process in background for large campaigns
            if ($this->record->recipient_count > 50) {
                // Would dispatch a job here in production
                // dispatch(new ProcessBulkEmailJob($this->record));
            } else {
                $emailService->processBulkEmail($this->record);
            }
        }
    }
}
