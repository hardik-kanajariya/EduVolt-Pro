<?php

namespace App\Filament\Admin\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SchoolClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                TextInput::make('academic_year_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('section')
                    ->required()
                    ->default('A'),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(30),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
