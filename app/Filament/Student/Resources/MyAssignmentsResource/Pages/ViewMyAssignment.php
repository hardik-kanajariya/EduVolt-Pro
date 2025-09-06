<?php

namespace App\Filament\Student\Resources\MyAssignmentsResource\Pages;

use App\Filament\Student\Resources\MyAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyAssignment extends ViewRecord
{
    protected static string $resource = MyAssignmentsResource::class;

    public function mount($record): void
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent() || !$user->student) {
            redirect()->route('filament.student.auth.login');
        }

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submit')
                ->label('Submit Assignment')
                ->icon('heroicon-o-paper-airplane')
                ->url(fn() => route('filament.student.resources.my-assignments.submit', $this->record))
                ->visible(function () {
                    $user = Auth::user();
                    $submission = $this->record->submissions->where('student_id', $user->student->id)->first();

                    // Can submit if no submission exists or if submission is in draft
                    return !$submission || $submission->status === 'draft';
                }),
        ];
    }
}
