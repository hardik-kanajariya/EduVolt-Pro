<?php

namespace App\Filament\Parent\Resources\ChildGrades\Pages;

use App\Filament\Parent\Resources\ChildGrades\ChildGradesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class ViewChildGrade extends ViewRecord
{
    protected static string $resource = ChildGradesResource::class;

    public function mount($record): void
    {
        $user = Auth::user();

        if (!$user || !$user->isParent()) {
            redirect()->route('filament.parent.auth.login');
        }

        parent::mount($record);

        // Verify user has access to this grade (belongs to their child)
        $childrenIds = Student::where('parent_email', $user->email)
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->pluck('id');

        if (!$childrenIds->contains($this->record->student_id)) {
            abort(403, 'You do not have permission to view this grade.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // No edit/delete actions for parents
        ];
    }
}
