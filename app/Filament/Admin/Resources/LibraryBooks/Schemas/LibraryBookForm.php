<?php

namespace App\Filament\Admin\Resources\LibraryBooks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class LibraryBookForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 Select::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->searchable()
 ->preload()
 ->required()
 ->columnSpan(2),

 Select::make('category_id')
 ->label('Book Category')
 ->relationship('category', 'name')
 ->searchable()
 ->preload()
 ->required()
 ->columnSpan(1),

 TextInput::make('title')
 ->label('Book Title')
 ->required()
 ->maxLength(255)
 ->columnSpan(2),

 TextInput::make('author')
 ->label('Author(s)')
 ->required()
 ->maxLength(255)
 ->columnSpan(1),

 TextInput::make('isbn')
 ->label('ISBN')
 ->maxLength(20)
 ->unique(ignoreRecord: true)
 ->columnSpan(1),

 TextInput::make('publisher')
 ->label('Publisher')
 ->maxLength(255)
 ->columnSpan(1),

 TextInput::make('publication_year')
 ->label('Publication Year')
 ->numeric()
 ->minValue(1900)
 ->maxValue(date('Y'))
 ->columnSpan(1),

 TextInput::make('edition')
 ->label('Edition')
 ->maxLength(50)
 ->columnSpan(1),

 TextInput::make('language')
 ->label('Language')
 ->default('English')
 ->maxLength(50)
 ->columnSpan(1),

 TextInput::make('pages')
 ->label('Number of Pages')
 ->numeric()
 ->minValue(1)
 ->columnSpan(1),

 TextInput::make('price')
 ->label('Price')
 ->numeric()
 ->prefix('')
 ->columnSpan(1),

 TextInput::make('total_copies')
 ->label('Total Copies')
 ->required()
 ->numeric()
 ->default(1)
 ->minValue(1)
 ->columnSpan(1),

 TextInput::make('available_copies')
 ->label('Available Copies')
 ->required()
 ->numeric()
 ->default(1)
 ->minValue(0)
 ->columnSpan(1),

 Select::make('condition')
 ->label('Book Condition')
 ->options([
 'excellent' => 'Excellent',
 'good' => 'Good',
 'fair' => 'Fair',
 'poor' => 'Poor',
 ])
 ->default('excellent')
 ->required()
 ->columnSpan(1),

 TextInput::make('location')
 ->label('Shelf Location')
 ->maxLength(100)
 ->placeholder('e.g., A-1-001, Section B')
 ->columnSpan(1),

 TextInput::make('barcode')
 ->label('Barcode')
 ->unique(ignoreRecord: true)
 ->maxLength(50)
 ->columnSpan(1),

 FileUpload::make('cover_image')
 ->label('Book Cover Image')
 ->image()
 ->directory('book-covers')
 ->visibility('public')
 ->columnSpan(1),

 Textarea::make('description')
 ->label('Book Description')
 ->rows(3)
 ->columnSpanFull(),

 KeyValue::make('additional_info')
 ->label('Additional Information')
 ->reorderable()
 ->columnSpanFull(),
 ])
 ->columns(3);
 }
}
