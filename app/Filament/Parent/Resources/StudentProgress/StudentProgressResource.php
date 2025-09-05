<?php

namespace App\Filament\Parent\Resources\StudentProgress;

use App\Filament\Parent\Resources\StudentProgress\Pages\CreateStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\EditStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\ListStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\ViewStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Schemas\StudentProgressForm;
use App\Filament\Parent\Resources\StudentProgress\Tables\StudentProgressTable;
use App\Models\StudentProgress;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentProgressResource extends Resource
{
    protected static ?string $model = StudentProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $recordTitleAttribute = 'Student Progres';

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
            'view' => ViewStudentProgress::route('/{record}'),
            'edit' => EditStudentProgress::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
