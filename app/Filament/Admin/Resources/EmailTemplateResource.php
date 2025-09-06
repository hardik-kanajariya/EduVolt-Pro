<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(string $context, $state, Forms\Set $set) =>
                                $context === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('You can use variables like {{student_name}}, {{school_name}}'),

                        Forms\Components\Select::make('category')
                            ->required()
                            ->options([
                                'academic' => 'Academic',
                                'attendance' => 'Attendance',
                                'fees' => 'Fees',
                                'events' => 'Events',
                                'examinations' => 'Examinations',
                                'library' => 'Library',
                                'announcements' => 'Announcements',
                                'emergency' => 'Emergency',
                            ]),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'system' => 'System Template',
                                'custom' => 'Custom Template',
                                'bulk' => 'Bulk Email Template',
                            ])
                            ->default('custom'),
                    ])->columns(2),

                Forms\Components\Section::make('Template Content')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->helperText('Available variables: {{student_name}}, {{parent_name}}, {{school_name}}, {{amount}}, {{due_date}}, {{attendance_percentage}}, {{exam_name}}'),

                        Forms\Components\Textarea::make('variables')
                            ->rows(3)
                            ->helperText('JSON array of available variables for this template')
                            ->placeholder('["student_name", "school_name", "amount"]'),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),

                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Description of when and how to use this template'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'academic' => 'info',
                        'attendance' => 'warning',
                        'fees' => 'danger',
                        'events' => 'success',
                        'examinations' => 'primary',
                        'library' => 'gray',
                        'announcements' => 'info',
                        'emergency' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'system' => 'primary',
                        'custom' => 'success',
                        'bulk' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable()
                    ->label('Used'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'academic' => 'Academic',
                        'attendance' => 'Attendance',
                        'fees' => 'Fees',
                        'events' => 'Events',
                        'examinations' => 'Examinations',
                        'library' => 'Library',
                        'announcements' => 'Announcements',
                        'emergency' => 'Emergency',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'system' => 'System Template',
                        'custom' => 'Custom Template',
                        'bulk' => 'Bulk Email Template',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('preview')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Template Preview')
                        ->modalContent(
                            fn(EmailTemplate $record): \Illuminate\Contracts\View\View =>
                            view('filament.admin.email-template-preview', ['template' => $record])
                        )
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Tables\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function (EmailTemplate $record) {
                            $newTemplate = $record->replicate();
                            $newTemplate->name = $record->name . ' (Copy)';
                            $newTemplate->slug = $record->slug . '-copy-' . time();
                            $newTemplate->save();
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'view' => Pages\ViewEmailTemplate::route('/{record}'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }
}
