<?php

namespace App\Filament\Admin\Resources\ExamSubjects;

use App\Filament\Admin\Resources\ExamSubjects\Pages\CreateExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\EditExamSubject;
use App\Filament\Admin\Resources\ExamSubjects\Pages\ListExamSubjects;
use App\Filament\Admin\Resources\ExamSubjects\Schemas\ExamSubjectForm;
use App\Filament\Admin\Resources\ExamSubjects\Tables\ExamSubjectsTable;
use App\Models\ExamSubject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamSubjectResource extends Resource
{
    protected static ?string $model = ExamSubject::class;

    

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ExamSubjectForm::configure($schema);
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
