<?php

namespace App\Filament\Admin\Resources\Schools;

use App\Filament\Admin\Resources\Schools\Pages\CreateSchool;
use App\Filament\Admin\Resources\Schools\Pages\EditSchool;
use App\Filament\Admin\Resources\Schools\Pages\ListSchools;
use App\Filament\Admin\Resources\Schools\Pages\ViewSchool;
use App\Filament\Admin\Resources\Schools\Schemas\SchoolForm;
use App\Filament\Admin\Resources\Schools\Tables\SchoolsTable;
use App\Filament\Admin\Resources\Schools\RelationManagers\StudentsRelationManager;
use App\Models\School;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Multi-School Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Schools';

    protected static ?string $modelLabel = 'School';

    protected static ?string $pluralModelLabel = 'Schools';

    protected static ?string $slug = 'schools';

    public static function form(Form $form): Form
    {
        return SchoolForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SchoolsTable::configure($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist; // Will be configured in ViewSchool page
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchools::route('/'),
            'create' => CreateSchool::route('/create'),
            'view' => ViewSchool::route('/{record}'),
            'edit' => EditSchool::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'email', 'phone'];
    }
}
