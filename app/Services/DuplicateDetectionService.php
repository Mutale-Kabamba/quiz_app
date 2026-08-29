<?php

namespace App\Services;

use App\Models\QuestionBankItem;
use Illuminate\Support\Collection;

class DuplicateDetectionService
{
    /**
     * Compute a deterministic normalized similarity hash for question text
     */
    public function generateSimilarityHash(string $text): string
    {
        // 1. Lowercase and remove punctuation
        $cleaned = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $text));

        // 2. Tokenize and remove common stopwords
        $stopWords = ['what', 'is', 'the', 'of', 'in', 'and', 'a', 'an', 'to', 'for', 'which', 'who', 'how', 'many', 'does', 'by', 'that', 'are', 'was'];
        $tokens = array_filter(explode(' ', $cleaned), fn($t) => strlen($t) > 1 && !in_array($t, $stopWords));

        // 3. Sort tokens alphabetically
        sort($tokens);

        return hash('sha256', implode(' ', $tokens));
    }

    /**
     * Calculate token Jaccard similarity between two strings (0.0 to 1.0)
     */
    public function calculateSimilarityScore(string $textA, string $textB): float
    {
        $tokensA = array_unique(explode(' ', strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $textA))));
        $tokensB = array_unique(explode(' ', strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', '', $textB))));

        $intersection = count(array_intersect($tokensA, $tokensB));
        $union = count(array_unique(array_merge($tokensA, $tokensB)));

        if ($union === 0) {
            return 0.0;
        }

        return round($intersection / $union, 3);
    }

    /**
     * Find potential duplicate questions for a candidate question text
     */
    public function findPotentialDuplicates(string $candidateText, ?int $trackId = null, float $threshold = 0.75): Collection
    {
        $hash = $this->generateSimilarityHash($candidateText);

        $query = QuestionBankItem::query();
        if ($trackId) {
            $query->where('track_id', $trackId);
        }

        // 1. Exact similarity hash match
        $exactMatches = (clone $query)->where('duplicate_similarity_hash', $hash)->get();
        if ($exactMatches->isNotEmpty()) {
            return $exactMatches->map(fn($q) => [
                'question' => $q,
                'similarity' => 1.0,
                'match_type' => 'EXACT_HASH',
            ]);
        }

        // 2. Jaccard token scan on track
        $candidates = $query->select('id', 'question_text', 'status', 'track_id', 'topic_id')->limit(200)->get();
        $duplicates = collect();

        foreach ($candidates as $item) {
            $score = $this->calculateSimilarityScore($candidateText, $item->question_text);
            if ($score >= $threshold) {
                $duplicates->push([
                    'question' => $item,
                    'similarity' => $score,
                    'match_type' => 'TOKEN_SIMILARITY',
                ]);
            }
        }

        return $duplicates->sortByDesc('similarity');
    }
}
