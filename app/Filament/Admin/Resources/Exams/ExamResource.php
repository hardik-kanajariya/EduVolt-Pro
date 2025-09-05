<?php

namespace App\Filament\Admin\Resources\Exams;

use App\Filament\Admin\Resources\Exams\Pages\CreateExam;
use App\Filament\Admin\Resources\Exams\Pages\EditExam;
use App\Filament\Admin\Resources\Exams\Pages\ListExams;
use App\Filament\Admin\Resources\Exams\Schemas\ExamForm;
use App\Filament\Admin\Resources\Exams\Tables\ExamsTable;
use App\Models\Exam;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Examination System';

    public static function form(Form $form): Form
    {
        return ExamForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }
}
