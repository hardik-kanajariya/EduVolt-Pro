<?php

namespace App\Filament\Admin\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class AcademicYearForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                // Basic Information
                Select::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-building-office-2')
                    ->helperText('Select the school for this academic year')
                    ->columnSpan(2),

                TextInput::make('name')
                    ->label('Academic Year Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., 2024-2025')
                    ->prefixIcon('heroicon-m-academic-cap')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, ?string $state) {
                        if ($state && !str_contains($state, '-')) {
                            $year = (int) $state;
                            if ($year > 2000 && $year < 3000) {
                                $nextYear = $year + 1;
                                $set('name', "{$year}-{$nextYear}");
                            }
                        }
                    })
                    ->helperText('Format: YYYY-YYYY (e.g., 2024-2025)')
                    ->columnSpan(2),

                // Timeline
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->native(false)
                    ->live()
                    ->prefixIcon('heroicon-m-play')
                    ->afterStateUpdated(function ($set, $get, ?string $state) {
                        if ($state && !$get('end_date')) {
                            $startDate = Carbon::parse($state);
                            $endDate = $startDate->copy()->addMonths(10); // Typical academic year duration
                            $set('end_date', $endDate->format('Y-m-d'));
                        }
                    })
                    ->helperText('Academic year begins')
                    ->columnSpan(1),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->native(false)
                    ->after('start_date')
                    ->prefixIcon('heroicon-m-stop')
                    ->helperText('Academic year ends')
                    ->columnSpan(1),

                Placeholder::make('duration_display')
                    ->label('Duration Information')
                    ->content(function ($get): HtmlString {
                        $startDate = $get('start_date');
                        $endDate = $get('end_date');

                        if ($startDate && $endDate) {
                            $start = Carbon::parse($startDate);
                            $end = Carbon::parse($endDate);
                            $diffInDays = $start->diffInDays($end) + 1;
                            $diffInMonths = $start->diffInMonths($end);

                            return new HtmlString("<div class='p-4 bg-blue-50 rounded-lg border border-blue-200'>" .
                                "<div class='grid grid-cols-2 gap-4'>" .
                                "<div class='text-center'>" .
                                "<div class='text-xl font-bold text-blue-700'>{$diffInDays}</div>" .
                                "<div class='text-sm text-blue-600'>Days</div>" .
                                "</div>" .
                                "<div class='text-center'>" .
                                "<div class='text-xl font-bold text-blue-700'>{$diffInMonths}</div>" .
                                "<div class='text-sm text-blue-600'>Months</div>" .
                                "</div>" .
                                "</div>" .
                                "</div>");
                        }

                        return new HtmlString("<div class='p-4 bg-gray-50 rounded-lg border border-gray-200 text-center'>" .
                            "<div class='text-sm text-gray-500'> Select start and end dates to see duration</div>" .
                            "</div>");
                    })
                    ->columnSpan(2),

                // Status Configuration
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => ' Active',
                        'inactive' => ' Inactive',
                        'draft' => ' Draft',
                        'completed' => ' Completed',
                    ])
                    ->default('active')
                    ->required()
                    ->live()
                    ->prefixIcon('heroicon-m-flag')
                    ->helperText('Current status of the academic year')
                    ->columnSpan(1),

                Toggle::make('is_current')
                    ->label('Set as Current Academic Year')
                    ->helperText('Only one academic year can be current at a time. This will automatically set status to active.')
                    ->live()
                    ->afterStateUpdated(function ($set, ?bool $state) {
                        if ($state) {
                            $set('status', 'active');
                        }
                    })
                    ->columnSpan(1),

                Placeholder::make('status_info')
                    ->label('Status Information')
                    ->content(function ($get): HtmlString {
                        $status = $get('status');
                        $isCurrent = $get('is_current');

                        $statusInfo = match ($status) {
                            'active' => ['This academic year is currently operational and accepting enrollments', 'bg-green-50 border-green-200 text-green-800'],
                            'inactive' => ['This academic year is not in use and not accepting enrollments', 'bg-red-50 border-red-200 text-red-800'],
                            'draft' => ['This academic year is being planned and not yet operational', 'bg-yellow-50 border-yellow-200 text-yellow-800'],
                            'completed' => ['This academic year has ended and is archived', 'bg-gray-50 border-gray-200 text-gray-800'],
                            default => ['Status not defined', 'bg-gray-50 border-gray-200 text-gray-500']
                        };

                        $currentBadge = $isCurrent ? '<div class="mt-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"> Current Academic Year</div>' : '';

                        return new HtmlString("<div class='p-4 rounded-lg border {$statusInfo[1]}'>" .
                            "<div class='text-sm font-medium mb-1'>Status Details:</div>" .
                            "<div class='text-sm'>{$statusInfo[0]}</div>" .
                            $currentBadge .
                            "</div>");
                    })
                    ->columnSpan(2),

                // Quick Setup Information
                Placeholder::make('quick_setup_info')
                    ->label('Quick Setup Tips')
                    ->content(function (): HtmlString {
                        return new HtmlString("<div class='p-4 bg-indigo-50 rounded-lg border border-indigo-200'>" .
                            "<div class='text-sm font-medium text-indigo-800 mb-2'> Quick Setup Tips:</div>" .
                            "<ul class='text-sm text-indigo-700 space-y-1'>" .
                            "<li> Academic years typically run for 10-12 months</li>" .
                            "<li> Only one academic year can be marked as 'current'</li>" .
                            "<li> Use format YYYY-YYYY for academic year names</li>" .
                            "<li> Set start date first - end date will auto-suggest</li>" .
                            "<li> Draft status allows planning before activation</li>" .
                            "</ul>" .
                            "</div>");
                    })
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }
}
