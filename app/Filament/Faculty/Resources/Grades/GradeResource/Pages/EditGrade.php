<?php

namespace App\Filament\Faculty\Resources\Grades\GradeResource\Pages;

use App\Filament\Faculty\Resources\Grades\GradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        // Recalculate percentage
        if ($data['total_marks'] > 0) {
            $percentage = ($data['marks_obtained'] / $data['total_marks']) * 100;
            $data['percentage'] = round($percentage, 2);

            // Auto-assign grade if not manually set
            if (!$data['grade']) {
                $data['grade'] = $this->calculateGrade($percentage);
            }
        }

        return $data;
    }

    private function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C+',
            $percentage >= 40 => 'C',
            $percentage >= 33 => 'D',
            default => 'F',
        };
    }
}
