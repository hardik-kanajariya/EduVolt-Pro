<?php

namespace App\Filament\Student\Resources\MyTimetableResource\Pages;

use App\Filament\Student\Resources\MyTimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyTimetable extends ViewRecord
{
    protected static string $resource = MyTimetableResource::class;

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
            // No edit/delete actions for students
        ];
    }
}
