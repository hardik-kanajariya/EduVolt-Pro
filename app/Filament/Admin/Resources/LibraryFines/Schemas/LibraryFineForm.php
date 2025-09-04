<?php

namespace App\Filament\Admin\Resources\LibraryFines\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LibraryFineForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 Select::make('school_id')
 ->relationship('school', 'name')
 ->required(),
 Select::make('book_issue_id')
 ->relationship('bookIssue', 'id')
 ->required(),
 Select::make('student_id')
 ->relationship('student', 'id')
 ->required(),
 TextInput::make('amount')
 ->required()
 ->numeric(),
 Select::make('type')
 ->options(['overdue' => 'Overdue', 'damage' => 'Damage', 'lost' => 'Lost', 'other' => 'Other'])
 ->default('overdue')
 ->required(),
 Textarea::make('reason')
 ->columnSpanFull(),
 DatePicker::make('fine_date')
 ->required(),
 Select::make('status')
 ->options(['pending' => 'Pending', 'paid' => 'Paid', 'waived' => 'Waived'])
 ->default('pending')
 ->required(),
 TextInput::make('paid_amount')
 ->required()
 ->numeric()
 ->default(0.0),
 DatePicker::make('paid_date'),
 TextInput::make('collected_by')
 ->numeric(),
 Textarea::make('payment_notes')
 ->columnSpanFull(),
 ]);
 }
}
