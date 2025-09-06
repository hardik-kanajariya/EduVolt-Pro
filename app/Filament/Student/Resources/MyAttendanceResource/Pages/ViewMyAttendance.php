<?php

namespace App\Filament\Student\Resources\MyAttendanceResource\Pages;

use App\Filament\Student\Resources\MyAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewMyAttendance extends ViewRecord
{
    protected static string $resource = MyAttendanceResource::class;

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
