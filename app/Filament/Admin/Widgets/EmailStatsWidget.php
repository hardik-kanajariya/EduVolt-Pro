<?php

namespace App\Filament\Admin\Widgets;

use App\Models\EmailLog;
use App\Models\BulkEmail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        // Today's stats
        $todayTotal = EmailLog::where('created_at', '>=', $today)->count();
        $todaySent = EmailLog::where('created_at', '>=', $today)->sent()->count();
        $todayFailed = EmailLog::where('created_at', '>=', $today)->failed()->count();

        // This week's stats
        $weekTotal = EmailLog::where('created_at', '>=', $thisWeek)->count();
        $weekSent = EmailLog::where('created_at', '>=', $thisWeek)->sent()->count();

        // This month's stats
        $monthTotal = EmailLog::where('created_at', '>=', $thisMonth)->count();

        // Pending emails
        $pendingEmails = EmailLog::pending()->count();

        // Active campaigns
        $activeCampaigns = BulkEmail::whereIn('status', ['scheduled', 'sending'])->count();

        // Calculate success rate
        $successRate = $todayTotal > 0 ? round(($todaySent / $todayTotal) * 100, 1) : 0;

        return [
            Stat::make('Emails Today', $todayTotal)
                ->description($todaySent . ' sent, ' . $todayFailed . ' failed')
                ->descriptionIcon($todayFailed > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($todayFailed > 0 ? 'danger' : 'success')
                ->chart($this->getEmailChart('today')),

            Stat::make('Success Rate', $successRate . '%')
                ->description('Today\'s delivery success rate')
                ->descriptionIcon($successRate >= 95 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($successRate >= 95 ? 'success' : ($successRate >= 90 ? 'warning' : 'danger')),

            Stat::make('This Week', $weekTotal)
                ->description($weekSent . ' sent successfully')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart($this->getEmailChart('week')),

            Stat::make('Pending Queue', $pendingEmails)
                ->description($activeCampaigns . ' active campaigns')
                ->descriptionIcon($pendingEmails > 100 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-clock')
                ->color($pendingEmails > 100 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.email-logs.index')),
        ];
    }

    private function getEmailChart(string $period): array
    {
        switch ($period) {
            case 'today':
                // Last 24 hours, hourly
                $data = [];
                for ($i = 23; $i >= 0; $i--) {
                    $hour = now()->subHours($i)->startOfHour();
                    $count = EmailLog::where('created_at', '>=', $hour)
                        ->where('created_at', '<', $hour->copy()->addHour())
                        ->count();
                    $data[] = $count;
                }
                return $data;

            case 'week':
                // Last 7 days, daily
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $day = now()->subDays($i)->startOfDay();
                    $count = EmailLog::where('created_at', '>=', $day)
                        ->where('created_at', '<', $day->copy()->addDay())
                        ->count();
                    $data[] = $count;
                }
                return $data;

            default:
                return [];
        }
    }
}
