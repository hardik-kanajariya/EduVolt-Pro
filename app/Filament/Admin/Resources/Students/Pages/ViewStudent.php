<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Filament\Admin\Resources\Students\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('student_profile_header')
                    ->label('')
                    ->content(function ($record): HtmlString {
                        $avatar = $record->avatar
                            ? '<img src="' . e(asset('storage/' . $record->avatar)) . '" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-blue-200">'
                            : '<div class="w-24 h-24 rounded-full mx-auto mb-4 bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold">' .
                            e(substr($record->user?->name ?? 'S', 0, 1)) . '</div>';

                        return new HtmlString('<div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">' .
                            $avatar .
                            '<h2 class="text-2xl font-bold text-gray-800 mb-2">' . e($record->user?->name ?? 'Student') . '</h2>' .
                            '<p class="text-blue-600 font-medium">' . e($record->admission_number) . '</p>' .
                            '<div class="mt-4 flex justify-center">' .
                            '<span class="px-3 py-1 rounded-full text-sm font-medium ' .
                            ($record->status === 'active' ? 'bg-green-100 text-green-800' : ($record->status === 'inactive' ? 'bg-red-100 text-red-800' : ($record->status === 'transferred' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'))) . '">' .
                            e(match ($record->status) {
                                'active' => ' Active',
                                'inactive' => ' Inactive',
                                'transferred' => ' Transferred',
                                'graduated' => ' Graduated',
                                'suspended' => ' Suspended',
                                default => ucfirst($record->status)
                            }) . '</span></div></div>');
                    })
                    ->columnSpanFull(),

                Placeholder::make('academic_info_header')
                    ->label('Academic Information')
                    ->content('')
                    ->columnSpanFull(),

                TextInput::make('school.name')
                    ->label('School')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('schoolClass.name')
                    ->label('Class')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('roll_number')
                    ->label('Roll Number')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                DatePicker::make('admission_date')
                    ->label('Admission Date')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                Placeholder::make('family_info_header')
                    ->label('Family Information')
                    ->content('')
                    ->columnSpanFull(),

                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('parent_phone')
                    ->label('Parent Phone')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('parent_email')
                    ->label('Parent Email')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                Textarea::make('address')
                    ->label('Home Address')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(2)
                    ->columnSpanFull(),

                Placeholder::make('personal_info_header')
                    ->label('Personal Details')
                    ->content('')
                    ->columnSpanFull(),

                DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                TextInput::make('gender')
                    ->label('Gender')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'male' => ' Male',
                        'female' => ' Female',
                        'other' => ' Other',
                        default => 'Not specified',
                    })
                    ->columnSpan(1),

                Placeholder::make('additional_info_header')
                    ->label('Additional Information')
                    ->content('')
                    ->columnSpanFull(),

                Textarea::make('medical_info')
                    ->label('Medical Information')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(3)
                    ->columnSpan(1),

                TextInput::make('transport_route')
                    ->label('Transport Route')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                Textarea::make('notes')
                    ->label('Additional Notes')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(2)
                    ->columnSpan(2),

                Placeholder::make('emergency_contacts_header')
                    ->label('Emergency Contacts')
                    ->content('')
                    ->columnSpanFull(),

                Repeater::make('emergency_contacts')
                    ->label('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Contact Name')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('relationship')
                            ->label('Relationship')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->disabled()
                            ->columnSpan(1),

                        Textarea::make('address')
                            ->label('Address')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->disabled()
                    ->dehydrated(false)
                    ->columns(2)
                    ->columnSpanFull(),

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

                Placeholder::make('student_stats')
                    ->label('Quick Statistics')
                    ->content(function ($record): HtmlString {
                        $age = $record->date_of_birth ? \Carbon\Carbon::parse($record->date_of_birth)->age : 'N/A';
                        $emergencyContacts = is_array($record->emergency_contacts) ? count($record->emergency_contacts) : 0;
                        $hasMedical = !empty($record->medical_info) ? 'Yes' : 'No';

                        return new HtmlString('<div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">' .
                            '<div class="text-center"><div class="text-2xl font-bold text-blue-600">' . e($age) . '</div><div class="text-sm text-gray-600">Age (years)</div></div>' .
                            '<div class="text-center"><div class="text-2xl font-bold text-green-600">' . e($emergencyContacts) . '</div><div class="text-sm text-gray-600">Emergency Contacts</div></div>' .
                            '<div class="text-center"><div class="text-2xl font-bold text-red-600">' . e($hasMedical) . '</div><div class="text-sm text-gray-600">Medical Info</div></div>' .
                            '</div>');
                    })
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
