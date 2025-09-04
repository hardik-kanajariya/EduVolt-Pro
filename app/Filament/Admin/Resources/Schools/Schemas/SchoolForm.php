<?php

namespace App\Filament\Admin\Resources\Schools\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('School Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('code')
                    ->label('School Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->columnSpan(1),
                Textarea::make('address')
                    ->label('Full Address')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label('Contact Phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->columnSpan(1),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(1),
                TextInput::make('website')
                    ->label('Website URL')
                    ->url()
                    ->maxLength(255)
                    ->columnSpan(1),
                DatePicker::make('established_date')
                    ->label('Established Date')
                    ->required()
                    ->native(false)
                    ->columnSpan(1),
                FileUpload::make('logo')
                    ->label('School Logo')
                    ->image()
                    ->directory('school-logos')
                    ->visibility('public')
                    ->columnSpan(1),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required()
                    ->columnSpan(1),
                KeyValue::make('settings')
                    ->label('School Settings')
                    ->reorderable()
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
