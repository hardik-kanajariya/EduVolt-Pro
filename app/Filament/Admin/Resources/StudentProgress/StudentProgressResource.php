<?php

namespace App\Filament\Admin\Resources\StudentProgress;

use App\Filament\Admin\Resources\StudentProgress\Pages\ListStudentProgress;
use App\Filament\Admin\Resources\StudentProgress\Tables\StudentProgressTable;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class StudentProgress extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-trending-up';

    protected static ?string $navigationGroup = 'Student & Attendance';

    public static function form(Form $form): Form
    {
        return StudentProgressForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return StudentProgressTable::configure($table);
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
            'index' => ListStudentProgress::route('/'),
            'create' => CreateStudentProgress::route('/create'),
            'edit' => EditStudentProgress::route('/{record}/edit'),
        ];
    }
}
