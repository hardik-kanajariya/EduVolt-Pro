<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Period;

class TimetableBuilder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Timetable Builder';

    protected static ?string $navigationLabel = 'Timetable Builder';

    protected static string $view = 'filament.admin.pages.timetable-builder';

    public function getHeaderActions(): array
    {
        return [
            Action::make('create_timetable')
                ->label('Create New Timetable')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->url(route('filament.admin.resources.timetables.create')),

            Action::make('view_all_timetables')
                ->label('View All Timetables')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(route('filament.admin.resources.timetables.index')),
        ];
    }

    public function getViewData(): array
    {
        return [
            'totalTimetables' => Timetable::count(),
            'totalClasses' => SchoolClass::count(),
            'totalSubjects' => Subject::count(),
            'totalTeachers' => Teacher::count(),
            'totalPeriods' => Period::count(),
            'recentTimetables' => Timetable::with(['class', 'subject', 'teacher', 'period'])
                ->latest()
                ->limit(5)
                ->get(),
            'classesByDay' => SchoolClass::withCount(['timetables' => function ($query) {
                $query->whereNotNull('day_of_week');
            }])->get(),
        ];
    }
}
