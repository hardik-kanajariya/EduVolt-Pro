<?php

namespace App\Filament\Admin\Resources\Schools\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')
                    ->label('Student Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('student_id')
                    ->label('Student ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('user.email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Select::make('class_id')
                    ->label('Class')
                    ->relationship('class', 'name')
                    ->required()
                    ->searchable(),

                DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'transferred' => 'Transferred',
                    ])
                    ->default('active')
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png')
                    ->size(40),

                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('student_id')
                    ->label('Student ID')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                TextColumn::make('class.name')
                    ->label('Class')
                    ->badge()
                    ->color('info'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('date_of_birth')
                    ->label('Age')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->age . ' years')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'graduated' => 'warning',
                        'transferred' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Enrolled')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('class', 'name'),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'transferred' => 'Transferred',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add New Student'),
                AssociateAction::make()
                    ->label('Associate Existing Student')
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No students enrolled')
            ->emptyStateDescription('Add students to this school to get started.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
