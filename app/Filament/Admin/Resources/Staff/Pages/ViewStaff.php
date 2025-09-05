<?php

namespace App\Filament\Admin\Resources\Staff\Pages;

use App\Filament\Admin\Resources\Staff\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffResource::class;

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
        $record = $this->getRecord();

        // Calculate statistics
        $yearsOfService = $record->join_date ? $record->join_date->diffInYears(now()) : 0;
        $monthsOfService = $record->join_date ? $record->join_date->diffInMonths(now()) % 12 : 0;
        $responsibilities = is_array($record->responsibilities) ? $record->responsibilities : [];

        return $form
            ->schema([
                // Staff Profile Header
                Placeholder::make('staff_profile_header')
                    ->label('')
                    ->content(function () use ($record, $yearsOfService): HtmlString {
                        $avatar = $record->user?->avatar
                            ? '<img src="' . asset('storage/' . $record->user->avatar) . '" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">'
                            : '<div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">' .
                            strtoupper(substr($record->user?->name ?? 'S', 0, 1)) . '</div>';

                        $statusColor = match ($record->status) {
                            'active' => 'bg-green-100 text-green-800',
                            'inactive' => 'bg-red-100 text-red-800',
                            'terminated' => 'bg-red-100 text-red-800',
                            'on_leave' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800'
                        };

                        return new HtmlString('<div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">' .
                            $avatar .
                            '<h2 class="text-2xl font-bold text-gray-800 mb-2">' . e($record->user?->name ?? 'Staff Member') . '</h2>' .
                            '<p class="text-blue-600 font-medium mb-2">' . e($record->employee_id) . '</p>' .
                            '<p class="text-gray-600 mb-4">' . e($record->position ?? 'Position Not Set') . '</p>' .
                            '<div class="flex justify-center space-x-4 mb-4">' .
                            '<span class="px-3 py-1 rounded-full text-sm font-medium ' . $statusColor . '">' .
                            e(ucfirst($record->status)) . '</span>' .
                            '<span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">' .
                            e($yearsOfService) . ' Years Service</span>' .
                            '</div>' .
                            '<div class="grid grid-cols-2 gap-4 mt-4">' .
                            '<div class="text-center"><span class="block text-lg font-bold text-blue-600">' . e($record->department ?? 'N/A') . '</span><span class="text-sm text-gray-600">Department</span></div>' .
                            '<div class="text-center"><span class="block text-lg font-bold text-green-600">' . e($record->employment_type ?? 'N/A') . '</span><span class="text-sm text-gray-600">Employment Type</span></div>' .
                            '</div></div>');
                    })
                    ->columnSpanFull(),

                // Basic Information
                TextInput::make('employee_id')
                    ->label('Employee ID')
                    ->disabled()
                    ->prefixIcon('heroicon-m-identification'),

                Select::make('user_id')
                    ->label('User Account')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->prefixIcon('heroicon-m-user'),

                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->disabled()
                    ->prefixIcon('heroicon-m-building-office-2'),

                // Employment Details
                TextInput::make('position')
                    ->label('Position')
                    ->disabled()
                    ->prefixIcon('heroicon-m-briefcase'),

                TextInput::make('department')
                    ->label('Department')
                    ->disabled()
                    ->prefixIcon('heroicon-m-building-office'),

                Select::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                        'intern' => 'Intern',
                    ])
                    ->disabled()
                    ->prefixIcon('heroicon-m-clock'),

                // Employment Timeline
                DatePicker::make('join_date')
                    ->label('Join Date')
                    ->disabled()
                    ->prefixIcon('heroicon-m-calendar-days'),

                TextInput::make('salary')
                    ->label('Salary')
                    ->disabled()
                    ->prefix('$')
                    ->prefixIcon('heroicon-m-currency-dollar'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                        'on_leave' => 'On Leave',
                    ])
                    ->disabled()
                    ->prefixIcon('heroicon-m-flag'),

                // Service Information
                Placeholder::make('service_duration')
                    ->label('Service Duration')
                    ->content(function () use ($yearsOfService, $monthsOfService): HtmlString {
                        return new HtmlString('<div class="p-4 bg-blue-50 rounded-lg border border-blue-200">' .
                            '<div class="grid grid-cols-2 gap-4">' .
                            '<div class="text-center">' .
                            '<div class="text-xl font-bold text-blue-700">' . e($yearsOfService) . '</div>' .
                            '<div class="text-sm text-blue-600">Years</div>' .
                            '</div>' .
                            '<div class="text-center">' .
                            '<div class="text-xl font-bold text-blue-700">' . e($monthsOfService) . '</div>' .
                            '<div class="text-sm text-blue-600">Months</div>' .
                            '</div>' .
                            '</div>' .
                            '</div>');
                    }),

                // Responsibilities
                Textarea::make('responsibilities_display')
                    ->label('Responsibilities')
                    ->default(function () use ($responsibilities): string {
                        if (empty($responsibilities)) {
                            return 'No responsibilities defined';
                        }
                        return is_array($responsibilities)
                            ? implode("\n• ", array_merge([''], $responsibilities))
                            : $responsibilities;
                    })
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),

                // Contact Information
                Placeholder::make('contact_info')
                    ->label('Contact Information')
                    ->content(function () use ($record): HtmlString {
                        $user = $record->user;
                        if (!$user) {
                            return new HtmlString('<div class="p-4 bg-gray-50 rounded-lg text-gray-500">No user account linked</div>');
                        }

                        return new HtmlString('<div class="p-4 bg-green-50 rounded-lg border border-green-200">' .
                            '<div class="space-y-2">' .
                            '<div><strong>Email:</strong> ' . e($user->email ?? 'Not provided') . '</div>' .
                            '<div><strong>Phone:</strong> ' . e($user->phone ?? 'Not provided') . '</div>' .
                            '<div><strong>Address:</strong> ' . e($user->address ?? 'Not provided') . '</div>' .
                            '</div>' .
                            '</div>');
                    })
                    ->columnSpanFull(),

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
