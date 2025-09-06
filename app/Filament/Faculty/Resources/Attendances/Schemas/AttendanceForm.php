<?php

namespace App\Filament\Faculty\Resources\Attendances\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

class AttendanceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema();
    }
}
