<?php

namespace App\Filament\Admin\Resources\Teachers\Pages;

use App\Filament\Admin\Resources\Teachers\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class ViewTeacher extends ViewRecord
{
 protected static string $resource = TeacherResource::class;

 protected function getHeaderActions(): array
 {
 return [
 Actions\EditAction::make()
 ->icon('heroicon-o-pencil-square')
 ->color('warning'),
 Actions\DeleteAction::make()
 ->icon('heroicon-o-trash')
 ->color('danger'),
 ];
 }

 public function form(Schema $schema): Schema
 {
 return $schema
 ->schema([
 Placeholder::make('teacher_profile_header')
 ->label('')
 ->content(function ($record): string {
 $photo = $record->profile_photo
 ? '<img src="' . asset('storage/' . $record->profile_photo) . '" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-blue-200">'
 : '<div class="w-32 h-32 rounded-full mx-auto mb-4 bg-blue-100 flex items-center justify-center text-blue-600 text-4xl font-bold">' .
 substr($record->user?->name ?? 'T', 0, 1) . '</div>';

 $designation = match ($record->designation) {
 'teacher' => ' Teacher',
 'senior_teacher' => ' Senior Teacher',
 'head_teacher' => ' Head Teacher',
 'coordinator' => ' Coordinator',
 'principal' => ' Principal',
 'vice_principal' => ' Vice Principal',
 'department_head' => ' Department Head',
 default => ' ' . ucfirst($record->designation ?? 'Teacher')
 };

 return '<div class="text-center p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">' .
 $photo .
 '<h2 class="text-2xl font-bold text-gray-800 mb-2">' . ($record->user?->name ?? 'Teacher') . '</h2>' .
 '<p class="text-green-600 font-medium mb-2">' . $record->employee_id . '</p>' .
 '<p class="text-gray-600 mb-4">' . $designation . '</p>' .
 '<div class="flex justify-center space-x-4">' .
 '<span class="px-3 py-1 rounded-full text-sm font-medium ' .
 ($record->status === 'active' ? 'bg-green-100 text-green-800' : ($record->status === 'inactive' ? 'bg-red-100 text-red-800' :
 'bg-yellow-100 text-yellow-800')) . '">' .
 match ($record->status) {
 'active' => ' Active',
 'inactive' => ' Inactive',
 'terminated' => ' Terminated',
 'resigned' => ' Resigned',
 'retired' => ' Retired',
 'on_leave' => ' On Leave',
 default => ucfirst($record->status)
 } . '</span>' .
 '<span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">' .
 $record->experience_years . ' Years Experience</span>' .
 '</div></div>';
 })
 ->columnSpanFull(),

 Placeholder::make('professional_info_header')
 ->label('Professional Information')
 ->content('')
 ->columnSpanFull(),

 TextInput::make('employee_id')
 ->label('Employee ID')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('school.name')
 ->label('School')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('qualification')
 ->label('Highest Qualification')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('designation')
 ->label('Designation')
 ->disabled()
 ->dehydrated(false)
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'teacher' => ' Teacher',
 'senior_teacher' => ' Senior Teacher',
 'head_teacher' => ' Head Teacher',
 'coordinator' => ' Coordinator',
 'principal' => ' Principal',
 'vice_principal' => ' Vice Principal',
 'department_head' => ' Department Head',
 default => ucfirst($state ?? 'Teacher'),
 })
 ->columnSpan(1),

 TextInput::make('experience_years')
 ->label('Teaching Experience')
 ->disabled()
 ->dehydrated(false)
 ->suffix(' years')
 ->columnSpan(1),

 DatePicker::make('join_date')
 ->label('Joining Date')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('employment_type')
 ->label('Employment Type')
 ->disabled()
 ->dehydrated(false)
 ->formatStateUsing(fn(?string $state): string => match ($state) {
 'full_time' => ' Full Time',
 'part_time' => ' Part Time',
 'contract' => ' Contract',
 'substitute' => ' Substitute',
 'visiting' => ' Visiting Faculty',
 default => ucfirst($state ?? 'Full Time'),
 })
 ->columnSpan(1),

 TextInput::make('salary')
 ->label('Monthly Salary')
 ->disabled()
 ->dehydrated(false)
 ->prefix('')
 ->placeholder('Not disclosed')
 ->columnSpan(1),

 Placeholder::make('contact_info_header')
 ->label('Contact Information')
 ->content('')
 ->columnSpanFull(),

 TextInput::make('user.email')
 ->label('Email Address')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('phone_number')
 ->label('Phone Number')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 TextInput::make('emergency_contact')
 ->label('Emergency Contact')
 ->disabled()
 ->dehydrated(false)
 ->columnSpan(1),

 Textarea::make('address')
 ->label('Residential Address')
 ->disabled()
 ->dehydrated(false)
 ->rows(2)
 ->columnSpanFull(),

 Placeholder::make('academic_info_header')
 ->label('Academic Information')
 ->content('')
 ->columnSpanFull(),

 Textarea::make('specialization')
 ->label('Subject Specialization')
 ->disabled()
 ->dehydrated(false)
 ->rows(3)
 ->columnSpanFull(),

 Placeholder::make('certifications_header')
 ->label('Professional Certifications')
 ->content('')
 ->columnSpanFull(),

 Repeater::make('certifications')
 ->label('')
 ->schema([
 TextInput::make('name')
 ->label('Certification Name')
 ->disabled()
 ->columnSpan(1),

 TextInput::make('authority')
 ->label('Issuing Authority')
 ->disabled()
 ->columnSpan(1),

 DatePicker::make('issue_date')
 ->label('Issue Date')
 ->disabled()
 ->columnSpan(1),

 DatePicker::make('expiry_date')
 ->label('Expiry Date')
 ->disabled()
 ->columnSpan(1),

 TextInput::make('credential_id')
 ->label('Credential ID')
 ->disabled()
 ->columnSpanFull(),
 ])
 ->disabled()
 ->dehydrated(false)
 ->columns(2)
 ->columnSpanFull(),

 Placeholder::make('experience_header')
 ->label('Previous Experience')
 ->content('')
 ->columnSpanFull(),

 Repeater::make('previous_experience')
 ->label('')
 ->schema([
 TextInput::make('institution')
 ->label('Institution Name')
 ->disabled()
 ->columnSpan(1),

 TextInput::make('position')
 ->label('Position Held')
 ->disabled()
 ->columnSpan(1),

 DatePicker::make('start_date')
 ->label('Start Date')
 ->disabled()
 ->columnSpan(1),

 DatePicker::make('end_date')
 ->label('End Date')
 ->disabled()
 ->columnSpan(1),

 Textarea::make('responsibilities')
 ->label('Key Responsibilities')
 ->disabled()
 ->rows(2)
 ->columnSpanFull(),
 ])
 ->disabled()
 ->dehydrated(false)
 ->columns(2)
 ->columnSpanFull(),

 Placeholder::make('additional_notes_header')
 ->label('Additional Information')
 ->content('')
 ->columnSpanFull(),

 Textarea::make('notes')
 ->label('Notes')
 ->disabled()
 ->dehydrated(false)
 ->rows(3)
 ->columnSpan(2),

 Placeholder::make('record_info_header')
 ->label('Record Information')
 ->content('')
 ->columnSpanFull(),

 Placeholder::make('created_at')
 ->label('Record Created')
 ->content(fn($record): string => $record?->created_at?->format('F j, Y g:i A') . ' (' . $record?->created_at?->diffForHumans() . ')')
 ->columnSpan(1),

 Placeholder::make('updated_at')
 ->label('Last Updated')
 ->content(fn($record): string => $record?->updated_at?->format('F j, Y g:i A') . ' (' . $record?->updated_at?->diffForHumans() . ')')
 ->columnSpan(1),

 Placeholder::make('teacher_stats')
 ->label('Teaching Statistics')
 ->content(function ($record): string {
 $tenure = $record->join_date ? \Carbon\Carbon::parse($record->join_date)->diffInYears(now()) : 0;
 $certifications = is_array($record->certifications) ? count($record->certifications) : 0;
 $previousJobs = is_array($record->previous_experience) ? count($record->previous_experience) : 0;

 return '<div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">' .
 '<div class="text-center"><div class="text-2xl font-bold text-blue-600">' . $tenure . '</div><div class="text-sm text-gray-600">Years at School</div></div>' .
 '<div class="text-center"><div class="text-2xl font-bold text-green-600">' . $certifications . '</div><div class="text-sm text-gray-600">Certifications</div></div>' .
 '<div class="text-center"><div class="text-2xl font-bold text-purple-600">' . $previousJobs . '</div><div class="text-sm text-gray-600">Previous Positions</div></div>' .
 '</div>';
 })
 ->columnSpanFull(),
 ])
 ->columns(3);
 }
}
