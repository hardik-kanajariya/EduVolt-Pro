<?php

namespace App\Filament\Faculty\Resources\Students\Pages;

use App\Filament\Faculty\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
