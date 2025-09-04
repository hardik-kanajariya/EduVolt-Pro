<?php

namespace App\Filament\Admin\Resources\Staff;

use App\Filament\Admin\Resources\Staff\Pages\CreateStaff;
use App\Filament\Admin\Resources\Staff\Pages\EditStaff;
use App\Filament\Admin\Resources\Staff\Pages\ListStaff;
use App\Filament\Admin\Resources\Staff\Pages\ViewStaff;
use App\Filament\Admin\Resources\Staff\Schemas\StaffForm;
use App\Filament\Admin\Resources\Staff\Tables\StaffTable;
use App\Models\Staff;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | UnitEnum | null $navigationGroup = 'Academic Structure';

    protected static ?string $recordTitleAttribute = 'employee_id';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
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
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'view' => ViewStaff::route('/{record}'),
            'edit' => EditStaff::route('/{record}/edit'),
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

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'school']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee_id', 'position', 'department', 'user.name', 'school.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Employee' => $record->user?->name,
            'Position' => $record->position,
            'Department' => $record->department,
            'School' => $record->school?->name,
            'Status' => ucfirst($record->status),
        ];
    }
}
