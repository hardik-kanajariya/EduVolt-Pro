<?php

namespace App\Filament\Admin\Resources\ExamMarks\Pages;

use App\Filament\Admin\Resources\ExamMarks\ExamMarkResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExamMark extends ViewRecord
{
    protected static string $resource = ExamMarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
