<?php

namespace App\Filament\Pages;

use App\Models\Parish;
use App\Services\ParishAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishPerformancePage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-pie';
    protected static string | UnitEnum | null $navigationGroup = 'Formation & Performance';
    protected static ?string $navigationLabel = 'Study Track Performance';
    protected string $view = 'filament.pages.parish-performance-page';
    protected static ?string $title = 'Parish Catechetical & Quiz Analytics';

    public function getViewData(): array
    {
        $user = Auth::user();
        if (!$user || !$user->parish_id) {
            return ['analytics' => null, 'myParish' => null];
        }

        $analytics = app(ParishAnalyticsService::class)->getTrackPerformance($user->parish_id);
        $myParish = Parish::find($user->parish_id);

        return [
            'analytics' => $analytics,
            'myParish' => $myParish,
        ];
    }
}
