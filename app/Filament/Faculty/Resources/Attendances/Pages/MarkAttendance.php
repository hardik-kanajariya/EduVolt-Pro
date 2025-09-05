<?php

namespace App\Filament\Faculty\Resources\Attendances\Pages;

use App\Filament\Faculty\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\Page;

class MarkAttendance extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.faculty.resources.attendances.pages.mark-attendance';
}
