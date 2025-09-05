<?php

namespace App\Filament\Admin\Resources\SchoolClasses;

use App\Filament\Admin\Resources\SchoolClasses\Pages\CreateSchoolClass;
use App\Filament\Admin\Resources\SchoolClasses\Pages\EditSchoolClass;
use App\Filament\Admin\Resources\SchoolClasses\Pages\ListSchoolClasses;
use App\Filament\Admin\Resources\SchoolClasses\Pages\ViewSchoolClass;
use App\Filament\Admin\Resources\SchoolClasses\Schemas\SchoolClassForm;
use App\Filament\Admin\Resources\SchoolClasses\Tables\SchoolClassesTable;
use App\Models\SchoolClass;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academic Structure';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return SchoolClassForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SchoolClassesTable::configure($table);
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
            'index' => ListSchoolClasses::route('/'),
            'create' => CreateSchoolClass::route('/create'),
            'view' => ViewSchoolClass::route('/{record}'),
            'edit' => EditSchoolClass::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
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
