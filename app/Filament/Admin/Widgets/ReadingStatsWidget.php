<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BookIssue;
use App\Models\LibraryBook;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReadingStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBooksIssued = BookIssue::count();
        $booksIssuedThisMonth = BookIssue::whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->count();

        $totalStudentsWithBooks = Student::whereHas('bookIssues')->count();
        $averageBooksPerStudent = $totalStudentsWithBooks > 0
            ? round($totalBooksIssued / $totalStudentsWithBooks, 1)
            : 0;

        $overdueBooks = BookIssue::whereNull('return_date')
            ->where('due_date', '<', now())
            ->count();

        $mostActiveStudent = Student::withCount('bookIssues')
            ->orderByDesc('book_issues_count')
            ->first();

        return [
            Stat::make('Books Issued This Month', $booksIssuedThisMonth)
                ->description('Total books issued this month')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Average Books Per Student', $averageBooksPerStudent)
                ->description('Books per active student')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Overdue Books', $overdueBooks)
                ->description('Books past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueBooks > 0 ? 'danger' : 'success'),

            Stat::make('Most Active Reader', $mostActiveStudent ? $mostActiveStudent->user->name : 'None')
                ->description($mostActiveStudent ? "{$mostActiveStudent->book_issues_count} books issued" : 'No active readers')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
