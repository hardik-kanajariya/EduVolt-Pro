<?php

namespace App\Filament\Admin\Resources\BookIssues;

use App\Filament\Admin\Resources\BookIssues\Pages\CreateBookIssue;
use App\Filament\Admin\Resources\BookIssues\Pages\EditBookIssue;
use App\Filament\Admin\Resources\BookIssues\Pages\ListBookIssues;
use App\Filament\Admin\Resources\BookIssues\Schemas\BookIssueForm;
use App\Filament\Admin\Resources\BookIssues\Tables\BookIssuesTable;
use App\Models\BookIssue;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class BookIssueResource extends Resource
{
    protected static ?string $model = BookIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?string $modelLabel = 'Book Issue';

    protected static ?string $pluralModelLabel = 'Book Issues';

    public static function form(Form $form): Form
    {
        return BookIssueForm::configure($form);
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
