<?php

namespace App\Filament\Admin\Widgets;

use App\Models\EmailLog;
use App\Models\FeeReminder;
use App\Models\Assignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class CommunicationWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Communications';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        return $table
            ->query(
                EmailLog::query()
                    ->whereHas('user', function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId);
                    })
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recipient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'fee_reminder' => 'warning',
                        'assignment' => 'info',
                        'attendance' => 'danger',
                        'general' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fee_reminder' => 'Fee Reminder',
                        'assignment' => 'Assignment',
                        'attendance' => 'Attendance',
                        'general' => 'General',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),
            ])
            ->defaultSort('sent_at', 'desc')
            ->paginated([5, 10]);
    }
}
