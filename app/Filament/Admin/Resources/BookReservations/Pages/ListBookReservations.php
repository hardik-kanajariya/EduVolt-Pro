<?php

namespace App\Filament\Admin\Resources\BookReservations\Pages;

use App\Filament\Admin\Resources\BookReservations\BookReservationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookReservations extends ListRecords
{
 protected static string $resource = BookReservationResource::class;

 protected function getHeaderActions(): array
 {
 return [
 CreateAction::make(),
 ];
 }
}
