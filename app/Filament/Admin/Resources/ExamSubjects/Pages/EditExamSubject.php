<?php

namespace App\Filament\Admin\Resources\ExamSubjects\Pages;

use App\Filament\Admin\Resources\ExamSubjects\ExamSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamSubject extends EditRecord
{
    protected static string $resource = ExamSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
