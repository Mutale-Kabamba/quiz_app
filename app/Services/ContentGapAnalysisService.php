<?php

namespace App\Services;

use App\Models\FormationLevel;
use App\Models\QuestionBankItem;
use App\Models\StudyResource;
use App\Models\TaxonomyTopic;
use App\Models\TaxonomyTrack;
use Illuminate\Support\Collection;

class ContentGapAnalysisService
{
    /**
     * Compute full curriculum coverage matrix and flag pedagogical gaps
     */
    public function analyzeCoverage(?int $trackId = null): array
    {
        $tracksQuery = TaxonomyTrack::where('is_active', true)->with('categories.topics');
        if ($trackId) {
            $tracksQuery->where('id', $trackId);
        }

        $tracks = $tracksQuery->get();
        $matrix = [];
        $totalGapsFound = 0;

        foreach ($tracks as $track) {
            $trackData = [
                'track_id' => $track->id,
                'track_name' => $track->name,
                'topics' => [],
            ];

            foreach ($track->categories as $cat) {
                foreach ($cat->topics as $topic) {
                    $resourceCount = StudyResource::where('topic_id', $topic->id)
                        ->where('status', 'PUBLISHED')
                        ->count();

                    $questionCount = QuestionBankItem::where('topic_id', $topic->id)
                        ->where('status', 'PUBLISHED')
                        ->count();

                    $beginnerQuestions = QuestionBankItem::where('topic_id', $topic->id)
                        ->where('status', 'PUBLISHED')
                        ->whereIn('editorial_difficulty', ['VERY_EASY', 'EASY', 'MEDIUM'])
                        ->count();

                    $advancedQuestions = QuestionBankItem::where('topic_id', $topic->id)
                        ->where('status', 'PUBLISHED')
                        ->whereIn('editorial_difficulty', ['HARD', 'VERY_HARD', 'EXPERT'])
                        ->count();

                    $gaps = [];
                    if ($resourceCount === 0) {
                        $gaps[] = 'NO_STUDY_RESOURCES';
                    }
                    if ($questionCount === 0) {
                        $gaps[] = 'NO_QUESTIONS';
                    } elseif ($questionCount < 10) {
                        $gaps[] = 'LOW_QUESTION_VOLUME';
                    }
                    if ($questionCount > 0 && $advancedQuestions === 0) {
                        $gaps[] = 'NO_ADVANCED_QUESTIONS';
                    }

                    if (!empty($gaps)) {
                        $totalGapsFound++;
                    }

                    $trackData['topics'][] = [
                        'topic_id' => $topic->id,
                        'topic_name' => $topic->name,
                        'category_name' => $cat->name,
                        'resource_count' => $resourceCount,
                        'question_count' => $questionCount,
                        'beginner_count' => $beginnerQuestions,
                        'advanced_count' => $advancedQuestions,
                        'gaps' => $gaps,
                        'health_badge' => empty($gaps) ? '🟢 Covered' : '🔴 Gaps Detected',
                    ];
                }
            }

            $matrix[] = $trackData;
        }

        return [
            'total_gaps' => $totalGapsFound,
            'matrix' => $matrix,
        ];
    }
}
