<?php

namespace App\Filament\Admin\Resources\AcademicYears\Pages;

use App\Filament\Admin\Resources\AcademicYears\AcademicYearResource;
use App\Models\AcademicYear;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;

class ViewAcademicYear extends ViewRecord
{
 protected static string $resource = AcademicYearResource::class;

 protected function getHeaderActions(): array
 {
 return [
 Actions\EditAction::make()
 ->icon('heroicon-m-pencil-square'),
 Actions\DeleteAction::make()
 ->icon('heroicon-m-trash'),
 ];
 }

 public function getTitle(): string
 {
 $record = $this->getRecord();
 return "Academic Year: {$record->name}";
 }

 public function form(Schema $schema): Schema
 {
 $record = $this->getRecord();

 // Calculate statistics
 $totalClasses = $record->classes()->count();
 $activeClasses = $record->classes()->where('status', 'active')->count();
 $totalStudents = $record->classes()->withCount('students')->get()->sum('students_count');
 $duration = $record->start_date && $record->end_date
 ? $record->start_date->diffInDays($record->end_date) + 1 . ' days'
 : 'Not specified';
 $progress = $record->start_date && $record->end_date
 ? min(100, max(0, now()->diffInDays($record->start_date) / $record->start_date->diffInDays($record->end_date) * 100))
 : 0;

 return $schema
 ->schema([
 // Academic Year Profile Header
 Placeholder::make('academic_year_profile')
 ->label('')
 ->content(function () use ($record, $totalClasses, $activeClasses, $totalStudents, $duration, $progress): string {
 $statusColor = match ($record->status) {
 'active' => 'bg-green-100 text-green-800',
 'inactive' => 'bg-red-100 text-red-800',
 default => 'bg-gray-100 text-gray-800'
 };

 $statusIcon = match ($record->status) {
 'active' => '',
 'inactive' => '',
 default => ''
 };

 $currentBadge = $record->is_current
 ? '<span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"> Current Academic Year</span>'
 : '';

 return '<div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">' .
 '<h2 class="text-2xl font-bold text-gray-800 mb-2"> ' . $record->name . '</h2>' .
 '<p class="text-blue-600 font-medium mb-2"> ' . ($record->school?->name ?? 'No School') . '</p>' .
 '<p class="text-gray-600 mb-4">' . $duration . '</p>' .
 '<div class="flex justify-center space-x-4 mb-4">' .
 '<span class="px-3 py-1 rounded-full text-sm font-medium ' . $statusColor . '">' .
 $statusIcon . ' ' . ucfirst($record->status) . '</span>' .
 $currentBadge .
 '</div>' .
 '<div class="grid grid-cols-3 gap-4 mt-4">' .
 '<div class="text-center"><span class="block text-lg font-bold text-blue-600">' . $totalClasses . '</span><span class="text-sm text-gray-600">Total Classes</span></div>' .
 '<div class="text-center"><span class="block text-lg font-bold text-green-600">' . $activeClasses . '</span><span class="text-sm text-gray-600">Active Classes</span></div>' .
 '<div class="text-center"><span class="block text-lg font-bold text-purple-600">' . $totalStudents . '</span><span class="text-sm text-gray-600">Total Students</span></div>' .
 '</div></div>';
 })
 ->columnSpanFull(),

 // Basic Information
 TextInput::make('name')
 ->label('Academic Year Name')
 ->disabled()
 ->prefixIcon('heroicon-m-calendar-days'),

 Select::make('school_id')
 ->label('School')
 ->relationship('school', 'name')
 ->disabled()
 ->prefixIcon('heroicon-m-building-office-2'),

 Select::make('status')
 ->label('Status')
 ->options([
 'active' => 'Active',
 'inactive' => 'Inactive'
 ])
 ->disabled()
 ->prefixIcon('heroicon-m-flag'),

 // Duration Information
 DatePicker::make('start_date')
 ->label('Start Date')
 ->disabled()
 ->prefixIcon('heroicon-m-play'),

 DatePicker::make('end_date')
 ->label('End Date')
 ->disabled()
 ->prefixIcon('heroicon-m-stop'),

 Toggle::make('is_current')
 ->label('Current Academic Year')
 ->disabled()
 ->inline(false),

 // Statistics Section
 Placeholder::make('duration_stats')
 ->label('Duration Statistics')
 ->content(function () use ($duration, $progress): string {
 $progressColor = $progress > 75 ? 'bg-green-500' : ($progress > 50 ? 'bg-yellow-500' : 'bg-blue-500');

 return '<div class="space-y-4">' .
 '<div class="flex justify-between items-center">' .
 '<span class="text-sm font-medium text-gray-600">Total Duration:</span>' .
 '<span class="text-sm font-bold text-gray-800">' . $duration . '</span>' .
 '</div>' .
 '<div class="space-y-2">' .
 '<div class="flex justify-between items-center">' .
 '<span class="text-sm font-medium text-gray-600">Progress:</span>' .
 '<span class="text-sm font-bold text-gray-800">' . round($progress, 1) . '%</span>' .
 '</div>' .
 '<div class="w-full bg-gray-200 rounded-full h-2">' .
 '<div class="' . $progressColor . ' h-2 rounded-full" style="width: ' . min(100, $progress) . '%"></div>' .
 '</div>' .
 '</div>' .
 '</div>';
 }),

 Placeholder::make('class_stats')
 ->label('Class Statistics')
 ->content(function () use ($totalClasses, $activeClasses, $totalStudents): string {
 $utilization = $totalClasses > 0 ? round(($activeClasses / $totalClasses) * 100, 1) : 0;

 return '<div class="grid grid-cols-2 gap-4">' .
 '<div class="bg-blue-50 p-3 rounded-lg">' .
 '<span class="block text-lg font-bold text-blue-600">' . $totalClasses . '</span>' .
 '<span class="text-sm text-blue-800">Total Classes</span>' .
 '</div>' .
 '<div class="bg-green-50 p-3 rounded-lg">' .
 '<span class="block text-lg font-bold text-green-600">' . $activeClasses . '</span>' .
 '<span class="text-sm text-green-800">Active Classes</span>' .
 '</div>' .
 '<div class="bg-purple-50 p-3 rounded-lg">' .
 '<span class="block text-lg font-bold text-purple-600">' . $totalStudents . '</span>' .
 '<span class="text-sm text-purple-800">Total Students</span>' .
 '</div>' .
 '<div class="bg-orange-50 p-3 rounded-lg">' .
 '<span class="block text-lg font-bold text-orange-600">' . $utilization . '%</span>' .
 '<span class="text-sm text-orange-800">Utilization Rate</span>' .
 '</div>' .
 '</div>';
 }),

 // Record Tracking
 Placeholder::make('created_at')
 ->label('Created At')
 ->content(fn() => $record->created_at ? $record->created_at->format('F j, Y \a\t g:i A') : 'Not available'),

 Placeholder::make('updated_at')
 ->label('Last Updated')
 ->content(fn() => $record->updated_at ? $record->updated_at->format('F j, Y \a\t g:i A') : 'Not available'),
 ])
 ->columns(3);
 }
}
