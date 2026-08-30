<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\QuizAttempt;
use App\Services\OfflineSyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class QuizSession extends Component
{
    public int $categoryId;
    public int $level;
    public string $mode; // 'practice' or 'ranked'

    public array $questions = [];
    public int $currentIndex = 0;
    public ?string $selectedOption = null;
    public bool $isAnswerSubmitted = false;
    public bool $isCorrect = false;

    // Timers & Scoring
    public int $timeLimit = 15;
    public int $timeRemaining = 15;
    public int $totalScore = 0;
    public int $correctCount = 0;
    public int $currentStreak = 0;
    public int $totalTimeTaken = 0;
    public bool $quizFinished = false;
    public string $attemptUuid;

    public function mount(int $categoryId, int $level = 1, string $mode = 'ranked'): void
    {
        $this->categoryId = $categoryId;
        $this->level = (int) request()->query('level', $level);
        $this->mode = request()->query('mode', $mode);
        $this->attemptUuid = (string) Str::uuid();

        // Level / Mode based timer limits: Practice mode is 30s
        $this->timeLimit = match ($this->mode) {
            'practice' => 30,
            default => match ($this->level) {
                1 => 15, // Junior
                2 => 20, // Youth
                3 => 30, // Advanced
                default => 20,
            },
        };
        $this->timeRemaining = $this->timeLimit;

        $this->loadQuestions();
    }

    public function loadQuestions(): void
    {
        $rawQuestions = Question::where('category_id', $this->categoryId)
            ->where('level', $this->level)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $this->questions = $rawQuestions->map(function ($q) {
            $options = $q->options ?? [];
            $keys = array_keys($options);
            shuffle($keys);
            $shuffledOptions = [];
            foreach ($keys as $key) {
                $shuffledOptions[$key] = $options[$key];
            }

            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $shuffledOptions,
                'correct_option_key' => $q->correct_option_key,
                'explanation' => $q->explanation,
                'reference_citation' => $q->reference_citation,
            ];
        })->toArray();
    }

    public function submitAnswer(?string $selectedKey = null): void
    {
        if ($this->isAnswerSubmitted || $this->quizFinished) {
            return;
        }

        $this->selectedOption = $selectedKey;
        $this->isAnswerSubmitted = true;
        
        if (!isset($this->questions[$this->currentIndex])) {
            return;
        }

        $currentQ = $this->questions[$this->currentIndex];
        $this->isCorrect = ($this->selectedOption === $currentQ['correct_option_key']);

        if ($this->isCorrect) {
            $this->correctCount++;
            $this->currentStreak++;

            $basePoints = 100 * $this->level;
            $speedBonus = 50 * $this->level;
            $streakMultiplier = min(1.0 + ($this->currentStreak * 0.1), 2.5);

            $earnedPoints = ($basePoints + (($this->timeRemaining / $this->timeLimit) * $speedBonus)) * $streakMultiplier;
            $this->totalScore += (int) round($earnedPoints);
        } else {
            $this->currentStreak = 0;
        }

        $this->totalTimeTaken += ($this->timeLimit - $this->timeRemaining);
    }

    public function nextQuestion(): void
    {
        if ($this->currentIndex + 1 < count($this->questions)) {
            $this->currentIndex++;
            $this->selectedOption = null;
            $this->isAnswerSubmitted = false;
            $this->isCorrect = false;
            $this->timeRemaining = $this->timeLimit;
            $this->dispatch('reset-timer', time: $this->timeLimit);
        } else {
            $this->finishQuiz();
        }
    }

    public function finishQuiz(): void
    {
        $this->quizFinished = true;

        if (count($this->questions) > 0 && Auth::check()) {
            $attempt = QuizAttempt::create([
                'id' => $this->attemptUuid,
                'user_id' => Auth::id(),
                'category_id' => $this->categoryId,
                'level' => $this->level,
                'mode' => $this->mode,
                'score' => $this->totalScore,
                'total_questions' => count($this->questions),
                'correct_answers_count' => $this->correctCount,
                'time_taken_seconds' => $this->totalTimeTaken,
                'completed_at' => now(),
                'is_synced' => false,
            ]);

            app(OfflineSyncService::class)->syncAttemptToServer($attempt);
        }
    }

    public function render()
    {
        return view('livewire.quiz-session');
    }
}
