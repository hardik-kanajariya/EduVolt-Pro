<?php

namespace App\Filament\Student\Resources\Grades\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GradeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.id'),
                TextEntry::make('subject.name'),
                TextEntry::make('class_id')
                    ->numeric(),
                TextEntry::make('exam_type'),
                TextEntry::make('exam_name'),
                TextEntry::make('obtained_marks')
                    ->numeric(),
                TextEntry::make('total_marks')
                    ->numeric(),
                TextEntry::make('percentage')
                    ->numeric(),
                TextEntry::make('grade'),
                TextEntry::make('exam_date')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
