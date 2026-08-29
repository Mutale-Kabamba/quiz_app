<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\Question;
use App\Models\User;
use App\Services\DiocesanAnalyticsService;
use App\Services\QuestionQualityService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DiocesanReportsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | UnitEnum | null $navigationGroup = 'Reports & Exports';
    protected static ?string $navigationLabel = 'Diocesan Reports';
    protected string $view = 'filament.pages.diocesan-reports-page';
    protected static ?string $title = 'Livingstone Diocesan Youth Ministry Reports';

    public string $reportType = 'census'; // census, deanery, content, quality

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public function setReport(string $type): void
    {
        $this->reportType = $type;
    }

    public function getViewData(): array
    {
        $analyticsService = app(DiocesanAnalyticsService::class);
        $kpis = $analyticsService->getDiocesanKpis();
        $deaneries = $analyticsService->getDeaneryPerformance();

        // Parishes census
        $parishes = Parish::with('deanery')
            ->withCount(['users' => fn($q) => $q->where('role', 'youth')])
            ->get();

        // Problematic / Skewed Questions
        $reportedQuestions = app(QuestionQualityService::class)->getQuestionsRequiringReview();

        return [
            'kpis' => $kpis,
            'deaneries' => $deaneries,
            'parishes' => $parishes,
            'reportedQuestions' => $reportedQuestions,
            'generatedDate' => Carbon::now()->format('F d, Y - H:i'),
        ];
    }
}
