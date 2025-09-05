<?php

namespace App\Filament\Student\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Form;

class AttendanceInfolist
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextEntry::make('student.id'),
                TextEntry::make('class_id')
                    ->numeric(),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('marked_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
