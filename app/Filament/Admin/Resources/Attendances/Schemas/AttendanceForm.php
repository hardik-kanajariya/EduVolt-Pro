<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\AttendanceSession;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn(Student $record): string => "{$record->first_name} {$record->last_name} ({$record->admission_number})")
                    ->searchable(['first_name', 'last_name', 'admission_number'])
                    ->preload()
                    ->required(),

                Select::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name')
                    ->getOptionLabelFromRecordUsing(fn(SchoolClass $record): string => "{$record->name} - {$record->section}")
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required(),

                Select::make('status')
                    ->label('Attendance Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused'
                    ])
                    ->default('present')
                    ->required(),

                Select::make('session_id')
                    ->label('Session')
                    ->relationship('session', 'session_type')
                    ->getOptionLabelFromRecordUsing(fn(AttendanceSession $record): string => ucfirst($record->session_type) . " Session")
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TimePicker::make('in_time')
                    ->label('In Time')
                    ->seconds(false),

                TimePicker::make('out_time')
                    ->label('Out Time')
                    ->seconds(false),

                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('marked_by')
                    ->label('Marked By')
                    ->relationship('markedBy', 'name')
                    ->default(Auth::id())
                    ->required()
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
