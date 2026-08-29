<?php

namespace App\Filament\Pages;

use App\Services\ContentGapAnalysisService;
use Filament\Pages\Page;

class ContentCoverageMatrixPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | \UnitEnum | null $navigationGroup = 'Knowledge Base & Curriculum';

    protected static ?string $navigationLabel = 'Content Coverage Matrix';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.content-coverage-matrix-page';

    public array $analysisData = [];

    public function mount(): void
    {
        $this->loadMatrix();
    }

    public function loadMatrix(?int $trackId = null): void
    {
        $this->analysisData = app(ContentGapAnalysisService::class)->analyzeCoverage($trackId);
    }
}
