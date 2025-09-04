<?php

namespace App\Filament\Admin\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->label('Teacher User Account')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Search and select teacher user account...')
                    ->helperText('👤 Link this teacher record to an existing user account')
                    ->columnSpan(2),

                FileUpload::make('profile_photo')
                    ->label('Profile Photo')
                    ->image()
                    ->imageEditor()
                    ->directory('teacher-profiles')
                    ->visibility('public')
                    ->helperText('📸 Upload professional profile photo')
                    ->columnSpan(1),

                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select assigned school...')
                    ->helperText('🏫 School where teacher is employed')
                    ->live()
                    ->columnSpan(2),

                TextInput::make('employee_id')
                    ->label('Employee ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g., EMP2025001, TCH001')
                    ->helperText('🆔 Unique employee identifier')
                    ->live()
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->columnSpan(1),

                TextInput::make('qualification')
                    ->label('Highest Qualification')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., M.Sc Mathematics, B.Ed, PhD Physics')
                    ->helperText('🎓 Highest educational qualification')
                    ->columnSpan(1),

                TextInput::make('experience_years')
                    ->label('Teaching Experience')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(50)
                    ->suffix('years')
                    ->placeholder('5')
                    ->helperText('📚 Years of teaching experience')
                    ->columnSpan(1),

                DatePicker::make('join_date')
                    ->label('Joining Date')
                    ->required()
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('M j, Y')
                    ->helperText('📅 Date of joining this institution')
                    ->default(now())
                    ->maxDate(now())
                    ->columnSpan(1),

                TextInput::make('salary')
                    ->label('Monthly Salary')
                    ->numeric()
                    ->prefix('₹')
                    ->placeholder('50000')
                    ->helperText('💰 Monthly gross salary (optional)')
                    ->columnSpan(1),

                Select::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => '🕘 Full Time',
                        'part_time' => '🕐 Part Time',
                        'contract' => '📋 Contract',
                        'substitute' => '🔄 Substitute',
                        'visiting' => '👥 Visiting Faculty',
                    ])
                    ->default('full_time')
                    ->required()
                    ->helperText('Work arrangement type')
                    ->columnSpan(1),

                Select::make('designation')
                    ->label('Designation')
                    ->options([
                        'teacher' => '👨‍🏫 Teacher',
                        'senior_teacher' => '👩‍🏫 Senior Teacher',
                        'head_teacher' => '🎯 Head Teacher',
                        'coordinator' => '🤝 Coordinator',
                        'principal' => '🏛️ Principal',
                        'vice_principal' => '🎖️ Vice Principal',
                        'department_head' => '📚 Department Head',
                    ])
                    ->default('teacher')
                    ->required()
                    ->helperText('Official designation/position')
                    ->columnSpan(1),

                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('+91 98765 43210')
                    ->helperText('📞 Primary contact number')
                    ->columnSpan(1),

                TextInput::make('emergency_contact')
                    ->label('Emergency Contact')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('+91 98765 43210')
                    ->helperText('🆘 Emergency contact number')
                    ->columnSpan(1),

                Textarea::make('address')
                    ->label('Residential Address')
                    ->rows(2)
                    ->placeholder('Enter complete residential address...')
                    ->helperText('🏠 Current residential address')
                    ->columnSpanFull(),

                Textarea::make('specialization')
                    ->label('Subject Specialization')
                    ->rows(3)
                    ->placeholder('Mathematics, Physics, Chemistry, Science Lab Management, etc.')
                    ->helperText('📖 Subject areas and teaching specializations')
                    ->columnSpanFull(),

                Repeater::make('certifications')
                    ->label('Professional Certifications')
                    ->schema([
                        TextInput::make('name')
                            ->label('Certification Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., TEFL Certificate'),

                        TextInput::make('authority')
                            ->label('Issuing Authority')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., British Council, NCERT'),

                        DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('M j, Y'),

                        DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('M j, Y')
                            ->placeholder('Leave blank if no expiry'),

                        TextInput::make('credential_id')
                            ->label('Credential ID')
                            ->maxLength(100)
                            ->placeholder('Certificate/License number'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable()
                    ->helperText('🏆 Professional certifications and licenses'),

                Repeater::make('previous_experience')
                    ->label('Previous Teaching Experience')
                    ->schema([
                        TextInput::make('institution')
                            ->label('Institution Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., ABC High School'),

                        TextInput::make('position')
                            ->label('Position Held')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mathematics Teacher'),

                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('M j, Y'),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->format('Y-m-d')
                            ->displayFormat('M j, Y'),

                        Textarea::make('responsibilities')
                            ->label('Key Responsibilities')
                            ->rows(2)
                            ->placeholder('Brief description of responsibilities...'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable()
                    ->helperText('💼 Previous work experience in education'),

                Select::make('status')
                    ->label('Employment Status')
                    ->options([
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                        'terminated' => '🚫 Terminated',
                        'resigned' => '📤 Resigned',
                        'retired' => '🎖️ Retired',
                        'on_leave' => '🏖️ On Leave',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Current employment status')
                    ->columnSpan(1),

                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->rows(3)
                    ->placeholder('Any additional information about the teacher...')
                    ->helperText('📝 Special notes or instructions')
                    ->columnSpan(2),

                Placeholder::make('created_at')
                    ->label('Record Created')
                    ->content(fn ($record): string => $record?->created_at?->format('M j, Y g:i A') ?? 'Not yet created')
                    ->columnSpan(1),

                Placeholder::make('updated_at')
                    ->label('Last Updated')
                    ->content(fn ($record): string => $record?->updated_at?->format('M j, Y g:i A') ?? 'Not yet updated')
                    ->columnSpan(1),

                Placeholder::make('teacher_stats')
                    ->label('Quick Statistics')
                    ->content(function ($record): string {
                        if (!$record) return 'Stats will be available after creation';
                        
                        $experience = $record->experience_years ?? 0;
                        $tenure = $record->join_date ? \Carbon\Carbon::parse($record->join_date)->diffInYears(now()) : 0;
                        $subjects = $record->subjects?->count() ?? 0;
                        
                        return "📚 Experience: {$experience} years | 🏫 Tenure: {$tenure} years | 📖 Subjects: {$subjects}";
                    })
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
