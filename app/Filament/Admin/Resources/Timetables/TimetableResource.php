<?php

namespace App\Filament\Admin\Resources\Timetables;

use App\Filament\Admin\Resources\Timetables\Pages\CreateTimetable;
use App\Filament\Admin\Resources\Timetables\Pages\EditTimetable;
use App\Filament\Admin\Resources\Timetables\Pages\ListTimetables;
use App\Filament\Admin\Resources\Timetables\Schemas\TimetableForm;
use App\Filament\Admin\Resources\Timetables\Tables\TimetablesTable;
use App\Models\Timetable;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Schedule Management';

    public static function form(Form $form): Form
    {
        return TimetableForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TimetablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimetables::route('/'),
            'create' => CreateTimetable::route('/create'),
            'edit' => EditTimetable::route('/{record}/edit'),
        ];
    }
}
