<?php

namespace App\Filament\Admin\Resources\BookReservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class BookReservationForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                DatePicker::make('reservation_date')
                    ->required(),
                DatePicker::make('expiry_date')
                    ->required(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'fulfilled' => 'Fulfilled',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('fulfilled_at'),
                TextInput::make('fulfilled_by')
                    ->numeric(),
            ]);
    }
}
