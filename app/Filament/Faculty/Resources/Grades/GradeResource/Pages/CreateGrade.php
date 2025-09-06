<?php

namespace App\Filament\Faculty\Resources\Grades\GradeResource\Pages;

use App\Filament\Faculty\Resources\Grades\GradeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entered_by'] = Auth::id();

        // Calculate percentage
        if ($data['total_marks'] > 0) {
            $percentage = ($data['marks_obtained'] / $data['total_marks']) * 100;
            $data['percentage'] = round($percentage, 2);

            // Auto-assign grade based on percentage
            if (!$data['grade']) {
                $data['grade'] = $this->calculateGrade($percentage);
            }

            // Auto-assign status based on marks
            if ($data['status'] === 'pending') {
                $data['status'] = $percentage >= 40 ? 'pass' : 'fail';
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
