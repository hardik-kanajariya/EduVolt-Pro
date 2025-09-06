<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\MyAssignmentsResource\Pages;
use App\Models\Assignment;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class MyAssignmentsResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'My Assignments';

    protected static ?string $slug = 'my-assignments';

    protected static ?string $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->isTeacher()) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Get classes where the user is assigned as a teacher
        $classIds = $user->assignedClasses()->pluck('school_classes.id');

        return parent::getEloquentQuery()
            ->whereIn('class_id', $classIds)
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->relationship('schoolClass', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $classIds = $user->assignedClasses()->pluck('school_classes.id');
                            $query->whereIn('id', $classIds)
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

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(4),

                DatePicker::make('due_date')
                    ->required()
                    ->minDate(now()),

                TextInput::make('max_marks')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                FileUpload::make('attachment')
                    ->directory('assignments')
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])
                    ->maxSize(10240), // 10MB

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('max_marks')
                    ->label('Max Marks')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'danger' => 'closed',
                    ]),

                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),

                SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $classIds = $user->assignedClasses()->pluck('school_classes.id');
                            $query->whereIn('id', $classIds)
                                ->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user && $user->isTeacher()) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),
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
            'index' => Pages\ListMyAssignments::route('/'),
            'create' => Pages\CreateMyAssignments::route('/create'),
            'view' => Pages\ViewMyAssignments::route('/{record}'),
            'edit' => Pages\EditMyAssignments::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isTeacher();
    }
}
