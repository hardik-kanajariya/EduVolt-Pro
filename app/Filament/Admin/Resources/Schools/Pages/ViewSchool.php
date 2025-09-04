<?php

namespace App\Filament\Admin\Resources\Schools\Pages;

use App\Filament\Admin\Resources\Schools\SchoolResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;

class ViewSchool extends ViewRecord
{
    protected static string $resource = SchoolResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                ImageEntry::make('logo')
                    ->label('School Logo')
                    ->height(100)
                    ->defaultImageUrl('/images/default-school.png'),

                TextEntry::make('name')
                    ->label('School Name')
                    ->weight(FontWeight::Bold)
                    ->size('lg'),

                TextEntry::make('code')
                    ->label('School Code')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                TextEntry::make('type')
                    ->label('School Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'info',
                        'charter' => 'warning',
                        'magnet' => 'primary',
                        'international' => 'secondary',
                        'religious' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'public' => '🏛️ Public School',
                        'private' => '🏫 Private School',
                        'charter' => '📜 Charter School',
                        'magnet' => '🧲 Magnet School',
                        'international' => '🌍 International School',
                        'religious' => '⛪ Religious School',
                        default => $state,
                    }),

                TextEntry::make('address')
                    ->label('Address')
                    ->columnSpanFull(),

                TextEntry::make('phone')
                    ->label('Phone')
                    ->copyable(),

                TextEntry::make('email')
                    ->label('Email')
                    ->copyable(),

                TextEntry::make('website')
                    ->label('Website')
                    ->url(fn($record) => $record->website)
                    ->openUrlInNewTab(),

                TextEntry::make('established_date')
                    ->label('Established Date')
                    ->date('M j, Y'),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                        'pending' => '⏳ Pending',
                        'suspended' => '⚠️ Suspended',
                        default => $state,
                    }),

                TextEntry::make('students_count')
                    ->label('Total Students')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn($record) => $record->students()->count()),

                TextEntry::make('teachers_count')
                    ->label('Total Teachers')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn($record) => $record->teachers()->count()),

                TextEntry::make('classes_count')
                    ->label('Total Classes')
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
            ->columns(3);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visit_website')
                ->label('Visit Website')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->url(fn($record) => $record->website)
                ->openUrlInNewTab()
                ->visible(fn($record) => filled($record->website)),

            EditAction::make()
                ->icon('heroicon-o-pencil'),

            DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }
}
