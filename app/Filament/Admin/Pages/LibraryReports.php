<?php

namespace App\Filament\Admin\Pages;

use App\Models\BookIssue;
use App\Models\LibraryBook;
use App\Models\LibraryFine;
use App\Models\Student;
use Filament\Pages\Page;

class LibraryReports extends Page
{
    protected static string $view = 'filament.admin.pages.library-reports';

    public function getOverviewStats()
    {
        return [
            'total_books' => LibraryBook::count(),
            'total_copies' => LibraryBook::sum('total_copies'),
            'available_copies' => LibraryBook::sum('available_copies'),
            'issued_books' => BookIssue::whereNull('return_date')->count(),
            'overdue_books' => BookIssue::whereNull('return_date')
                ->where('due_date', '<', now())
                ->count(),
            'total_fines' => LibraryFine::where('status', 'unpaid')->sum('amount'),
        ];
    }

    public function getCirculationData()
    {
        return BookIssue::selectRaw('DATE(issue_date) as date, COUNT(*) as issues')
            ->whereBetween('issue_date', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getOverdueBooks()
    {
        return BookIssue::with(['libraryBook', 'student'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(10)
            ->get();
    }

    public function getPopularBooks()
    {
        return LibraryBook::withCount('bookIssues')
            ->orderByDesc('book_issues_count')
            ->limit(10)
            ->get();
    }

    public function getFinesReport()
    {
        return LibraryFine::with(['student', 'bookIssue.libraryBook'])
            ->where('status', 'unpaid')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    public function getStudentActivity()
    {
        return Student::withCount(['bookIssues' => function ($q) {
            $q->whereBetween('issue_date', [now()->subDays(30), now()]);
        }])
            ->having('book_issues_count', '>', 0)
            ->orderByDesc('book_issues_count')
            ->limit(10)
            ->get();
    }
}
