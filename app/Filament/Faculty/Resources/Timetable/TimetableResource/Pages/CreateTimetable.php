<?php

namespace App\Filament\Faculty\Resources\Timetable\TimetableResource\Pages;

use App\Filament\Faculty\Resources\Timetable\TimetableResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTimetable extends CreateRecord
{
    protected static string $resource = TimetableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = Auth::user()->teacher->id;
        return $data;
    }
}
