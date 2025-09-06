<?php

namespace App\Filament\Faculty\Pages;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Subject;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class StudentPerformanceDashboard extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?string $navigationLabel = 'Student Performance';
    protected static ?int $navigationSort = 50;
    protected static string $view = 'filament.faculty.pages.student-performance-dashboard';

    public ?array $data = [];
    public ?SchoolClass $selectedClass = null;
    public ?Subject $selectedSubject = null;

    public function getTitle(): string|Htmlable
    {
        return 'Student Performance Dashboard';
    }

    public function mount(): void
    {
        $teacher = Auth::user()->teacher;
        if ($teacher) {
            $firstClass = SchoolClass::whereHas('subjects.teachers', function ($query) use ($teacher) {
                $query->where('teachers.id', $teacher->id);
            })->first();

            if ($firstClass) {
                $this->selectedClass = $firstClass;
                $this->selectedSubject = $teacher->subjects()->first();
            }
        }

        $this->form->fill([
            'class_id' => $this->selectedClass?->id,
            'subject_id' => $this->selectedSubject?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        $teacher = Auth::user()->teacher;

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('Select Class')
                                    ->options(function () use ($teacher) {
                                        if (!$teacher) return [];

                                        return SchoolClass::whereHas('subjects.teachers', function ($query) use ($teacher) {
                                            $query->where('teachers.id', $teacher->id);
                                        })->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->selectedClass = SchoolClass::find($state);
                                        $this->updateSubjectOptions();
                                    }),

                                Forms\Components\Select::make('subject_id')
                                    ->label('Select Subject')
                                    ->options(function () use ($teacher) {
                                        if (!$teacher) return [];

                                        return $teacher->subjects()->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->selectedSubject = Subject::find($state);
                                    }),
                            ]),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('admission_number')
                    ->label('Admission No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('average_grade')
                    ->label('Average Grade')
                    ->state(function ($record) {
                        if (!$this->selectedSubject) return '-';

                        $grades = Grade::where('student_id', $record->id)
                            ->where('subject_id', $this->selectedSubject->id)
                            ->get();

                        if ($grades->isEmpty()) return '-';

                        $average = $grades->avg('percentage');
                        return round($average, 1) . '%';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendance_percentage')
                    ->label('Attendance')
                    ->state(function ($record) {
                        if (!$this->selectedClass) return '-';

                        $total = Attendance::where('student_id', $record->id)
                            ->where('class_id', $this->selectedClass->id)
                            ->count();

                        if ($total === 0) return '-';

                        $present = Attendance::where('student_id', $record->id)
                            ->where('class_id', $this->selectedClass->id)
                            ->where('status', 'present')
                            ->count();

                        return round(($present / $total) * 100, 1) . '%';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignments_completed')
                    ->label('Assignments')
                    ->state(function ($record) {
                        if (!$this->selectedSubject || !$this->selectedClass) return '-';

                        $total = Assignment::where('class_id', $this->selectedClass->id)
                            ->where('subject_id', $this->selectedSubject->id)
                            ->count();

                        if ($total === 0) return '0/0';

                        $completed = $record->assignmentSubmissions()
                            ->whereHas('assignment', function ($query) {
                                $query->where('class_id', $this->selectedClass->id)
                                    ->where('subject_id', $this->selectedSubject->id);
                            })
                            ->where('status', 'submitted')
                            ->count();

                        return "$completed/$total";
                    }),

                Tables\Columns\TextColumn::make('last_grade')
                    ->label('Latest Grade')
                    ->state(function ($record) {
                        if (!$this->selectedSubject) return '-';

                        $lastGrade = Grade::where('student_id', $record->id)
                            ->where('subject_id', $this->selectedSubject->id)
                            ->latest()
                            ->first();

                        return $lastGrade ? $lastGrade->grade : '-';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match (strtoupper($state)) {
                        'A+', 'A' => 'success',
                        'B+', 'B' => 'info',
                        'C+', 'C' => 'warning',
                        'D', 'F' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('performance_trend')
                    ->label('Trend')
                    ->state(function ($record) {
                        if (!$this->selectedSubject) return '-';

                        $grades = Grade::where('student_id', $record->id)
                            ->where('subject_id', $this->selectedSubject->id)
                            ->orderBy('exam_date')
                            ->take(3)
                            ->get();

                        if ($grades->count() < 2) return '-';

                        $first = $grades->first()->percentage;
                        $last = $grades->last()->percentage;
                        $diff = $last - $first;

                        if ($diff > 5) return '↗ Improving';
                        if ($diff < -5) return '↘ Declining';
                        return '→ Stable';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        str_contains($state, 'Improving') => 'success',
                        str_contains($state, 'Declining') => 'danger',
                        str_contains($state, 'Stable') => 'info',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('performance_level')
                    ->label('Performance Level')
                    ->options([
                        'excellent' => 'Excellent (90%+)',
                        'good' => 'Good (75-89%)',
                        'average' => 'Average (60-74%)',
                        'poor' => 'Poor (<60%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value'] || !$this->selectedSubject) {
                            return $query;
                        }

                        return $query->whereHas('grades', function ($query) use ($data) {
                            $query->where('subject_id', $this->selectedSubject->id);

                            match ($data['value']) {
                                'excellent' => $query->where('percentage', '>=', 90),
                                'good' => $query->whereBetween('percentage', [75, 89]),
                                'average' => $query->whereBetween('percentage', [60, 74]),
                                'poor' => $query->where('percentage', '<', 60),
                                default => $query
                            };
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.faculty.resources.students.students.view', $record)),
            ])
            ->emptyStateHeading('No students found')
            ->emptyStateDescription('Select a class and subject to view student performance data.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    protected function getTableQuery(): Builder
    {
        $teacher = Auth::user()->teacher;

        $query = Student::query();

        if ($this->selectedClass) {
            $query->where('class_id', $this->selectedClass->id);
        } elseif ($teacher) {
            // Show students from all classes this teacher teaches
            $query->whereHas('schoolClass.subjects.teachers', function ($q) use ($teacher) {
                $q->where('teachers.id', $teacher->id);
            });
        }

        return $query->with(['user', 'grades', 'assignmentSubmissions']);
    }

    protected function updateSubjectOptions(): void
    {
        // This method can be extended to update subject options based on selected class
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
}
