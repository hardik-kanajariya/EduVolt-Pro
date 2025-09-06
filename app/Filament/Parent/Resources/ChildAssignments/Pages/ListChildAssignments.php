<?php

namespace App\Filament\Parent\Resources\ChildAssignments\Pages;

use App\Filament\Parent\Resources\ChildAssignments\ChildAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class ListChildAssignments extends ListRecords
{
    protected static string $resource = ChildAssignmentsResource::class;

    public function mount(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isParent()) {
            redirect()->route('filament.parent.auth.login');
        }

        // Verify user has children
        $hasChildren = Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->exists();

        if (!$hasChildren) {
            abort(403, 'No student records found associated with your account.');
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action for parents
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add assignment statistics widget here if needed
        ];
    }
}
