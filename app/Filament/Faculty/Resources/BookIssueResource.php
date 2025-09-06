<?php

namespace App\Filament\Faculty\Resources;

use App\Filament\Faculty\Resources\BookIssueResource\Pages;
use App\Models\BookIssue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class BookIssueResource extends Resource
{
    protected static ?string $model = BookIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';

    protected static ?string $navigationLabel = 'Book Issues';

    protected static ?string $slug = 'book-issues';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole(['librarian', 'school_admin', 'principal'])) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('school_id', $user->school_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('book_id')
                    ->relationship('book', 'title', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id)
                                ->where('status', 'available');
                        }
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('student_id')
                    ->relationship('student', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('issue_date')
                    ->required(),

                DatePicker::make('due_date')
                    ->required(),

                DatePicker::make('return_date'),

                Select::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'returned' => 'Returned',
                        'overdue' => 'Overdue',
                        'lost' => 'Lost',
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
                TextColumn::make('book.title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.schoolClass.name')
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(
                        fn($record) =>
                        $record->due_date < now() && $record->status !== 'returned'
                            ? 'danger'
                            : 'primary'
                    ),

                TextColumn::make('return_date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'issued',
                        'success' => 'returned',
                        'danger' => 'overdue',
                        'warning' => 'lost',
                    ]),

                TextColumn::make('days_overdue')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->due_date < now() && $record->status !== 'returned'
                            ? now()->diffInDays($record->due_date) . ' days'
                            : '-'
                    )
                    ->color('danger'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'returned' => 'Returned',
                        'overdue' => 'Overdue',
                        'lost' => 'Lost',
                    ]),

                SelectFilter::make('book_id')
                    ->relationship('book', 'title', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),

                SelectFilter::make('student_id')
                    ->relationship('student', 'name', function (Builder $query) {
                        $user = Auth::user();
                        if ($user) {
                            $query->where('school_id', $user->school_id);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('return')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'return_date' => now(),
                            'status' => 'returned',
                        ]);
                    })
                    ->visible(fn($record) => $record->status === 'issued'),
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
            'index' => Pages\ListBookIssues::route('/'),
            'create' => Pages\CreateBookIssue::route('/create'),
            'view' => Pages\ViewBookIssue::route('/{record}'),
            'edit' => Pages\EditBookIssue::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['librarian', 'school_admin', 'principal']);
    }
}
