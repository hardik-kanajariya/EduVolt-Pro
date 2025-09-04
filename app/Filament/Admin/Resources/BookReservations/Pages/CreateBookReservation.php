<?php

namespace App\Filament\Admin\Resources\BookReservations\Pages;

use App\Filament\Admin\Resources\BookReservations\BookReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookReservation extends CreateRecord
{
    protected static string $resource = BookReservationResource::class;
}
