<?php

namespace App\Filament\Admin\Resources\Schools\Pages;

use App\Filament\Admin\Resources\Schools\SchoolResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewSchool extends ViewRecord
{
    protected static string $resource = SchoolResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('School Overview')
                    ->description('Basic information about the school')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('logo')
                                    ->label('School Logo')
                                    ->height(100)
                                    ->defaultImageUrl('/images/default-school.png')
                                    ->columnSpan(1),

                                TextEntry::make('name')
                                    ->label('School Name')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),

                                TextEntry::make('code')
                                    ->label('School Code')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->columnSpan(1),

                                TextEntry::make('type')
                                    ->label('School Type')
                                    ->badge()
                                    ->columnSpan(1),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    })
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Contact Information')
                    ->description('How to reach the school')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->columnSpanFull(),

                                TextEntry::make('phone')
                                    ->label('Phone')
                                    ->copyable()
                                    ->icon('heroicon-o-phone')
                                    ->columnSpan(1),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope')
                                    ->columnSpan(1),

                                TextEntry::make('website')
                                    ->label('Website')
                                    ->url(fn($record) => $record->website)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-globe-alt')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Statistics')
                    ->description('School statistics and metrics')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('students_count')
                                    ->label('Total Students')
                                    ->badge()
                                    ->color('success')
                                    ->getStateUsing(fn($record) => $record->students()->count())
                                    ->columnSpan(1),

                                TextEntry::make('teachers_count')
                                    ->label('Total Teachers')
                                    ->badge()
                                    ->color('info')
                                    ->getStateUsing(fn($record) => $record->teachers()->count())
                                    ->columnSpan(1),

                                TextEntry::make('classes_count')
                                    ->label('Total Classes')
                                    ->badge()
                                    ->color('warning')
                                    ->getStateUsing(fn($record) => $record->classes()->count())
                                    ->columnSpan(1),

                                TextEntry::make('principal.name')
                                    ->label('Principal')
                                    ->placeholder('Not assigned'),

                                TextEntry::make('established_date')
                                    ->label('Established Date')
                                    ->date('M j, Y'),
                            ]),
                    ]),

                Section::make('System Information')
                    ->description('Record tracking information')
                    ->icon('heroicon-o-document-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('M j, Y g:i A')
                                    ->columnSpan(1),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
