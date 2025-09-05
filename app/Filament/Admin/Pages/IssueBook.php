<?php

namespace App\Filament\Admin\Pages;

use App\Models\LibraryBook;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class IssueBook extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?string $navigationLabel = 'Issue Book';

    protected static ?string $title = 'Issue Book to Member';

    protected static string $view = 'filament.admin.pages.issue-book';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Member Information')
                    ->description('Select the member who will receive the book')
                    ->schema([
                        Forms\Components\Select::make('member_type')
                            ->label('Member Type')
                            ->options([
                                'student' => 'Student',
                                'teacher' => 'Teacher',
                                'staff' => 'Staff',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('member_id', null)),

                        Forms\Components\Select::make('member_id')
                            ->label('Member')
                            ->options(function (Get $get) {
                                $memberType = $get('member_type');
                                if (!$memberType) {
                                    return [];
                                }

                                return match ($memberType) {
                                    'student' => Student::query()
                                        ->whereHas('user')
                                        ->with('user')
                                        ->get()
                                        ->pluck('user.name', 'id'),
                                    'teacher' => Teacher::query()
                                        ->whereHas('user')
                                        ->with('user')
                                        ->get()
                                        ->pluck('user.name', 'id'),
                                    'staff' => Staff::query()
                                        ->whereHas('user')
                                        ->with('user')
                                        ->get()
                                        ->pluck('user.name', 'id'),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if (!$state) return;

                                $memberType = $get('member_type');
                                $member = match ($memberType) {
                                    'student' => Student::with('user')->find($state),
                                    'teacher' => Teacher::with('user')->find($state),
                                    'staff' => Staff::with('user')->find($state),
                                    default => null,
                                };

                                if ($member) {
                                    $set('member_name', $member->user->name ?? 'Unknown');
                                    $set('member_email', $member->user->email ?? '');
                                }
                            }),

                        Forms\Components\TextInput::make('member_name')
                            ->label('Member Name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('member_email')
                            ->label('Member Email')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Book Information')
                    ->description('Select the book to be issued')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('Book')
                            ->relationship('book', 'title')
                            ->searchable(['title', 'isbn', 'author'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!$state) return;

                                $book = LibraryBook::find($state);
                                if ($book) {
                                    $set('book_title', $book->title);
                                    $set('book_author', $book->author);
                                    $set('book_isbn', $book->isbn);
                                    $set('available_copies', $book->available_copies);
                                }
                            })
                            ->helperText('Search by title, author, or ISBN'),

                        Forms\Components\TextInput::make('book_title')
                            ->label('Book Title')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('book_author')
                            ->label('Author')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('book_isbn')
                            ->label('ISBN')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('available_copies')
                            ->label('Available Copies')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Issue Details')
                    ->description('Configure the book issue details')
                    ->schema([
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->default(now()->addDays(14))
                            ->minDate(now()),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'normal' => 'Normal',
                                'high' => 'High Priority',
                                'urgent' => 'Urgent',
                            ])
                            ->default('normal')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Issue Notes')
                            ->maxLength(500)
                            ->placeholder('Any special notes about this book issue...')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('send_notification')
                            ->label('Send Email Notification')
                            ->default(true)
                            ->helperText('Notify the member about the book issue'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function issueBook(): void
    {
        try {
            $data = $this->form->getState();

            // Validate book availability
            $book = LibraryBook::find($data['book_id']);
            if (!$book || $book->available_copies <= 0) {
                throw new Halt('Selected book is not available for issue.');
            }

            // Check if member already has this book
            $existingIssue = BookIssue::where('book_id', $data['book_id'])
                ->where('member_type', $data['member_type'])
                ->where('member_id', $data['member_id'])
                ->whereNull('return_date')
                ->exists();

            if ($existingIssue) {
                throw new Halt('This member already has this book issued.');
            }

            // Check member's book limit
            $currentIssues = BookIssue::where('member_type', $data['member_type'])
                ->where('member_id', $data['member_id'])
                ->whereNull('return_date')
                ->count();

            $maxBooks = match ($data['member_type']) {
                'student' => 3,
                'teacher' => 5,
                'staff' => 4,
                default => 2,
            };

            if ($currentIssues >= $maxBooks) {
                throw new Halt("Member has reached maximum book limit of {$maxBooks} books.");
            }

            // Create book issue record
            $issue = BookIssue::create([
                'book_id' => $data['book_id'],
                'member_type' => $data['member_type'],
                'member_id' => $data['member_id'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'priority' => $data['priority'],
                'notes' => $data['notes'] ?? null,
                'status' => 'issued',
                'issued_by' => Auth::id(),
            ]);

            // Update book availability
            $book->decrement('available_copies');

            // Send notification if requested
            if ($data['send_notification']) {
                // Send notification logic here
                // You can implement email notifications here
            }

            Notification::make()
                ->title('Book Issued Successfully')
                ->body("Book '{$book->title}' has been issued to the member.")
                ->success()
                ->send();

            // Reset form
            $this->form->fill();
            $this->data = [];
        } catch (Halt $exception) {
            Notification::make()
                ->title('Issue Failed')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('issueBook')
                ->label('Issue Book')
                ->icon('heroicon-o-book-open')
                ->color('success')
                ->action('issueBook')
                ->requiresConfirmation()
                ->modalHeading('Confirm Book Issue')
                ->modalDescription('Are you sure you want to issue this book to the selected member?')
                ->modalSubmitActionLabel('Yes, Issue Book'),

            Action::make('viewActiveIssues')
                ->label('Active Issues')
                ->icon('heroicon-o-list-bullet')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.book-issues.index')),

            Action::make('quickStats')
                ->label('Library Stats')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->modalContent(view('filament.admin.pages.library-stats-modal'))
                ->modalHeading('Quick Library Statistics')
                ->slideOver(),
        ];
    }

    public function getTitle(): string
    {
        return 'Issue Book to Member';
    }

    public function getHeading(): string
    {
        return 'Issue Book';
    }

    public function getSubheading(): ?string
    {
        return 'Issue library books to students, teachers, and staff members';
    }
}
