<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Email Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Email Details')
                    ->schema([
                        Forms\Components\TextInput::make('message_id')
                            ->disabled()
                            ->label('Message ID'),

                        Forms\Components\TextInput::make('recipient_email')
                            ->email()
                            ->disabled(),

                        Forms\Components\TextInput::make('subject')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('content')
                            ->disabled()
                            ->rows(10)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'sent' => 'Sent',
                                'delivered' => 'Delivered',
                                'failed' => 'Failed',
                                'bounced' => 'Bounced',
                                'opened' => 'Opened',
                                'clicked' => 'Clicked',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('retry_count')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Textarea::make('error_message')
                            ->disabled()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('sent_at')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('opened_at')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('recipient_email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

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
                        'pending' => 'warning',
                        'sent' => 'info',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        'bounced' => 'danger',
                        'opened' => 'success',
                        'clicked' => 'primary',
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

                Tables\Columns\TextColumn::make('emailTemplate.name')
                    ->label('Template')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sender')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('retry_count')
                    ->label('Retries')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'bounced' => 'Bounced',
                        'opened' => 'Opened',
                        'clicked' => 'Clicked',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\Filter::make('has_template')
                    ->label('Has Template')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_template_id')),

                Tables\Filters\Filter::make('failed_emails')
                    ->label('Failed Emails')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'failed')),

                Tables\Filters\Filter::make('recent')
                    ->label('Last 24 Hours')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDay())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('retry')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn(EmailLog $record): bool => $record->status === 'failed')
                        ->action(function (EmailLog $record) {
                            $emailService = app(\App\Services\EmailService::class);
                            $emailService->processSingleEmail($record);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Retry Email')
                        ->modalDescription('Are you sure you want to retry sending this email?'),
                    Tables\Actions\Action::make('view_content')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Email Content')
                        ->modalContent(
                            fn(EmailLog $record): \Illuminate\Contracts\View\View =>
                            view('filament.admin.email-content-preview', ['emailLog' => $record])
                        )
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('retry_failed')
                        ->label('Retry Failed')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function ($records) {
                            $emailService = app(\App\Services\EmailService::class);
                            foreach ($records->where('status', 'failed') as $record) {
                                $emailService->processSingleEmail($record);
                            }
                        })
                        ->requiresConfirmation(),
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
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::failed()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $failedCount = static::getModel()::failed()->count();
        return $failedCount > 0 ? 'danger' : null;
    }
}
