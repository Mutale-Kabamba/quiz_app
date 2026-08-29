<?php

namespace App\Filament\Pages;

use App\Services\ParishReportService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishReportPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Monthly Parish Report';
    protected string $view = 'filament.pages.parish-report-page';
    protected static ?string $title = 'Monthly Youth Ministry Report';

    public function getViewData(): array
    {
        $user = Auth::user();
        if (!$user || !$user->parish_id) {
            return ['report' => null];
        }

        $report = app(ParishReportService::class)->generateMonthlyReport($user->parish_id, Carbon::now());

        return [
            'report' => $report,
        ];
    }
}
