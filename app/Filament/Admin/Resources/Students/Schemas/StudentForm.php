<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Str;

class StudentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Student User Account')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Search and select student user account...')
                    ->helperText(' Link this student record to an existing user account')
                    ->columnSpan(2),

                FileUpload::make('user.avatar')
                    ->label('Student Photo')
                    ->image()
                    ->imageEditor()
                    ->directory('student-avatars')
                    ->visibility('public')
                    ->helperText(' Upload student photo (recommended: square format)')
                    ->columnSpan(1),

                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select school...')
                    ->helperText(' School where student is enrolled')
                    ->live()
                    ->columnSpan(1),

                Select::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select class...')
                    ->helperText(' Student\'s current class')
                    ->columnSpan(1),

                TextInput::make('admission_number')
                    ->label('Admission Number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g., ADM2025001')
                    ->helperText(' Unique admission identifier')
                    ->live()
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))
                    ->columnSpan(1),

                TextInput::make('roll_number')
                    ->label('Roll Number')
                    ->maxLength(20)
                    ->placeholder('e.g., 001, A-01')
                    ->helperText(' Class roll number')
                    ->columnSpan(1),

                DatePicker::make('admission_date')
                    ->label('Admission Date')
                    ->required()
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('M j, Y')
                    ->helperText(' Date of school admission')
                    ->default(now())
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Student Status')
                    ->options([
                        'active' => ' Active',
                        'inactive' => ' Inactive',
                        'transferred' => ' Transferred',
                        'graduated' => ' Graduated',
                        'suspended' => ' Suspended',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Current enrollment status')
                    ->columnSpan(1),

                DatePicker::make('user.date_of_birth')
                    ->label('Date of Birth')
                    ->required()
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('M j, Y')
                    ->helperText(' Student\'s birth date')
                    ->maxDate(now()->subYears(3))
                    ->columnSpan(1),

                Select::make('user.gender')
                    ->label('Gender')
                    ->options([
                        'male' => ' Male',
                        'female' => ' Female',
                        'other' => ' Other',
                    ])
                    ->required()
                    ->helperText('Student\'s gender')
                    ->columnSpan(1),

                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter full name of parent/guardian')
                    ->helperText(' Primary parent or guardian')
                    ->columnSpan(1),

                TextInput::make('parent_phone')
                    ->label('Parent Phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->placeholder('+1 (555) 123-4567')
                    ->helperText(' Primary contact number')
                    ->columnSpan(1),

                TextInput::make('parent_email')
                    ->label('Parent Email')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('parent@example.com')
                    ->helperText(' Email for school communications')
                    ->columnSpan(1),

                Textarea::make('user.address')
                    ->label('Home Address')
                    ->rows(3)
                    ->placeholder('Enter complete home address...')
                    ->helperText(' Student\'s residential address')
                    ->columnSpanFull(),

                Textarea::make('medical_info')
                    ->label('Medical Information')
                    ->rows(3)
                    ->placeholder('Enter any medical conditions, allergies, medications...')
                    ->helperText(' Important medical information and allergies')
                    ->columnSpanFull(),

                TextInput::make('transport_route')
                    ->label('Transport Route')
                    ->maxLength(255)
                    ->placeholder('e.g., Route A, Bus #12')
                    ->helperText(' School transport route (if applicable)')
                    ->columnSpan(1),

                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->rows(2)
                    ->placeholder('Any additional information about the student...')
                    ->helperText(' Additional notes or special instructions')
                    ->columnSpan(2),

                Repeater::make('emergency_contacts')
                    ->label('Emergency Contacts')
                    ->schema([
                        TextInput::make('name')
                            ->label('Contact Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Full name'),

                        TextInput::make('relationship')
                            ->label('Relationship')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g., Uncle, Aunt, Family Friend'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+1 (555) 123-4567'),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('contact@example.com'),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->placeholder('Full address'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->reorderable()
                    ->helperText(' Emergency contacts for urgent situations'),

                Placeholder::make('created_at')
                    ->label('Record Created')
                    ->content(fn($record): string => $record?->created_at?->format('M j, Y g:i A') ?? 'Not yet created')
                    ->columnSpan(1),

                Placeholder::make('updated_at')
                    ->label('Last Updated')
                    ->content(fn($record): string => $record?->updated_at?->format('M j, Y g:i A') ?? 'Not yet updated')
                    ->columnSpan(1),

                Placeholder::make('stats')
                    ->label('Quick Stats')
                    ->content(function ($record): string {
                        if (!$record || !$record->user) return 'Stats will be available after creation';

                        $age = $record->user->date_of_birth ? \Carbon\Carbon::parse($record->user->date_of_birth)->age : 'N/A';
                        $attendance = $record->getAttendancePercentageAttribute() . '%';

                        return " Age: {$age} | Attendance: {$attendance}";
                    })
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
