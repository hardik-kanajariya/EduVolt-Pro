<?php

namespace App\Filament\Admin\Resources\Schools\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('School Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Central High School')
                    ->helperText('The official name of the educational institution')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($get, $set, ?string $state) {
                        if (filled($state) && empty($get('code'))) {
                            $set('code', strtoupper(Str::slug($state, '')));
                        }
                    })
                    ->columnSpan(2),

                TextInput::make('code')
                    ->label('School Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->placeholder('e.g., CHS001')
                    ->helperText('🔤 Auto-generated from school name or enter custom code')
                    ->live()
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))
                    ->columnSpan(1),

                Textarea::make('address')
                    ->label('Full Address')
                    ->required()
                    ->rows(3)
                    ->placeholder('Enter complete address including street, city, state, postal code')
                    ->helperText('📍 Complete physical address of the school')
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('Primary Phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->placeholder('+1 (555) 123-4567')
                    ->helperText('📞 Main contact number')
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('admin@school.edu')
                    ->helperText('📧 Primary email for communications')
                    ->columnSpan(1),

                TextInput::make('website')
                    ->label('Website URL')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://www.school.edu')
                    ->helperText('🌐 Official school website')
                    ->columnSpan(1),

                DatePicker::make('established_date')
                    ->label('Established Date')
                    ->format('Y-m-d')
                    ->displayFormat('M j, Y')
                    ->helperText('� Date when school was founded')
                    ->native(false)
                    ->columnSpan(1),

                Select::make('type')
                    ->label('School Type')
                    ->required()
                    ->options([
                        'public' => '🏛️ Public School',
                        'private' => '🏫 Private School',
                        'charter' => '📜 Charter School',
                        'magnet' => '🧲 Magnet School',
                        'international' => '🌍 International School',
                        'religious' => '⛪ Religious School',
                    ])
                    ->default('public')
                    ->helperText('Institutional classification')
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                        'pending' => '⏳ Pending',
                        'suspended' => '⚠️ Suspended',
                    ])
                    ->default('active')
                    ->helperText('Current operational status')
                    ->columnSpan(1),

                FileUpload::make('logo')
                    ->label('School Logo')
                    ->image()
                    ->imageEditor()
                    ->directory('school-logos')
                    ->visibility('public')
                    ->helperText('🖼️ Upload school logo (recommended: square format, max 2MB)')
                    ->columnSpan(1),

                KeyValue::make('settings')
                    ->label('Custom Settings')
                    ->helperText('⚙️ Additional configuration options (key-value pairs)')
                    ->reorderable()
                    ->columnSpan(2),

                Placeholder::make('created_at')
                    ->label('Created At')
                    ->content(fn($record): string => $record?->created_at?->format('M j, Y g:i A') ?? 'Not yet created')
                    ->columnSpan(1),

                Placeholder::make('updated_at')
                    ->label('Last Updated')
                    ->content(fn($record): string => $record?->updated_at?->format('M j, Y g:i A') ?? 'Not yet updated')
                    ->columnSpan(1),

                Placeholder::make('stats')
                    ->label('Quick Stats')
                    ->content(function ($record): string {
                        if (!$record) return 'Stats will be available after creation';

                        $students = $record->students()->count();
                        $teachers = $record->teachers()->count();
                        $classes = $record->classes()->count();

                        return "👥 {$students} Students | 👨‍🏫 {$teachers} Teachers | 🏛️ {$classes} Classes";
                    })
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
