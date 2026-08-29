<?php

namespace App\Filament\Widgets;

use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\QuestionReport;
use App\Services\AntiCheatingService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DiocesanActionRequiredWidget extends Widget
{
    protected string $view = 'filament.widgets.diocesan-action-required-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public function getViewData(): array
    {
        $pendingReports = QuestionReport::where('status', 'pending')->count();
        $pendingTransfers = ParishTransfer::where('status', 'pending')->count();
        $cheatingFlags = app(AntiCheatingService::class)->getSuspiciousActivity();

        return [
            'pending_reports' => $pendingReports,
            'pending_transfers' => $pendingTransfers,
            'cheating_flags_count' => count($cheatingFlags),
            'cheating_flags' => $cheatingFlags,
        ];
    }
}
