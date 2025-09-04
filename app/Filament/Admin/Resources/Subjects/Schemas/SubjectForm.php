<?php

namespace App\Filament\Admin\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('code'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required()
                    ->default('core'),
                TextInput::make('credits')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
