<?php

namespace App\Filament\Admin\Resources\BookIssues\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookIssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                TextInput::make('issued_by')
                    ->required()
                    ->numeric(),
                TextInput::make('returned_by')
                    ->numeric(),
                DatePicker::make('issue_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                DatePicker::make('return_date'),
                Select::make('status')
                    ->options(['issued' => 'Issued', 'returned' => 'Returned', 'overdue' => 'Overdue', 'lost' => 'Lost'])
                    ->default('issued')
                    ->required(),
                Select::make('condition_at_issue')
                    ->options(['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'])
                    ->default('excellent')
                    ->required(),
                Select::make('condition_at_return')
                    ->options(['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor']),
                Textarea::make('issue_notes')
                    ->columnSpanFull(),
                Textarea::make('return_notes')
                    ->columnSpanFull(),
                TextInput::make('renewal_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('last_renewal_date'),
            ]);
    }
}
