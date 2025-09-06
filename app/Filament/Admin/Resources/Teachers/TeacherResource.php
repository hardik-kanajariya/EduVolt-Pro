<?php

namespace App\Filament\Admin\Resources\Teachers;

use App\Filament\Admin\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Admin\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Admin\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Admin\Resources\Teachers\Tables\TeachersTable;
use App\Models\Teacher;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Faculty Management';

    protected static ?string $recordTitleAttribute = 'employee_id';

    public static function form(Form $form): Form
    {
        return TeacherForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\Teachers\RelationManagers\SubjectsRelationManager::class,
            \App\Filament\Admin\Resources\Teachers\RelationManagers\AssignedClassesRelationManager::class,
            \App\Filament\Admin\Resources\Teachers\RelationManagers\AssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
