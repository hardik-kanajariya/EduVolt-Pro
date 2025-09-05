<?php

namespace App\Filament\Admin\Resources\TimetableResource\Pages;

use App\Filament\Admin\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimetables extends ListRecords
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('timetable_builder')
                ->label('Timetable Builder')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->url('/admin/timetable-builder'),
        ];
    }
}
