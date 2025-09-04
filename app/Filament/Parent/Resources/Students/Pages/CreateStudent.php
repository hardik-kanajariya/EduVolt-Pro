<?php

namespace App\Filament\Parent\Resources\Students\Pages;

use App\Filament\Parent\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
