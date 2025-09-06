<?php

namespace App\Filament\Admin\Widgets;

use App\Models\School;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SchoolSwitcher extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.admin.widgets.school-switcher';

    protected int | string | array $columnSpan = 'full';

    public ?int $selectedSchoolId = null;

    public function mount(): void
    {
        // Get current school context from session or user's default school
        $this->selectedSchoolId = Session::get('admin_school_context', Auth::user()->school_id);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedSchoolId')
                ->label('Switch School Context')
                ->options(function () {
                    if (!Auth::user()->isSuperAdmin()) {
                        return [];
                    }

                    $schools = School::all();
                    $options = ['all' => '🌍 All Schools (Global View)'];

                    foreach ($schools as $school) {
                        $options[$school->id] = "🏫 {$school->name}";
                    }

                    return $options;
                })
                ->searchable()
                ->placeholder('Select a school context...')
                ->helperText('Switch between schools to manage school-specific data. Select "All Schools" for global management.')
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->switchSchool($state);
                }),
        ];
    }

    public function switchSchool($schoolId): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            return;
        }

        if ($schoolId === 'all') {
            Session::put('admin_school_context', null);
            $this->js('location.reload()');
        } else {
            Session::put('admin_school_context', $schoolId);
            $this->js('location.reload()');
        }
    }

    public static function canView(): bool
    {
        return Auth::user()->isSuperAdmin();
    }
}
