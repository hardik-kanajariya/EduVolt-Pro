<?php

namespace App\Filament\Admin\Resources\SchoolClasses\Pages;

use App\Filament\Admin\Resources\SchoolClasses\SchoolClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;

class ViewSchoolClass extends ViewRecord
{
    protected static string $resource = SchoolClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('class_profile_header')
                    ->label('')
                    ->content(function ($record): string {
                        $displayName = $record->display_name ?: "{$record->name}-{$record->section}";
                        $studentCount = $record->students?->count() ?? 0;
                        $capacity = $record->capacity ?? 0;
                        $occupancy = $capacity > 0 ? round(($studentCount / $capacity) * 100) : 0;

                        $gradeLevel = match ($record->grade_level) {
                            'kindergarten' => ' Kindergarten',
                            'primary' => ' Primary',
                            'middle' => ' Middle',
                            'secondary' => ' Secondary',
                            'higher_secondary' => ' Higher Secondary',
                            default => ' ' . ucfirst($record->grade_level ?? 'General')
                        };

                        $stream = match ($record->stream) {
                            'general' => ' General',
                            'science' => ' Science',
                            'commerce' => ' Commerce',
                            'arts' => ' Arts',
                            'vocational' => ' Vocational',
                            'special' => ' Special',
                            default => ' ' . ucfirst($record->stream ?? 'General')
                        };

                        return '<div class="text-center p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">' .
                            '<div class="w-24 h-24 rounded-full mx-auto mb-4 bg-indigo-100 flex items-center justify-center text-indigo-600 text-3xl font-bold"></div>' .
                            '<h2 class="text-2xl font-bold text-gray-800 mb-2">' . $displayName . '</h2>' .
                            '<p class="text-indigo-600 font-medium mb-2">' . $record->school?->name . '</p>' .
                            '<p class="text-gray-600 mb-4">' . $gradeLevel . ' ' . $stream . '</p>' .
                            '<div class="flex justify-center space-x-4 mb-4">' .
                            '<span class="px-3 py-1 rounded-full text-sm font-medium ' .
                            ($record->status === 'active' ? 'bg-green-100 text-green-800' : ($record->status === 'inactive' ? 'bg-red-100 text-red-800' :
                                'bg-yellow-100 text-yellow-800')) . '">' .
                            match ($record->status) {
                                'active' => ' Active',
                                'inactive' => ' Inactive',
                                'completed' => ' Completed',
                                'suspended' => ' Suspended',
                                default => ucfirst($record->status)
                            } . '</span>' .
                            '</div>' .
                            '<div class="text-2xl font-bold text-indigo-600">' . $studentCount . '/' . $capacity . ' Students (' . $occupancy . '%)</div>' .
                            '</div>';
                    })
                    ->columnSpanFull(),

                Placeholder::make('basic_info_header')
                    ->label('Basic Information')
                    ->content('')
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Class Name')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('section')
                    ->label('Section')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('display_name')
                    ->label('Display Name')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('school.name')
                    ->label('School')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('academicYear.name')
                    ->label('Academic Year')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('room_number')
                    ->label('Room Number')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('grade_level')
                    ->label('Grade Level')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'kindergarten' => ' Kindergarten',
                        'primary' => ' Primary (1-5)',
                        'middle' => ' Middle (6-8)',
                        'secondary' => ' Secondary (9-10)',
                        'higher_secondary' => ' Higher Secondary (11-12)',
                        default => ucfirst($state ?? 'General'),
                    })
                    ->columnSpan(1),

                TextInput::make('stream')
                    ->label('Stream/Track')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'general' => ' General',
                        'science' => ' Science',
                        'commerce' => ' Commerce',
                        'arts' => ' Arts/Humanities',
                        'vocational' => ' Vocational',
                        'special' => ' Special Education',
                        default => ucfirst($state ?? 'General'),
                    })
                    ->columnSpan(1),

                TextInput::make('capacity')
                    ->label('Student Capacity')
                    ->disabled()
                    ->dehydrated(false)
                    ->suffix(' students')
                    ->columnSpan(1),

                Placeholder::make('staff_info_header')
                    ->label('Staff Information')
                    ->content('')
                    ->columnSpanFull(),

                TextInput::make('classTeacher.user.name')
                    ->label('Class Teacher')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Not assigned')
                    ->columnSpan(1),

                TextInput::make('classTeacher.user.email')
                    ->label('Teacher Email')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Not available')
                    ->columnSpan(1),

                TextInput::make('fee_amount')
                    ->label('Monthly Fee')
                    ->disabled()
                    ->dehydrated(false)
                    ->prefix('')
                    ->placeholder('Not set')
                    ->columnSpan(1),

                Placeholder::make('academic_info_header')
                    ->label('Academic Information')
                    ->content('')
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Class Description')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3)
                    ->columnSpanFull(),

                Placeholder::make('subjects_header')
                    ->label('Class Subjects')
                    ->content('')
                    ->columnSpanFull(),

                Repeater::make('subjects')
                    ->label('')
                    ->schema([
                        TextInput::make('subject.name')
                            ->label('Subject Name')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('teacher.user.name')
                            ->label('Subject Teacher')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('periods_per_week')
                            ->label('Periods/Week')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('subject_type')
                            ->label('Subject Type')
                            ->disabled()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'core' => 'Core Subject',
                                'elective' => 'Elective',
                                'extra_curricular' => 'Extra-curricular',
                                default => ucfirst($state ?? 'Core'),
                            })
                            ->columnSpan(1),
                    ])
                    ->disabled()
                    ->dehydrated(false)
                    ->columns(2)
                    ->columnSpanFull(),

                Placeholder::make('timetable_header')
                    ->label('Weekly Timetable')
                    ->content('')
                    ->columnSpanFull(),

                Repeater::make('timetable_slots')
                    ->label('')
                    ->schema([
                        TextInput::make('day')
                            ->label('Day')
                            ->disabled()
                            ->formatStateUsing(fn(?string $state): string => ucfirst($state ?? ''))
                            ->columnSpan(1),

                        TextInput::make('start_time')
                            ->label('Start Time')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('end_time')
                            ->label('End Time')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('subject.name')
                            ->label('Subject')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('teacher.user.name')
                            ->label('Teacher')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('room')
                            ->label('Room')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->disabled()
                    ->dehydrated(false)
                    ->columns(3)
                    ->columnSpanFull(),

                Placeholder::make('additional_info_header')
                    ->label('Additional Information')
                    ->content('')
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3)
                    ->columnSpan(2),

                Placeholder::make('record_info_header')
                    ->label('Record Information')
                    ->content('')
                    ->columnSpanFull(),

                Placeholder::make('created_at')
                    ->label('Record Created')
                    ->content(fn($record): string => $record?->created_at?->format('F j, Y g:i A') . ' (' . $record?->created_at?->diffForHumans() . ')')
                    ->columnSpan(1),

                Placeholder::make('updated_at')
                    ->label('Last Updated')
                    ->content(fn($record): string => $record?->updated_at?->format('F j, Y g:i A') . ' (' . $record?->updated_at?->diffForHumans() . ')')
                    ->columnSpan(1),

                Placeholder::make('class_stats')
                    ->label('Class Statistics')
                    ->content(function ($record): string {
                        $studentCount = $record->students?->count() ?? 0;
                        $capacity = $record->capacity ?? 0;
                        $subjectCount = $record->subjects?->count() ?? 0;
                        $timetableSlots = is_array($record->timetable_slots) ? count($record->timetable_slots) : 0;

                        return '<div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">' .
                            '<div class="text-center"><div class="text-2xl font-bold text-blue-600">' . $studentCount . '</div><div class="text-sm text-gray-600">Students Enrolled</div></div>' .
                            '<div class="text-center"><div class="text-2xl font-bold text-green-600">' . $capacity . '</div><div class="text-sm text-gray-600">Total Capacity</div></div>' .
                            '<div class="text-center"><div class="text-2xl font-bold text-purple-600">' . $subjectCount . '</div><div class="text-sm text-gray-600">Subjects</div></div>' .
                            '<div class="text-center"><div class="text-2xl font-bold text-orange-600">' . $timetableSlots . '</div><div class="text-sm text-gray-600">Timetable Slots</div></div>' .
                            '</div>';
                    })
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
