<?php

namespace App\Filament\Admin\Resources\Teachers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user?->name ?? 'Teacher') . '&color=7F9CF5&background=EBF4FF')
                    ->size(40)
                    ->extraAttributes(['style' => 'border: 2px solid #e2e8f0;']),

                TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Employee ID copied!')
                    ->icon('heroicon-o-identification')
                    ->iconColor('primary'),

                TextColumn::make('user.name')
                    ->label('Teacher Name')
                    ->searchable(['users.name', 'users.email'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record): ?string => $record->user?->email)
                    ->icon('heroicon-o-user')
                    ->iconColor('success'),

                TextColumn::make('school.name')
                    ->label('School')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-building-office-2')
                    ->limit(30),

                TextColumn::make('designation')
                    ->label('Position')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'teacher' => '👨‍🏫 Teacher',
                        'senior_teacher' => '👩‍🏫 Senior Teacher',
                        'head_teacher' => '🎯 Head Teacher',
                        'coordinator' => '🤝 Coordinator',
                        'principal' => '🏛️ Principal',
                        'vice_principal' => '🎖️ Vice Principal',
                        'department_head' => '📚 Department Head',
                        default => ucfirst($state ?? 'Teacher'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'principal', 'vice_principal' => 'danger',
                        'head_teacher', 'department_head' => 'warning',
                        'senior_teacher', 'coordinator' => 'info',
                        default => 'success',
                    }),

                TextColumn::make('qualification')
                    ->label('Qualification')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->icon('heroicon-o-academic-cap')
                    ->iconColor('warning'),

                TextColumn::make('experience_years')
                    ->label('Experience')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' yrs')
                    ->badge()
                    ->color(function (?int $state): string {
                        if (!$state) return 'gray';
                        if ($state >= 10) return 'success';
                        if ($state >= 5) return 'info';
                        return 'warning';
                    })
                    ->icon('heroicon-o-star'),

                TextColumn::make('employment_type')
                    ->label('Employment')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'full_time' => '🕘 Full Time',
                        'part_time' => '🕐 Part Time',
                        'contract' => '📋 Contract',
                        'substitute' => '🔄 Substitute',
                        'visiting' => '👥 Visiting',
                        default => ucfirst($state ?? 'Full Time'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'info',
                        'contract' => 'warning',
                        'substitute' => 'gray',
                        'visiting' => 'purple',
                        default => 'gray',
                    }),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->placeholder('Not provided')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('join_date')
                    ->label('Joined')
                    ->date('M j, Y')
                    ->sortable()
                    ->description(fn($record): string => \Carbon\Carbon::parse($record->join_date)->diffForHumans())
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('info'),

                TextColumn::make('tenure')
                    ->label('Tenure')
                    ->state(function ($record): string {
                        if (!$record->join_date) return 'N/A';
                        $years = \Carbon\Carbon::parse($record->join_date)->diffInYears(now());
                        $months = \Carbon\Carbon::parse($record->join_date)->diffInMonths(now()) % 12;
                        return $years > 0 ? "{$years}y {$months}m" : "{$months}m";
                    })
                    ->alignCenter()
                    ->badge()
                    ->color('purple')
                    ->icon('heroicon-o-clock'),

                TextColumn::make('salary')
                    ->label('Salary')
                    ->numeric()
                    ->prefix('₹')
                    ->sortable()
                    ->placeholder('Not disclosed')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-currency-rupee'),

                TextColumn::make('specialization')
                    ->label('Subjects')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    })
                    ->placeholder('Not specified')
                    ->icon('heroicon-o-book-open')
                    ->iconColor('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('has_certifications')
                    ->label('Certified')
                    ->boolean()
                    ->state(fn($record): bool => is_array($record->certifications) && count($record->certifications) > 0)
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-academic-cap')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn($record): string => 
                        is_array($record->certifications) 
                            ? count($record->certifications) . ' certifications' 
                            : 'No certifications'
                    )
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                        'terminated' => '🚫 Terminated',
                        'resigned' => '📤 Resigned',
                        'retired' => '🎖️ Retired',
                        'on_leave' => '🏖️ On Leave',
                        default => ucfirst($state ?? 'Active'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'terminated' => 'danger',
                        'resigned' => 'warning',
                        'retired' => 'info',
                        'on_leave' => 'purple',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn($record): string => $record->created_at->diffForHumans()),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn($record): string => $record->updated_at->diffForHumans()),
            ])
            ->filters([
                TrashedFilter::make(),
                
                SelectFilter::make('status')
                    ->label('Employment Status')
                    ->options([
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                        'terminated' => '🚫 Terminated',
                        'resigned' => '📤 Resigned',
                        'retired' => '🎖️ Retired',
                        'on_leave' => '🏖️ On Leave',
                    ])
                    ->multiple()
                    ->default(['active']),

                SelectFilter::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => '🕘 Full Time',
                        'part_time' => '🕐 Part Time',
                        'contract' => '📋 Contract',
                        'substitute' => '🔄 Substitute',
                        'visiting' => '👥 Visiting Faculty',
                    ])
                    ->multiple(),

                SelectFilter::make('designation')
                    ->label('Designation')
                    ->options([
                        'teacher' => '👨‍🏫 Teacher',
                        'senior_teacher' => '👩‍🏫 Senior Teacher',
                        'head_teacher' => '🎯 Head Teacher',
                        'coordinator' => '🤝 Coordinator',
                        'principal' => '🏛️ Principal',
                        'vice_principal' => '🎖️ Vice Principal',
                        'department_head' => '📚 Department Head',
                    ])
                    ->multiple(),

                SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('experienced')
                    ->label('Experienced (5+ years)')
                    ->query(fn (Builder $query): Builder => $query->where('experience_years', '>=', 5))
                    ->toggle(),

                Filter::make('senior_staff')
                    ->label('Senior Staff (10+ years)')
                    ->query(fn (Builder $query): Builder => $query->where('experience_years', '>=', 10))
                    ->toggle(),

                Filter::make('recent_joiners')
                    ->label('Recent Joiners (6 months)')
                    ->query(fn (Builder $query): Builder => $query->where('join_date', '>=', now()->subMonths(6)))
                    ->toggle(),

                Filter::make('has_certifications')
                    ->label('Has Certifications')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('certifications'))
                    ->toggle(),

                Filter::make('join_date')
                    ->form([
                        DatePicker::make('joined_from')
                            ->label('Joined From')
                            ->native(false),
                        DatePicker::make('joined_until')
                            ->label('Joined Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['joined_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('join_date', '>=', $date),
                            )
                            ->when(
                                $data['joined_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('join_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('join_date', 'desc')
            ->striped()
            ->poll('30s')
            ->emptyStateHeading('No teachers found')
            ->emptyStateDescription('Start by creating your first teacher record.')
            ->emptyStateIcon('heroicon-o-users')
            ->recordUrl(fn($record) => null)
            ->searchPlaceholder('Search teachers by name, employee ID, qualification...')
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
