<?php

namespace App\Filament\Student\Resources\MyTimetableResource\Pages;

use App\Filament\Student\Resources\MyTimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListMyTimetable extends ListRecords
{
    protected static string $resource = MyTimetableResource::class;

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
            // Add timetable widgets here if needed
        ];
    }
}
