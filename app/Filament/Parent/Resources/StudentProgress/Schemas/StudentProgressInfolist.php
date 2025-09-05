<?php

namespace App\Filament\Parent\Resources\StudentProgress\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Form;

class StudentProgressInfolist
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextEntry::make('student.id'),
                TextEntry::make('subject.name'),
                TextEntry::make('term'),
                TextEntry::make('academic_year'),
                TextEntry::make('attendance_percentage')
                    ->numeric(),
                TextEntry::make('assignment_average')
                    ->numeric(),
                TextEntry::make('exam_average')
                    ->numeric(),
                TextEntry::make('overall_grade')
                    ->numeric(),
                TextEntry::make('letter_grade'),
                TextEntry::make('conduct'),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
