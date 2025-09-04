<?php

namespace App\Filament\Admin\Resources\ExamMarks;

use App\Filament\Admin\Resources\ExamMarks\Pages\CreateExamMark;
use App\Filament\Admin\Resources\ExamMarks\Pages\EditExamMark;
use App\Filament\Admin\Resources\ExamMarks\Pages\ListExamMarks;
use App\Filament\Admin\Resources\ExamMarks\Schemas\ExamMarkForm;
use App\Filament\Admin\Resources\ExamMarks\Tables\ExamMarksTable;
use App\Models\ExamMark;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExamMarkResource extends Resource
{
 protected static ?string $model = ExamMark::class;

 protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-trophy';

 protected static string | UnitEnum | null $navigationGroup = 'Examination System';

 protected static ?int $navigationSort = 6;

 public static function form(Schema $schema): Schema
 {
 return ExamMarkForm::configure($schema);
 }

 public static function table(Table $table): Table
 {
 return ExamMarksTable::configure($table);
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
 'index' => ListExamMarks::route('/'),
 'create' => CreateExamMark::route('/create'),
 'edit' => EditExamMark::route('/{record}/edit'),
 ];
 }
}
