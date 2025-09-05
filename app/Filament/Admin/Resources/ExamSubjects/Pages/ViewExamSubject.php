<?php

namespace App\Filament\Admin\Resources\ExamSubjects\Pages;

use App\Filament\Admin\Resources\ExamSubjects\ExamSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExamSubject extends ViewRecord
{
    protected static string $resource = ExamSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
