<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Assignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentActivitiesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Assignments';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        return $table
            ->query(
                Assignment::query()
                    ->whereHas('schoolClass', function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId);
                    })
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('schoolClass.name')
                    ->label('Class'),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'overdue' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
