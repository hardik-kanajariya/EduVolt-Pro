<?php

namespace App\Filament\Parent\Resources\StudentProgress;

use App\Filament\Parent\Resources\StudentProgress\Pages\CreateStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\EditStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\ListStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Pages\ViewStudentProgress;
use App\Filament\Parent\Resources\StudentProgress\Schemas\StudentProgressForm;
use App\Filament\Parent\Resources\StudentProgress\Schemas\StudentProgressInfolist;
use App\Filament\Parent\Resources\StudentProgress\Tables\StudentProgressTable;
use App\Models\StudentProgress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentProgressResource extends Resource
{
    protected static ?string $model = StudentProgress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Student Progres';

    public static function form(Schema $schema): Schema
    {
        return StudentProgressForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentProgressInfolist::configure($schema);
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
