<?php

namespace App\Filament\Widgets;

use App\Services\ParishDashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParishKpiOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::user()?->isChairperson() ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user || !$user->parish_id) {
            return [];
        }

        $kpis = app(ParishDashboardService::class)->getParishKpis($user->parish_id);

        return [
            Stat::make('Total Parish Youth', number_format($kpis['total_youth']) . ' Members')
                ->description("{$kpis['active_this_week']} Active This Week")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Parish Engagement', $kpis['health_status']['badge'])
                ->description("{$kpis['health_status']['rate']}% of youth active this week")
                ->color($kpis['health_status']['color']),

            Stat::make('Lessons Completed', number_format($kpis['lessons_completed']))
                ->description('Catechetical formation tracks')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Quizzes Completed', number_format($kpis['quizzes_completed']))
                ->description("Avg Accuracy: {$kpis['avg_accuracy']}%")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Total Parish XP', number_format($kpis['parish_xp']) . ' XP')
                ->description('Competitive formation points')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Average Quiz Score', "{$kpis['avg_score']} pts")
                ->description("Avg Accuracy: {$kpis['avg_accuracy']}%")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
