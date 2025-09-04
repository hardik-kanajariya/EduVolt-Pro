<?php

namespace App\Filament\Admin\Resources\LibraryBooks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\BookCategory;

class LibraryBooksTable
{
 public static function configure(Table $table): Table
 {
 return $table
 ->columns([
 ImageColumn::make('cover_image')
 ->height(60)
 ->width(40)
 ->defaultImageUrl('https://via.placeholder.com/40x60/e5e7eb/9ca3af?text='),

 TextColumn::make('title')
 ->searchable()
 ->sortable()
 ->weight('medium')
 ->limit(30),

 TextColumn::make('author')
 ->searchable()
 ->sortable()
 ->limit(20),

 TextColumn::make('category.name')
 ->badge()
 ->color(fn($record) => match ($record->category?->code) {
 'FICTION' => 'primary',
 'NON_FICTION' => 'success',
 'ACADEMIC' => 'danger',
 'REFERENCE' => 'warning',
 'CHILDREN' => 'info',
 default => 'gray'
 }),

 TextColumn::make('isbn')
 ->label('ISBN')
 ->searchable()
 ->copyable()
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('barcode')
 ->searchable()
 ->copyable()
 ->badge()
 ->color('gray'),

 TextColumn::make('total_copies')
 ->label('Total')
 ->alignCenter()
 ->badge()
 ->color('info'),

 TextColumn::make('available_copies')
 ->label('Available')
 ->alignCenter()
 ->badge()
 ->color(fn($record) => $record->available_copies > 0 ? 'success' : 'danger'),

 TextColumn::make('issued_copies')
 ->label('Issued')
 ->alignCenter()
 ->badge()
 ->color('warning'),

 TextColumn::make('condition')
 ->badge()
 ->color(fn($state) => match ($state) {
 'excellent' => 'success',
 'good' => 'info',
 'fair' => 'warning',
 'poor' => 'danger',
 default => 'gray'
 }),

 IconColumn::make('is_active')
 ->label('Status')
 ->boolean()
 ->trueIcon('heroicon-o-check-circle')
 ->falseIcon('heroicon-o-x-circle')
 ->trueColor('success')
 ->falseColor('danger'),

 TextColumn::make('price')
 ->money('INR')
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('location')
 ->label('Shelf')
 ->toggleable(isToggledHiddenByDefault: true),

 TextColumn::make('created_at')
 ->dateTime()
 ->sortable()
 ->toggleable(isToggledHiddenByDefault: true),
 ])
 ->filters([
 SelectFilter::make('category_id')
 ->label('Category')
 ->options(BookCategory::pluck('name', 'id'))
 ->searchable(),

 SelectFilter::make('condition')
 ->options([
 'excellent' => 'Excellent',
 'good' => 'Good',
 'fair' => 'Fair',
 'poor' => 'Poor',
 ]),

 TernaryFilter::make('is_active')
 ->label('Status')
 ->boolean()
 ->trueLabel('Active only')
 ->falseLabel('Inactive only')
 ->native(false),

 TernaryFilter::make('available')
 ->label('Availability')
 ->queries(
 true: fn($query) => $query->where('available_copies', '>', 0),
 false: fn($query) => $query->where('available_copies', '=', 0),
 )
 ->trueLabel('Available only')
 ->falseLabel('Out of stock only')
 ->native(false),

 TrashedFilter::make(),
 ])
 ->recordActions([
 ViewAction::make(),
 EditAction::make(),
 ])
 ->toolbarActions([
 BulkActionGroup::make([
 DeleteBulkAction::make(),
 ForceDeleteBulkAction::make(),
 RestoreBulkAction::make(),
 ]),
 ])
 ->defaultSort('created_at', 'desc')
 ->searchable()
 ->striped();
 }
}
