<?php

namespace App\Filament\Admin\Resources\LibraryBooks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LibraryBookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('author')
                    ->required(),
                TextInput::make('isbn'),
                TextInput::make('publisher'),
                TextInput::make('publication_year'),
                TextInput::make('edition'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('language')
                    ->required()
                    ->default('English'),
                TextInput::make('pages')
                    ->numeric(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('cover_image')
                    ->image(),
                TextInput::make('barcode'),
                TextInput::make('total_copies')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('available_copies')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('issued_copies')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('reserved_copies')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('condition')
                    ->options(['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'])
                    ->default('excellent')
                    ->required(),
                TextInput::make('location'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('additional_info'),
            ]);
    }
}
