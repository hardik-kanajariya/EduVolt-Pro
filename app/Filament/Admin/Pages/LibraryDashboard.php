<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\LibraryBook;
use App\Models\BookIssue;
use App\Models\Student;

class LibraryDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Library Dashboard';

    protected static ?string $navigationLabel = 'Library Dashboard';

    protected static string $view = 'filament.admin.pages.library-dashboard';

    public function getHeaderActions(): array
    {
        return [
            Action::make('issue_book')
                ->label('Issue Book')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->url(route('filament.admin.pages.issue-book')),

            Action::make('return_book')
                ->label('Return Book')
                ->icon('heroicon-o-arrow-left')
                ->color('warning')
                ->url(route('filament.admin.pages.return-book')),

            Action::make('library_reports')
                ->label('Reports')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->url(route('filament.admin.pages.library-reports')),
        ];
    }

    public function getViewData(): array
    {
        return [
            'totalBooks' => LibraryBook::count(),
            'availableBooks' => LibraryBook::where('available_copies', '>', 0)->count(),
            'issuedBooks' => BookIssue::whereNull('return_date')->count(),
            'overdueBooks' => BookIssue::whereNull('return_date')
                ->where('due_date', '<', now())
                ->count(),
            'totalStudents' => Student::count(),
            'activeIssues' => BookIssue::whereNull('return_date')->count(),
            'recentIssues' => BookIssue::with(['book', 'student'])
                ->latest()
                ->limit(5)
                ->get(),
            'popularBooks' => LibraryBook::withCount('issues')
                ->orderBy('issues_count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}
