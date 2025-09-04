<?php

namespace App\Filament\Admin\Resources\Subjects;

use App\Filament\Admin\Resources\Subjects\Pages\CreateSubject;
use App\Filament\Admin\Resources\Subjects\Pages\EditSubject;
use App\Filament\Admin\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Admin\Resources\Subjects\Pages\ViewSubject;
use App\Filament\Admin\Resources\Subjects\Schemas\SubjectForm;
use App\Filament\Admin\Resources\Subjects\Tables\SubjectsTable;
use App\Filament\Admin\Resources\Subjects\RelationManagers\TeachersRelationManager;
use App\Filament\Admin\Resources\Subjects\RelationManagers\ClassesRelationManager;
use App\Models\Subject;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    // Navigation will be configured in service provider

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Subjects';

    protected static ?string $modelLabel = 'Subject';

    protected static ?string $pluralModelLabel = 'Subjects';

    protected static ?string $slug = 'subjects';

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema; // Will be configured in ViewSubject page
    }

    public static function getRelations(): array
    {
        return [
            TeachersRelationManager::class,
            ClassesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'view' => ViewSubject::route('/{record}'),
            'edit' => EditSubject::route('/{record}/edit'),
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
