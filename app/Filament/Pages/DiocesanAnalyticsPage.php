<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\DiocesanAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DiocesanAnalyticsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string | UnitEnum | null $navigationGroup = 'Analytics & Insights';
    protected static ?string $navigationLabel = 'Diocesan Analytics';
    protected string $view = 'filament.pages.diocesan-analytics-page';
    protected static ?string $title = 'Livingstone Diocesan Youth Ministry Analytics';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public function getViewData(): array
    {
        $analyticsService = app(DiocesanAnalyticsService::class);
        $kpis = $analyticsService->getDiocesanKpis();
        $deaneryPerformance = $analyticsService->getDeaneryPerformance();

        // Top 5 Parishes in Diocese
        $topParishes = Parish::with('deanery')
            ->withCount(['users' => fn($q) => $q->where('role', 'youth')])
            ->get()
            ->map(function ($p) {
                $p->total_xp = (int) User::where('parish_id', $p->id)->sum('xp');
                $p->quizzes_count = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $p->id))->count();
                return $p;
            })
            ->sortByDesc('total_xp')
            ->take(5)
            ->values();

        // Study Track Popularity
        $tracks = Category::withCount(['lessons', 'questions'])->get();

        return [
            'kpis' => $kpis,
            'deaneries' => $deaneryPerformance,
            'topParishes' => $topParishes,
            'tracks' => $tracks,
        ];
    }
}
