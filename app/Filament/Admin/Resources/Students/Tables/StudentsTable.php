<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 ImageColumn::make('avatar')
 ->label('')
 ->circular()
 ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=Student&color=7F9CF5&background=EBF4FF')
 ->size(40)
 ->extraAttributes(['style' => 'border: 2px solid #e2e8f0;']),

 TextColumn::make('admission_number')
 ->label('ID')
 ->searchable()
 ->sortable()
 ->weight('medium')
 ->copyable()
 ->copyMessage('Admission number copied!')
 ->icon('heroicon-o-identification')
 ->iconColor('primary'),

 TextColumn::make('user.name')
 ->label('Student Name')
 ->searchable(['users.name', 'users.email'])
 ->sortable()
 ->weight('bold')
 ->description(fn($record): ?string => $record->user?->email)
 ->icon('heroicon-o-user')
 ->iconColor('success'),

 TextColumn::make('schoolClass.name')
 ->label('Class')
 ->sortable()
 ->badge()
 ->color('info')
 ->description(fn($record): ?string => $record->school?->name)
 ->icon('heroicon-o-academic-cap'),

 TextColumn::make('roll_number')
 ->label('Roll')
 ->searchable()
 ->alignCenter()
 ->badge()
 ->color('gray')
 ->placeholder('Not Set'),

 TextColumn::make('parent_info')
 ->label('Parent Details')
 ->state(function ($record): string {
 return $record->parent_name ?? 'No parent info';
 })
 ->description(fn($record): ?string => $record->parent_phone)
 ->searchable(['parent_name', 'parent_phone', 'parent_email'])
 ->icon('heroicon-o-users')
 ->iconColor('warning'),

 TextColumn::make('age')
 ->label('Age')
 ->state(function ($record): string {
 if (!$record->date_of_birth) return 'N/A';
 return \Carbon\Carbon::parse($record->date_of_birth)->age . ' yrs';
 })
 ->alignCenter()
 ->sortable()
 ->icon('heroicon-o-cake'),

 TextColumn::make('admission_date')
 ->label('Admitted')
 ->date('M j, Y')
 ->sortable()
 ->description(fn($record): string => \Carbon\Carbon::parse($record->admission_date)->diffForHumans())
 ->icon('heroicon-o-calendar-days')
 ->iconColor('info'),

 TextColumn::make('status')
 ->label('Status')
 ->badge()
 ->formatStateUsing(fn(string $state): string => match ($state) {
 'active' => ' Active',
 'inactive' => ' Inactive',
 'transferred' => ' Transferred',
 'graduated' => ' Graduated',
 'suspended' => ' Suspended',
 default => ucfirst($state),
 })
 ->color(fn(string $state): string => match ($state) {
 'active' => 'success',
 'inactive' => 'danger',
 'transferred' => 'warning',
 'graduated' => 'info',
 'suspended' => 'danger',
 default => 'gray',
 })
 ->sortable(),

 TextColumn::make('transport_route')
 ->label('Transport')
 ->badge()
 ->color('purple')
 ->placeholder('No Transport')
 ->toggleable(isToggledHiddenByDefault: true)
 ->icon('heroicon-o-truck'),

 IconColumn::make('has_medical_info')
 ->label('Medical')
 ->boolean()
 ->state(fn($record): bool => !empty($record->medical_info))
 ->trueIcon('heroicon-o-heart')
 ->falseIcon('heroicon-o-heart')
 ->trueColor('danger')
 ->falseColor('gray')
 ->tooltip(fn($record): string => $record->medical_info ? 'Has medical information' : 'No medical information')
 ->alignCenter(),

 TextColumn::make('emergency_contacts_count')
 ->label('Contacts')
 ->state(fn($record): string => is_array($record->emergency_contacts) ? count($record->emergency_contacts) : '0')
 ->alignCenter()
 ->badge()
 ->color(fn($state): string => $state > 0 ? 'success' : 'warning')
 ->tooltip('Emergency contacts count')
 ->toggleable(isToggledHiddenByDefault: true),

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
 ->label('Status')
 ->options([
 'active' => ' Active',
 'inactive' => ' Inactive',
 'transferred' => ' Transferred',
 'graduated' => ' Graduated',
 'suspended' => ' Suspended',
 ])
 ->multiple()
 ->default(['active']),

 SelectFilter::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->searchable()
 ->preload(),

 SelectFilter::make('class_id')
 ->label('Class')
 ->relationship('schoolClass', 'name')
 ->searchable()
 ->preload(),

 Filter::make('has_transport')
 ->label('Has Transport')
 ->query(fn(Builder $query): Builder => $query->whereNotNull('transport_route'))
 ->toggle(),

 Filter::make('has_medical_info')
 ->label('Has Medical Info')
 ->query(fn(Builder $query): Builder => $query->whereNotNull('medical_info'))
 ->toggle(),

 Filter::make('recent_admissions')
 ->label('Recent Admissions (30 days)')
 ->query(fn(Builder $query): Builder => $query->where('admission_date', '>=', now()->subDays(30)))
 ->toggle(),

 Filter::make('admission_date')
 ->form([
 DatePicker::make('admitted_from')
 ->label('Admitted From')
 ->native(false),
 DatePicker::make('admitted_until')
 ->label('Admitted Until')
 ->native(false),
 ])
 ->query(function (Builder $query, array $data): Builder {
 return $query
 ->when(
 $data['admitted_from'],
 fn(Builder $query, $date): Builder => $query->whereDate('admission_date', '>=', $date),
 )
 ->when(
 $data['admitted_until'],
 fn(Builder $query, $date): Builder => $query->whereDate('admission_date', '<=', $date),
 );
 }),
 ])
 ->actions([
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
 ->defaultSort('admission_date', 'desc')
 ->striped()
 ->poll('30s')
 ->emptyStateHeading('No students found')
 ->emptyStateDescription('Start by creating your first student record.')
 ->emptyStateIcon('heroicon-o-users')
 ->recordUrl(fn($record) => null)
 ->searchPlaceholder('Search students by name, admission number, parent details...')
 ->paginationPageOptions([10, 25, 50, 100])
 ->defaultPaginationPageOption(25);
 }
}
