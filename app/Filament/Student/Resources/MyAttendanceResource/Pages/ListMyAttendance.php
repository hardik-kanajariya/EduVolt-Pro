<?php

namespace App\Filament\Student\Resources\MyAttendanceResource\Pages;

use App\Filament\Student\Resources\MyAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListMyAttendance extends ListRecords
{
    protected static string $resource = MyAttendanceResource::class;

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
            // Add attendance statistics widget here if needed
        ];
    }
}
