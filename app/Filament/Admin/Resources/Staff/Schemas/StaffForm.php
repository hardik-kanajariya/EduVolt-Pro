<?php

namespace App\Filament\Admin\Resources\Staff\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StaffForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 TextInput::make('user_id')
 ->required()
 ->numeric(),
 TextInput::make('school_id')
 ->required()
 ->numeric(),
 TextInput::make('employee_id')
 ->required(),
 TextInput::make('position')
 ->required(),
 TextInput::make('department')
 ->required(),
 DatePicker::make('join_date')
 ->required(),
 TextInput::make('salary')
 ->numeric(),
 TextInput::make('employment_type')
 ->required()
 ->default('full_time'),
 TextInput::make('responsibilities'),
 Select::make('status')
 ->options(['active' => 'Active', 'inactive' => 'Inactive', 'terminated' => 'Terminated'])
 ->default('active')
 ->required(),
 ]);
 }
}
