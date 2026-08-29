<?php

namespace App\Filament\Widgets;

use App\Services\DiocesanAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DiocesanCommandKpisWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $kpis = app(DiocesanAnalyticsService::class)->getDiocesanKpis();

        return [
            Stat::make('Diocesan Youth Population', number_format($kpis['total_youth']) . ' Registered')
                ->description("{$kpis['active_this_week']} Active This Week &bull; {$kpis['new_registrations_month']} New This Month")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Active Parishes', "{$kpis['active_parishes']} / {$kpis['total_parishes']}")
                ->description("{$kpis['inactive_parishes']} Parishes Inactive")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color($kpis['inactive_parishes'] > 0 ? 'warning' : 'success'),

            Stat::make('Catechetical Lessons Mastered', number_format($kpis['lessons_completed']))
                ->description("{$kpis['flashcards_reviewed']} Flashcards Reviewed")
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Competitive Quiz Sessions', number_format($kpis['quizzes_completed']))
                ->description("Avg Accuracy: {$kpis['avg_accuracy']}%")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Total Diocesan XP Awarded', number_format($kpis['total_xp_awarded']) . ' XP')
                ->description("{$kpis['ranked_sessions']} Ranked Competition Rounds")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Active Engagement (WAU / MAU)', "{$kpis['wau']} / {$kpis['mau']}")
                ->description("{$kpis['dau']} Daily Active Youths Today")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
