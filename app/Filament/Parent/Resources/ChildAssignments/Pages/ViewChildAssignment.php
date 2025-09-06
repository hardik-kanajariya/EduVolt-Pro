<?php

namespace App\Filament\Parent\Resources\ChildAssignments\Pages;

use App\Filament\Parent\Resources\ChildAssignments\ChildAssignmentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class ViewChildAssignment extends ViewRecord
{
    protected static string $resource = ChildAssignmentsResource::class;

    public function mount($record): void
    {
        $user = Auth::user();

        if (!$user || !$user->isParent()) {
            redirect()->route('filament.parent.auth.login');
        }

        parent::mount($record);

        // Verify user has access to this assignment (children are in the class)
        $childrenClassIds = Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->pluck('class_id');

        if (!$childrenClassIds->contains($this->record->class_id)) {
            abort(403, 'You do not have permission to view this assignment.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // No edit/delete actions for parents
        ];
    }
}
