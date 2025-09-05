<?php

namespace App\Filament\Student\Resources\Assignments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Form;

class AssignmentInfolist
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextEntry::make('teacher.id'),
                TextEntry::make('class_id')
                    ->numeric(),
                TextEntry::make('subject.name'),
                TextEntry::make('title'),
                TextEntry::make('due_date')
                    ->date(),
                TextEntry::make('due_time')
                    ->time(),
                TextEntry::make('max_marks')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
