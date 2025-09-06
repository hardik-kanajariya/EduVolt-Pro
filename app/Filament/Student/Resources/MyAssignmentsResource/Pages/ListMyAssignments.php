<?php

namespace App\Filament\Student\Resources\MyAssignmentsResource\Pages;

use App\Filament\Student\Resources\MyAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListMyAssignments extends ListRecords
{
    protected static string $resource = MyAssignmentsResource::class;

    public function mount(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent() || !$user->student) {
            redirect()->route('filament.student.auth.login');
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action for students
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add assignment statistics widget here if needed
        ];
    }
}
