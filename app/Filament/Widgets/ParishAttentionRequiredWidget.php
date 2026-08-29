<?php

namespace App\Filament\Widgets;

use App\Services\ParishDashboardService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ParishAttentionRequiredWidget extends Widget
{
    protected string $view = 'filament.widgets.parish-attention-required-widget';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->isChairperson() ?? false;
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        if (!$user || !$user->parish_id) {
            return ['kpis' => null, 'user' => $user];
        }

        $kpis = app(ParishDashboardService::class)->getParishKpis($user->parish_id);

        return [
            'kpis' => $kpis,
            'user' => $user,
        ];
    }
}
