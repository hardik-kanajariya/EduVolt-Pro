<?php

namespace App\Filament\Parent\Resources\StudentProgress\Pages;

use App\Filament\Parent\Resources\StudentProgress\StudentProgressResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentProgress extends CreateRecord
{
    protected static string $resource = StudentProgressResource::class;
}
