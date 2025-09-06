<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\LibraryResource\Pages;
use App\Models\LibraryBook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class LibraryResource extends Resource
{
    protected static ?string $model = LibraryBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Library Books';

    protected static ?string $slug = 'library-books';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole(['librarian', 'school_admin', 'principal'])) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('author')
                    ->required()
                    ->maxLength(255),

                TextInput::make('isbn')
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                TextInput::make('publisher')
                    ->maxLength(255),

                DatePicker::make('publication_date'),

                Select::make('category_id')
                    ->relationship('category', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    })
                    ->searchable()
                    ->preload(),

                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('available_quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('price')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('rack_number')
                    ->maxLength(50),

                FileUpload::make('cover_image')
                    ->image()
                    ->directory('book-covers')
                    ->maxSize(2048),

                Textarea::make('description')
                    ->rows(4),

                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                        'damaged' => 'Damaged',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->square()
                    ->size(50),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('isbn')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('available_quantity')
                    ->sortable(),

                TextColumn::make('rack_number')
                    ->searchable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'unavailable',
                        'danger' => 'damaged',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                        'damaged' => 'Damaged',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLibrary::route('/'),
            'create' => Pages\CreateLibrary::route('/create'),
            'view' => Pages\ViewLibrary::route('/{record}'),
            'edit' => Pages\EditLibrary::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['librarian', 'school_admin', 'principal']);
    }
}
