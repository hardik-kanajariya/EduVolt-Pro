<?php

namespace App\Filament\Admin\Resources\Subjects\Pages;

use App\Filament\Admin\Resources\Subjects\SubjectResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class ViewSubject extends ViewRecord
{
    protected static string $resource = SubjectResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label('Subject Name')
                    ->weight(FontWeight::Bold)
                    ->size('lg'),

                TextEntry::make('code')
                    ->label('Subject Code')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                TextEntry::make('type')
                    ->label('Subject Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'core' => 'success',
                        'elective' => 'info',
                        'extra_curricular' => 'warning',
                        default => 'gray',
                    }),

                TextEntry::make('credits')
                    ->label('Credit Hours')
                    ->badge()
                    ->color('primary')
                    ->suffix(' credits'),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('school.name')
                    ->label('School Name')
                    ->weight(FontWeight::Bold),

                TextEntry::make('school.code')
                    ->label('School Code')
                    ->badge()
                    ->color('gray'),

                TextEntry::make('description')
                    ->label('Description')
                    ->html()
                    ->placeholder('No description provided')
                    ->columnSpanFull(),

                TextEntry::make('teachers_count')
                    ->label('Assigned Teachers')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn($record) => $record->teachers()->count()),

                TextEntry::make('classes_count')
                    ->label('Number of Classes')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn($record) => $record->classes()->count()),

                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A'),

                TextEntry::make('updated_at')
                    ->label('Last Modified')
                    ->dateTime('M j, Y g:i A'),
            ])
            ->columns(2);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assign_teacher')
                ->label('Assign Teacher')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->url(fn() => '#'), // Will implement later

            EditAction::make()
                ->icon('heroicon-o-pencil'),

            DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }
}
