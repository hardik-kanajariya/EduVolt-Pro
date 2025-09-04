<?php

namespace App\Filament\Admin\Resources\ExamMarks\Schemas;

use App\Models\ExamSubject;
use App\Models\Student;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;

class ExamMarkForm
{
 public static function configure(Schema $schema): Schema
 {
 return $schema
 ->components([
 Select::make('exam_subject_id')
 ->label('Exam Subject')
 ->relationship('examSubject', 'id')
 ->getOptionLabelFromRecordUsing(fn($record) => "{$record->exam->name} - {$record->subject->name}")
 ->required()
 ->searchable()
 ->preload(),

 Select::make('student_id')
 ->label('Student')
 ->relationship('student', 'admission_number')
 ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name} ({$record->admission_number})")
 ->required()
 ->searchable()
 ->preload(),

 Toggle::make('is_absent')
 ->label('Mark as Absent')
 ->reactive()
 ->afterStateUpdated(function ($state, $set) {
 if ($state) {
 $set('theory_marks', 0);
 $set('practical_marks', 0);
 $set('total_marks', 0);
 $set('grade', 'AB');
 }
 }),

 TextInput::make('theory_marks')
 ->numeric()
 ->min(0)
 ->reactive()
 ->afterStateUpdated(function ($state, $set, $get) {
 if (!$get('is_absent')) {
 $theory = $state ?? 0;
 $practical = $get('practical_marks') ?? 0;
 $set('total_marks', $theory + $practical);
 }
 })
 ->disabled(fn($get) => $get('is_absent')),

 TextInput::make('practical_marks')
 ->numeric()
 ->min(0)
 ->reactive()
 ->afterStateUpdated(function ($state, $set, $get) {
 if (!$get('is_absent')) {
 $theory = $get('theory_marks') ?? 0;
 $practical = $state ?? 0;
 $set('total_marks', $theory + $practical);
 }
 })
 ->disabled(fn($get) => $get('is_absent')),

 TextInput::make('total_marks')
 ->numeric()
 ->readonly()
 ->dehydrated(),

 TextInput::make('grade')
 ->maxLength(5)
 ->placeholder('Auto-calculated')
 ->readonly(),

 Textarea::make('remarks')
 ->rows(3)
 ->placeholder('Additional comments or notes'),

 Select::make('entered_by')
 ->label('Entered By')
 ->relationship('enteredBy', 'name')
 ->default(Auth::id())
 ->required()
 ->disabled(),

 Select::make('verified_by')
 ->label('Verified By')
 ->relationship('verifiedBy', 'name')
 ->searchable()
 ->preload(),

 Toggle::make('is_verified')
 ->label('Verified')
 ->default(false),

 Hidden::make('entered_at')
 ->default(now()),
 ]);
 }
}
