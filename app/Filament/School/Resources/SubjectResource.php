<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Subjects';

    protected static ?string $modelLabel = 'Subject';

    protected static ?string $pluralModelLabel = 'Subjects';

    protected static ?string $navigationGroup = 'Academic Structure';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when($user && !$user->isSuperAdmin(), function (Builder $query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->with('school');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subject Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->options([
                                'core' => 'Core Subject',
                                'elective' => 'Elective Subject',
                                'language' => 'Language Subject',
                                'practical' => 'Practical Subject',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('credits')
                            ->numeric(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000),
                        Forms\Components\Toggle::make('is_active'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'core' => 'primary',
                        'elective' => 'success',
                        'language' => 'warning',
                        'practical' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('credits')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'core' => 'Core Subject',
                        'elective' => 'Elective Subject',
                        'language' => 'Language Subject',
                        'practical' => 'Practical Subject',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'view' => Pages\ViewSubject::route('/{record}'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPrincipal());
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isPrincipal());
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->isSuperAdmin()) return true;

        return ($user->isSchoolAdmin() || $user->isPrincipal()) &&
            $user->school_id === $record->school_id;
    }
}
