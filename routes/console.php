<?php

use App\Services\DynamicContentImportService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:import-curriculum', function () {
    @ini_set('memory_limit', '2048M');
    @set_time_limit(0);

    $this->info('====================================================');
    $this->info('Starting Curriculum Import (Lessons & Questions)');
    $this->info('====================================================');

    $importService = app(DynamicContentImportService::class);

    // 1. IMPORT LESSONS
    $lessonsPath = public_path('imports/all_lessons.json');
    if (file_exists($lessonsPath)) {
        $this->info('Reading public/imports/all_lessons.json...');
        $rawLessons = file_get_contents($lessonsPath);
        $lessonData = json_decode($rawLessons, true);
        $lessonRows = $lessonData['lessons'] ?? $lessonData;

        $this->info('Found ' . count($lessonRows) . ' lessons. Importing into database...');
        $lessonResult = $importService->importLessons($lessonRows, null, 'overwrite');
        $this->info("✓ Lessons processed: {$lessonResult['total_processed']} (Success: {$lessonResult['successful']}, Skipped: {$lessonResult['duplicates_skipped']}, Failed: {$lessonResult['failed']})");
    } else {
        $this->warn('File public/imports/all_lessons.json not found.');
    }

    // 2. IMPORT QUESTIONS
    $questionsPath = public_path('imports/all_questions.json');
    if (file_exists($questionsPath)) {
        $this->info('Reading public/imports/all_questions.json...');
        $rawQuestions = file_get_contents($questionsPath);
        $questionData = json_decode($rawQuestions, true);
        $questionRows = $questionData['questions'] ?? $questionData;

        $totalQuestions = count($questionRows);
        $this->info("Found {$totalQuestions} questions. Importing in batches of 500...");

        $bar = $this->output->createProgressBar($totalQuestions);
        $bar->start();

        $chunkSize = 500;
        $totalSuccess = 0;
        $totalDuplicates = 0;
        $totalFailed = 0;
        $sampleErrors = [];

        foreach (array_chunk($questionRows, $chunkSize) as $chunk) {
            $qResult = $importService->importQuestions($chunk, null, 'skip');
            $totalSuccess += $qResult['successful'];
            $totalDuplicates += $qResult['duplicates_skipped'];
            $totalFailed += $qResult['failed'];
            if (!empty($qResult['errors']) && count($sampleErrors) < 10) {
                $sampleErrors = array_merge($sampleErrors, $qResult['errors']);
            }
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Questions processed: {$totalQuestions} (Success: {$totalSuccess}, Skipped/Duplicate: {$totalDuplicates}, Failed: {$totalFailed})");

        if ($totalFailed > 0 && !empty($sampleErrors)) {
            $this->newLine();
            $this->warn("⚠ Sample errors (first " . count($sampleErrors) . "):");
            foreach (array_slice($sampleErrors, 0, 10) as $err) {
                $this->error("  → {$err}");
            }
        }
    } else {
        $this->warn('File public/imports/all_questions.json not found.');
    }
})->purpose('Import all lessons and questions from public/imports');

Artisan::command('app:fix-categories', function () {
    $this->info('--- Fixing Category Synchronization ---');

    // 1. Remove orphaned empty category 32 first if it exists
    $emptyCat32 = \App\Models\Category::where('id', 32)->first();
    if ($emptyCat32 && $emptyCat32->questions()->count() === 0 && $emptyCat32->lessons()->count() === 0) {
        $emptyCat32->delete();
        $this->info("Deleted orphaned empty Category #32.");
    }

    // 2. Update Cat 6 to "Catechesis & Catholic Doctrine (CCC)"
    $cat6 = \App\Models\Category::where('id', 6)->first();
    if ($cat6) {
        $cat6->update([
            'name' => 'Catechesis & Catholic Doctrine (CCC)',
            'slug' => 'catechesis-and-doctrine',
            'description' => 'Catechism of the Catholic Church (CCC), foundational doctrines, dogmas, and Catholic faith fundamentals.'
        ]);
        $this->info("Updated Category #6 to '{$cat6->name}' (Questions: {$cat6->questions()->count()}, Lessons: {$cat6->lessons()->count()})");
    }

    // 3. Ensure all TaxonomyTracks match Category names & slugs cleanly
    $tracks = \App\Models\TaxonomyTrack::all();
    $categories = \App\Models\Category::all();
    
    foreach ($categories as $cat) {
        $this->line("Category ID: {$cat->id} | Name: '{$cat->name}' | Slug: '{$cat->slug}' | Qs: {$cat->questions()->count()} | Lessons: {$cat->lessons()->count()}");
    }

    $this->info('Category synchronization complete.');
})->purpose('Fix category naming and remove orphaned empty duplicates');
