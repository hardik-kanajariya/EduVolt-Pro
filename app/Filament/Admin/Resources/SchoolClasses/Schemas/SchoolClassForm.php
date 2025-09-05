<?php

namespace App\Filament\Admin\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class SchoolClassForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select school for this class...')
                    ->helperText(' School where this class operates')
                    ->live()
                    ->columnSpan(2),

                Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select academic year...')
                    ->helperText(' Academic year for this class')
                    ->live()
                    ->columnSpan(1),

                TextInput::make('name')
                    ->label('Class Name')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g., Grade 1, Class 10, Kindergarten')
                    ->helperText(' Primary class/grade identifier')
                    ->live()
                    ->columnSpan(1),

                TextInput::make('section')
                    ->label('Section')
                    ->required()
                    ->maxLength(10)
                    ->default('A')
                    ->placeholder('e.g., A, B, C, Red, Blue')
                    ->helperText(' Section or division within the class')
                    ->live()
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))
                    ->columnSpan(1),

                TextInput::make('display_name')
                    ->label('Display Name')
                    ->maxLength(100)
                    ->placeholder('e.g., Grade 1-A, Class 10 Science, KG-Red')
                    ->helperText(' Full display name (auto-generated if empty)')
                    ->live()
                    ->columnSpan(1),

                TextInput::make('capacity')
                    ->label('Student Capacity')
                    ->required()
                    ->numeric()
                    ->default(30)
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('students')
                    ->placeholder('30')
                    ->helperText(' Maximum number of students')
                    ->columnSpan(1),

                TextInput::make('room_number')
                    ->label('Room Number')
                    ->maxLength(20)
                    ->placeholder('e.g., 101, A-201, Lab-1')
                    ->helperText(' Assigned classroom/room')
                    ->columnSpan(1),

                Select::make('grade_level')
                    ->label('Grade Level')
                    ->options([
                        'kindergarten' => ' Kindergarten',
                        'primary' => ' Primary (1-5)',
                        'middle' => ' Middle (6-8)',
                        'secondary' => ' Secondary (9-10)',
                        'higher_secondary' => ' Higher Secondary (11-12)',
                    ])
                    ->required()
                    ->helperText('Education level classification')
                    ->columnSpan(1),

                Select::make('stream')
                    ->label('Stream/Track')
                    ->options([
                        'general' => ' General',
                        'science' => ' Science',
                        'commerce' => ' Commerce',
                        'arts' => ' Arts/Humanities',
                        'vocational' => ' Vocational',
                        'special' => ' Special Education',
                    ])
                    ->default('general')
                    ->helperText('Academic stream or specialization')
                    ->columnSpan(1),

                Select::make('class_teacher_id')
                    ->label('Class Teacher')
                    ->relationship('classTeacher', 'user.name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select class teacher...')
                    ->helperText(' Primary teacher responsible for this class')
                    ->columnSpan(1),

                TextInput::make('fee_amount')
                    ->label('Monthly Fee')
                    ->numeric()
                    ->prefix('')
                    ->placeholder('5000')
                    ->helperText(' Monthly fee for this class (optional)')
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Class Status')
                    ->options([
                        'active' => ' Active',
                        'inactive' => ' Inactive',
                        'completed' => ' Completed',
                        'suspended' => ' Suspended',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Current operational status')
                    ->columnSpan(1),

                Textarea::make('description')
                    ->label('Class Description')
                    ->rows(3)
                    ->placeholder('Brief description of the class, special features, focus areas...')
                    ->helperText(' Additional information about this class')
                    ->columnSpanFull(),

                Repeater::make('subjects')
                    ->label('Class Subjects')
                    ->schema([
                        Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Select::make('teacher_id')
                            ->label('Subject Teacher')
                            ->relationship('teacher', 'user.name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('periods_per_week')
                            ->label('Periods/Week')
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(20)
                            ->columnSpan(1),

                        Select::make('subject_type')
                            ->label('Subject Type')
                            ->options([
                                'core' => 'Core Subject',
                                'elective' => 'Elective',
                                'extra_curricular' => 'Extra-curricular',
                            ])
                            ->default('core')
                            ->columnSpan(1),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable()
                    ->helperText(' Subjects taught in this class'),

                Repeater::make('timetable_slots')
                    ->label('Weekly Timetable')
                    ->schema([
                        Select::make('day')
                            ->label('Day')
                            ->options([
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                            ])
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('start_time')
                            ->label('Start Time')
                            ->type('time')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('end_time')
                            ->label('End Time')
                            ->type('time')
                            ->required()
                            ->columnSpan(1),

                        Select::make('subject_id')
                            ->label('Subject')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Select::make('teacher_id')
                            ->label('Teacher')
                            ->relationship('teacher', 'user.name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('room')
                            ->label('Room')
                            ->maxLength(20)
                            ->placeholder('101, Lab-A')
                            ->columnSpan(1),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable()
                    ->helperText(' Weekly schedule for this class'),

                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->rows(2)
                    ->placeholder('Any special instructions, requirements, or notes about this class...')
                    ->helperText(' Special notes or instructions')
                    ->columnSpan(2),

                Placeholder::make('created_at')
                    ->label('Record Created')
                    ->content(fn($record): string => $record?->created_at?->format('M j, Y g:i A') ?? 'Not yet created')
                    ->columnSpan(1),

                Placeholder::make('updated_at')
                    ->label('Last Updated')
                    ->content(fn($record): string => $record?->updated_at?->format('M j, Y g:i A') ?? 'Not yet updated')
                    ->columnSpan(1),

                Placeholder::make('class_stats')
                    ->label('Quick Statistics')
                    ->content(function ($record): HtmlString {
                        if (!$record) return 'Stats will be available after creation';

                        $studentCount = $record->students?->count() ?? 0;
                        $capacity = $record->capacity ?? 0;
                        $occupancy = $capacity > 0 ? round(($studentCount / $capacity) * 100) : 0;
                        $subjects = $record->subjects?->count() ?? 0;

                        return " Students: {$studentCount}/{$capacity} ({$occupancy}%) | Subjects: {$subjects}";
                    })
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
