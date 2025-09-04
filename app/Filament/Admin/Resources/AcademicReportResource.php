<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AcademicReportResource\Pages;
use App\Filament\Admin\Resources\AcademicReportResource\Schemas\AcademicReportForm;
use App\Filament\Admin\Resources\AcademicReportResource\Tables\AcademicReportsTable;
use App\Models\AcademicReport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AcademicReportResource extends Resource
{
    protected static ?string $model = AcademicReport::class;

    public static function form(Schema $schema): Schema
    {
        return AcademicReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicReports::route('/'),
            'create' => Pages\CreateAcademicReport::route('/create'),
            'view' => Pages\ViewAcademicReport::route('/{record}'),
            'edit' => Pages\EditAcademicReport::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }
}
