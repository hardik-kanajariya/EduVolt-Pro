<?php

namespace App\Filament\Admin\Widgets;

use App\Models\LibraryBook;
use Filament\Widgets\ChartWidget;

class PopularBooksWidget extends ChartWidget
{
    protected ?string $heading = 'Popular Books';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $popularBooks = LibraryBook::withCount('bookIssues')
            ->orderByDesc('book_issues_count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Issues',
                    'data' => $popularBooks->pluck('book_issues_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 101, 101, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(6, 182, 212, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $popularBooks->map(function ($book) {
                return strlen($book->title) > 20 ? substr($book->title, 0, 17) . '...' : $book->title;
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Most Popular Books by Issue Count',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Issues',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Books',
                    ],
                ],
            ],
        ];
    }
}
