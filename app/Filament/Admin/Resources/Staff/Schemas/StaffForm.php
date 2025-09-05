<?php

namespace App\Filament\Admin\Resources\Staff\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class StaffForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                // Staff Identity Section
                Select::make('user_id')
                    ->label('User Account')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->minLength(8),
                    ])
                    ->live()
                    ->prefixIcon('heroicon-m-user')
                    ->helperText('Link this staff member to a user account')
                    ->columnSpan(2),

                TextInput::make('employee_id')
                    ->label('Employee ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefixIcon('heroicon-m-identification')
                    ->placeholder('EMP-2024-001')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state && !str_contains($state, '-')) {
                            $year = date('Y');
                            $number = str_pad($state, 3, '0', STR_PAD_LEFT);
                            $set('employee_id', "EMP-{$year}-{$number}");
                        }
                    })
                    ->helperText('Unique identifier for the staff member')
                    ->columnSpan(2),

                // School Assignment
                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->helperText('Assign staff member to a school')
                    ->columnSpanFull(),

                // Position & Department
                TextInput::make('position')
                    ->label('Position')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-m-briefcase')
                    ->placeholder('e.g., Administrative Assistant, IT Support')
                    ->datalist([
                        'Administrative Assistant',
                        'Principal',
                        'Vice Principal',
                        'Librarian',
                        'IT Support',
                        'Accountant',
                        'Security Guard',
                        'Maintenance Staff',
                        'Counselor',
                        'Nurse',
                        'Coordinator',
                    ])
                    ->columnSpan(2),

                TextInput::make('department')
                    ->label('Department')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-m-building-office')
                    ->placeholder('e.g., Administration, IT, Finance')
                    ->datalist([
                        'Administration',
                        'Academic Affairs',
                        'Student Affairs',
                        'IT Department',
                        'Finance',
                        'Human Resources',
                        'Library',
                        'Maintenance',
                        'Security',
                        'Health Services',
                        'Sports & Activities',
                    ])
                    ->columnSpan(2),

                // Employment Details
                Select::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'intern' => 'Intern',
                        'volunteer' => 'Volunteer',
                    ])
                    ->default('full_time')
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-clock')
                    ->columnSpan(1),

                DatePicker::make('join_date')
                    ->label('Join Date')
                    ->required()
                    ->native(false)
                    ->maxDate(now())
                    ->prefixIcon('heroicon-m-calendar-days')
                    ->helperText('Date when the staff member joined')
                    ->columnSpan(1),

                TextInput::make('salary')
                    ->label('Salary')
                    ->numeric()
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-currency-dollar')
                    ->placeholder('Annual salary amount')
                    ->visible(fn($get) => in_array($get('employment_type'), ['full_time', 'part_time', 'contract']))
                    ->columnSpan(2),

                // Status & Settings
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                        'on_leave' => 'On Leave',
                        'resigned' => 'Resigned',
                    ])
                    ->default('active')
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-flag')
                    ->columnSpan(2),

                // Responsibilities Section
                Repeater::make('responsibilities')
                    ->label('Key Responsibilities')
                    ->schema([
                        TextInput::make('responsibility')
                            ->label('Responsibility')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Describe a key responsibility...')
                            ->prefixIcon('heroicon-m-check-circle'),
                    ])
                    ->defaultItems(3)
                    ->addActionLabel('Add Responsibility')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['responsibility'] ?? null)
                    ->columnSpanFull(),

                // Additional Information
                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->placeholder('Any additional information about the staff member...')
                    ->rows(4)
                    ->columnSpanFull(),

                // Employment Summary
                Placeholder::make('employment_summary')
                    ->label('Employment Summary')
                    ->content(function ($get): HtmlString {
                        $joinDate = $get('join_date');
                        $position = $get('position');
                        $department = $get('department');
                        $employmentType = $get('employment_type');
                        $salary = $get('salary');

                        if (!$joinDate || !$position) {
                            return new HtmlString('<div class="p-4 bg-gray-50 rounded-lg text-gray-500">Fill in the basic details to see employment summary</div>');
                        }

                        $yearsOfService = $joinDate ? \Carbon\Carbon::parse($joinDate)->diffInYears(now()) : 0;
                        $formattedSalary = $salary ? '$' . number_format($salary) : 'Not specified';
                        $employmentTypeLabel = match ($employmentType) {
                            'full_time' => 'Full Time',
                            'part_time' => 'Part Time',
                            'contract' => 'Contract',
                            'temporary' => 'Temporary',
                            'intern' => 'Intern',
                            'volunteer' => 'Volunteer',
                            default => ucfirst($employmentType)
                        };

                        return new HtmlString('<div class="p-4 bg-blue-50 rounded-lg border border-blue-200">' .
                            '<div class="grid grid-cols-2 gap-4">' .
                            '<div><strong>Position:</strong> ' . $position . '</div>' .
                            '<div><strong>Department:</strong> ' . $department . '</div>' .
                            '<div><strong>Employment Type:</strong> ' . $employmentTypeLabel . '</div>' .
                            '<div><strong>Years of Service:</strong> ' . $yearsOfService . ' years</div>' .
                            '<div><strong>Annual Salary:</strong> ' . $formattedSalary . '</div>' .
                            '<div><strong>Join Date:</strong> ' . \Carbon\Carbon::parse($joinDate)->format('M j, Y') . '</div>' .
                            '</div>' .
                            '</div>');
                    })
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }
}
