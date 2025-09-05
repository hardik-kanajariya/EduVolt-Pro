<?php

namespace App\Filament\Admin\Pages;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Period;
use App\Models\Timetable;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class TimetableBuilder extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.admin.pages.timetable-builder';

    protected static ?string $navigationLabel = 'Timetable Builder';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public ?SchoolClass $selectedClass = null;

    public array $timetableGrid = [];

    public array $periods = [];

    public array $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    public function mount(): void
    {
        $this->periods = Period::orderBy('start_time')->get()->toArray();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Select Class')
                    ->schema([
                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->options(SchoolClass::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->loadTimetable($state)),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadTimetable(?int $classId): void
    {
        if (!$classId) {
            $this->timetableGrid = [];
            return;
        }

        $this->selectedClass = SchoolClass::find($classId);
        
        // Initialize grid
        $this->timetableGrid = [];
        
        foreach ($this->days as $day) {
            $this->timetableGrid[$day] = [];
            foreach ($this->periods as $period) {
                $existingTimetable = Timetable::where([
                    'class_id' => $classId,
                    'day_of_week' => $day,
                    'period_id' => $period['id'],
                ])->with(['subject', 'teacher'])->first();

                $this->timetableGrid[$day][$period['id']] = [
                    'id' => $existingTimetable?->id,
                    'subject_id' => $existingTimetable?->subject_id,
                    'teacher_id' => $existingTimetable?->teacher_id,
                    'subject_name' => $existingTimetable?->subject?->name ?? '',
                    'teacher_name' => $existingTimetable?->teacher?->name ?? '',
                    'room_number' => $existingTimetable?->room_number ?? '',
                ];
            }
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Timetable')
                ->action('saveTimetable')
                ->color('success'),

            Action::make('clear')
                ->label('Clear All')
                ->action('clearTimetable')
                ->color('danger')
                ->requiresConfirmation(),
        ];
    }

    public function saveTimetable(): void
    {
        if (empty($this->data['class_id'])) {
            Notification::make()
                ->title('Please select a class first')
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('Timetable builder feature is under development')
            ->info()
            ->send();
    }

    public function clearTimetable(): void
    {
        if (!empty($this->data['class_id'])) {
            Timetable::where('class_id', $this->data['class_id'])->delete();
            $this->loadTimetable($this->data['class_id']);

            Notification::make()
                ->title('Timetable cleared successfully')
                ->success()
                ->send();
        }
    }

    public function getViewData(): array
    {
        return [
            'totalTimetables' => Timetable::count(),
            'totalClasses' => SchoolClass::count(),
            'totalSubjects' => Subject::count(),
            'totalTeachers' => Teacher::count(),
            'totalPeriods' => Period::count(),
            'timetableGrid' => $this->timetableGrid,
            'periods' => $this->periods,
            'days' => $this->days,
        ];
    }
}
