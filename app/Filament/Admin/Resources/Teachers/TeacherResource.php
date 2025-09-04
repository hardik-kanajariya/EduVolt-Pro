<?php

namespace App\Filament\Admin\Resources\Teachers;

use App\Filament\Admin\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Admin\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Admin\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Admin\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Admin\Resources\Teachers\Tables\TeachersTable;
use App\Models\Teacher;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use BackedEnum;

class TeacherResource extends Resource
{
 protected static ?string $model = Teacher::class;

 protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

 protected static string | UnitEnum | null $navigationGroup = 'Academic Structure';

 protected static ?string $recordTitleAttribute = 'employee_id';

 public static function form(Schema $schema): Schema
 {
 return TeacherForm::configure($schema);
 }

 public static function table(Table $table): Table
 {
 return TeachersTable::configure($table);
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
 'index' => ListTeachers::route('/'),
 'create' => CreateTeacher::route('/create'),
 'view' => ViewTeacher::route('/{record}'),
 'edit' => EditTeacher::route('/{record}/edit'),
 ];
 }

 public static function getEloquentQuery(): Builder
 {
 return parent::getEloquentQuery()
 ->withoutGlobalScopes([
 SoftDeletingScope::class,
 ]);
 }

 public static function getNavigationBadge(): ?string
 {
 return static::getModel()::where('status', 'active')->count();
 }

 public static function getNavigationBadgeColor(): ?string
 {
 return 'success';
 }
}
