<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditStaff extends EditRecord
{
 protected static string $resource = StaffResource::class;

 protected function getHeaderActions(): array
 {
 return [
 DeleteAction::make(),
 ForceDeleteAction::make(),
 RestoreAction::make(),
 ];
 }
}
