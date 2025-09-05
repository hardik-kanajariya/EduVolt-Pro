<?php

namespace App\Filament\Admin\Resources\ExamSubjects;

use App\Filament\Admin\Resources\ExamSubjects\Pages\CreateExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\EditExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\ListExamSubjects;
use App\Filament\Admin\Resources\ExamSubjects\Schemas\ExamSubjectForm;
use App\Filament\Admin\Resources\ExamSubjects\Tables\ExamSubjectsTable;
use App\Models\ExamSubject;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ExamSubjectResource extends Resource
{
    protected static ?string $model = ExamSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Examination System';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return ExamSubjectForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return ExamSubjectsTable::configure($table);
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
            'index' => ListExamSubjects::route('/'),
            'create' => CreateExamSubject::route('/create'),
            'edit' => EditExamSubject::route('/{record}/edit'),
        ];
    }
}
