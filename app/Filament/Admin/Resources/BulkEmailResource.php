<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BulkEmailResource\Pages;
use App\Models\BulkEmail;
use App\Models\EmailTemplate;
use App\Services\EmailService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BulkEmailResource extends Resource
{
    protected static ?string $model = BulkEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Bulk Emails';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Campaign name for internal reference'),

                        Forms\Components\Select::make('email_template_id')
                            ->label('Email Template')
                            ->options(EmailTemplate::active()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $template = EmailTemplate::find($state);
                                if ($template) {
                                    $set('subject', $template->subject);
                                    $set('content', $template->content);
                                }
                            }),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Email Content')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

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
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Recipients')
                    ->schema([
                        Forms\Components\Repeater::make('recipient_criteria_form')
                            ->label('Recipient Criteria')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'role' => 'By Role',
                                        'class' => 'By Class',
                                        'email_list' => 'Email List',
                                        'all_students' => 'All Students',
                                        'all_parents' => 'All Parents',
                                        'all_faculty' => 'All Faculty',
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('roles')
                                    ->multiple()
                                    ->options([
                                        'student' => 'Students',
                                        'parent' => 'Parents',
                                        'faculty' => 'Faculty',
                                        'principal' => 'Principal',
                                        'school_admin' => 'School Admin',
                                        'super_admin' => 'Super Admin',
                                    ])
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'role'),

                                Forms\Components\TextInput::make('class_name')
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'class'),

                                Forms\Components\Textarea::make('email_list')
                                    ->placeholder('Enter email addresses separated by commas or new lines')
                                    ->visible(fn(Forms\Get $get) => $get('type') === 'email_list'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Criteria')
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Scheduling & Settings')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Schedule For')
                            ->helperText('Leave empty to send immediately after creation'),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Internal notes about this campaign'),
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

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'success',
                        'low' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('recipient_count')
                    ->label('Recipients')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Sent')
                    ->sortable(),

                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sender')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('scheduled_at')),

                Tables\Filters\Filter::make('recent')
                    ->label('Last 7 Days')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn(BulkEmail $record): bool => $record->status === 'draft'),
                    Tables\Actions\Action::make('send_now')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->visible(fn(BulkEmail $record): bool => in_array($record->status, ['draft', 'scheduled']))
                        ->action(function (BulkEmail $record) {
                            $emailService = app(EmailService::class);
                            $record->update(['scheduled_at' => null]);
                            $emailService->processBulkEmail($record);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Send Campaign Now')
                        ->modalDescription('Are you sure you want to send this campaign immediately?'),
                    Tables\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function (BulkEmail $record) {
                            $newCampaign = $record->replicate();
                            $newCampaign->name = $record->name . ' (Copy)';
                            $newCampaign->status = 'draft';
                            $newCampaign->sent_count = 0;
                            $newCampaign->failed_count = 0;
                            $newCampaign->started_at = null;
                            $newCampaign->completed_at = null;
                            $newCampaign->save();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(BulkEmail $record): bool => $record->status === 'draft'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->where('status', 'draft')->each->delete();
                        }),
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
            'index' => Pages\ListBulkEmails::route('/'),
            'create' => Pages\CreateBulkEmail::route('/create'),
            'view' => Pages\ViewBulkEmail::route('/{record}'),
            'edit' => Pages\EditBulkEmail::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $draftCount = static::getModel()::where('status', 'draft')->count();
        $scheduledCount = static::getModel()::where('status', 'scheduled')->count();
        $total = $draftCount + $scheduledCount;

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $urgentCount = static::getModel()::where('priority', 'urgent')
            ->whereIn('status', ['draft', 'scheduled'])
            ->count();
        return $urgentCount > 0 ? 'danger' : 'primary';
    }
}
