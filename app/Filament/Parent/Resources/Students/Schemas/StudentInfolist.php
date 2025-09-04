<?php

namespace App\Filament\Parent\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('school_id')
                    ->numeric(),
                TextEntry::make('class_id')
                    ->numeric(),
                TextEntry::make('admission_number'),
                TextEntry::make('roll_number'),
                TextEntry::make('admission_date')
                    ->date(),
                TextEntry::make('parent_name'),
                TextEntry::make('parent_phone'),
                TextEntry::make('parent_email'),
                TextEntry::make('transport_route'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
