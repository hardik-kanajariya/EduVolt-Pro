<?php

namespace App\Filament\Admin\Resources\BookIssues;

use App\Filament\Admin\Resources\BookIssues\Pages\CreateBookIssue;
use App\Filament\Admin\Resources\BookIssues\Pages\EditBookIssue;
use App\Filament\Admin\Resources\BookIssues\Pages\ListBookIssues;
use App\Filament\Admin\Resources\BookIssues\Schemas\BookIssueForm;
use App\Filament\Admin\Resources\BookIssues\Tables\BookIssuesTable;
use App\Models\BookIssue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookIssueResource extends Resource
{
    protected static ?string $model = BookIssue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $modelLabel = 'Book Issue';

    protected static ?string $pluralModelLabel = 'Book Issues';

    public static function form(Schema $schema): Schema
    {
        return BookIssueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookIssuesTable::configure($table);
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
            'index' => ListBookIssues::route('/'),
            'create' => CreateBookIssue::route('/create'),
            'edit' => EditBookIssue::route('/{record}/edit'),
        ];
    }
}
