<?php

namespace App\Filament\Admin\Resources\ExamSubjects\Pages;

use App\Filament\Admin\Resources\ExamSubjects\ExamSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamSubjects extends ListRecords
{
    protected static string $resource = ExamSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
