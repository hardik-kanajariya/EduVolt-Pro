<?php

namespace App\Filament\Admin\Resources\ExamMarks\Pages;

use App\Filament\Admin\Resources\ExamMarks\ExamMarkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamMark extends EditRecord
{
 protected static string $resource = ExamMarkResource::class;

 protected function getHeaderActions(): array
 {
 return [
 DeleteAction::make(),
 ];
 }
}
