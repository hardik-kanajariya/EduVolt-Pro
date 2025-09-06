<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyGradesResource\Pages;
use App\Models\Grade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MyGradesResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'My Grades';

    protected static ?string $slug = 'my-grades';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->isTeacher()) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Get classes where the user is assigned as a teacher
        $classIds = $user->assignedClasses()->pluck('school_classes.id');

        return parent::getEloquentQuery()
            ->whereHas('student', function (Builder $query) use ($classIds, $user) {
                $query->whereIn('class_id', $classIds)
                    ->where('school_id', $user->school_id);
            })
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->relationship('student', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $classIds = $user->assignedClasses()->pluck('school_classes.id');
                            $query->whereIn('class_id', $classIds)
                                ->where('school_id', $user->school_id);
                        }
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('subject_id')
                    ->relationship('subject', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('exam_id')
                    ->relationship('exam', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('marks_obtained')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('total_marks')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Select::make('grade')
                    ->options([
                        'A+' => 'A+',
                        'A' => 'A',
                        'B+' => 'B+',
                        'B' => 'B',
                        'C+' => 'C+',
                        'C' => 'C',
                        'D' => 'D',
                        'F' => 'F',
                    ])
                    ->required(),

                Textarea::make('remarks')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exam.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('marks_obtained')
                    ->sortable(),

                TextColumn::make('total_marks')
                    ->sortable(),

                TextColumn::make('percentage')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->total_marks > 0
                            ? round(($record->marks_obtained / $record->total_marks) * 100, 2) . '%'
                            : '0%'
                    )
                    ->sortable(),

                TextColumn::make('grade')
                    ->badge()
                    ->colors([
                        'success' => ['A+', 'A'],
                        'warning' => ['B+', 'B'],
                        'primary' => ['C+', 'C'],
                        'danger' => ['D', 'F'],
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->relationship('student', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $classIds = $user->assignedClasses()->pluck('school_classes.id');
                            $query->whereIn('class_id', $classIds)
                                ->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('exam_id')
                    ->relationship('exam', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('grade')
                    ->options([
                        'A+' => 'A+',
                        'A' => 'A',
                        'B+' => 'B+',
                        'B' => 'B',
                        'C+' => 'C+',
                        'C' => 'C',
                        'D' => 'D',
                        'F' => 'F',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMyGrades::route('/'),
            'create' => Pages\CreateMyGrades::route('/create'),
            'view' => Pages\ViewMyGrades::route('/{record}'),
            'edit' => Pages\EditMyGrades::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isTeacher();
    }
}
