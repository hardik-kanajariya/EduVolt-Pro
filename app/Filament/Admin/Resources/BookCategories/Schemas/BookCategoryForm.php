<?php

namespace App\Filament\Admin\Resources\BookCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Str;

class BookCategoryForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Hidden::make('school_id')
                    ->default(1), // Default school ID for now

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->live()
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create') {
                            $set('code', Str::upper(Str::slug($state, '_')));
                        }
                    }),

                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->alphaDash()
                    ->helperText('Unique identifier for the category (auto-generated from name)'),

                ColorPicker::make('color')
                    ->label('Category Color')
                    ->default('#6B7280')
                    ->helperText('Color used for visual identification'),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive categories are hidden'),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Brief description of what types of books belong to this category...'),
            ])
            ->columns(3);
    }
}
