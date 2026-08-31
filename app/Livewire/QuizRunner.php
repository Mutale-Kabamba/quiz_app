<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\UserChallengeParticipation;
use App\Services\GamificationService;
use App\Services\LearningIntelligenceService;
use App\Services\OfflineSyncService;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class QuizRunner extends Component
{
    public ?int $categoryId = null;
    public int $level = 1;
    public string $mode = 'ranked'; // 'practice' or 'ranked'
    public ?string $challenge = null; // 'today'
    public ?string $competitionId = null;
    public ?string $accessCode = null;

    public array $questions = [];
    public array $submittedAnswers = []; // [ ['question_id' => ..., 'category_id' => ..., 'selected' => ..., 'is_correct' => ...] ]
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
    public int $xpEarned = 0;

    // Smart Analysis for Results
    public array $masteredTopics = [];
    public array $weakTopics = [];

    // Question Dispute / Flagging Modal
    public bool $showReportModal = false;
    public string $reportType = 'wrong_answer';
    public string $reportNotes = '';
    public bool $reportSubmitted = false;

    public function mount(?int $categoryId = null, int $level = 1, string $mode = 'ranked', ?string $challenge = null)
    {
        $this->categoryId = $categoryId;
        $this->level = (int) request()->query('level', $level);
        $this->mode = request()->query('mode', $mode);
        $this->challenge = request()->query('challenge', $challenge);
        $this->competitionId = request()->query('competition');
        $this->accessCode = request()->query('code');
        $this->attemptUuid = (string) Str::uuid();

        // If this is a competition / rally quiz, enforce server-side access control
        if ($this->competitionId) {
            $rally = \App\Models\DiocesanCompetition::find($this->competitionId);
            if (!$rally) {
                session()->flash('error', 'Rally / Competition event not found.');
                return redirect()->route('arena.hub');
            }

            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $validation = app(\App\Services\RallyAccessService::class)->validateRallyAccess(
                $rally,
                $user,
                $this->accessCode
            );

            if (!$validation['allowed']) {
                session()->flash('error', $validation['message']);
                return redirect()->route('arena.hub');
            }

            // Set competition category, level, and timer
            if ($rally->category_id) {
                $this->categoryId = $rally->category_id;
            }
            $this->level = $rally->level ?? $this->level;
            $this->timeLimit = $rally->time_limit_seconds ?: 15;
            $this->timeRemaining = $this->timeLimit;
            $this->loadQuestions($rally->question_count ?: 15);
            return;
        }

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

    public function loadQuestions(int $count = 10): void
    {
        // 1. Check if loading Daily Challenge
        if ($this->challenge === 'today') {
            $dailyChallenge = DailyChallenge::where('challenge_date', now()->toDateString())->first();
            if ($dailyChallenge && !empty($dailyChallenge->question_ids)) {
                $rawQuestions = Question::whereIn('id', $dailyChallenge->question_ids)->get();
                $this->formatAndSetQuestions($rawQuestions);
                return;
            }
        }

        // 2. Standard Category / Level Query
        $query = Question::where('is_active', true);

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->level) {
            $query->where('level', $this->level);
        }

        $rawQuestions = $query->inRandomOrder()->limit($count)->get();

        if ($rawQuestions->isEmpty()) {
            $rawQuestions = Question::where('is_active', true)->inRandomOrder()->limit($count)->get();
        }

        $this->formatAndSetQuestions($rawQuestions);
    }

    protected function formatAndSetQuestions($rawQuestions): void
    {
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
                'category_id' => $q->category_id,
                'category_name' => $q->category?->name ?? 'General Doctrine',
                'level' => $q->level,
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

        $this->submittedAnswers[] = [
            'question_id' => $currentQ['id'],
            'category_id' => $currentQ['category_id'],
            'category_name' => $currentQ['category_name'],
            'selected_option_key' => $this->selectedOption,
            'is_correct' => $this->isCorrect,
        ];

        if ($this->isCorrect) {
            $this->correctCount++;
            $this->currentStreak++;

            // Scoring Calculation Formula
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
            $user = Auth::user();

            // 1. Create Quiz Attempt
            $attempt = QuizAttempt::create([
                'id' => $this->attemptUuid,
                'user_id' => $user->id,
                'category_id' => $this->categoryId ?? $this->questions[0]['category_id'] ?? 1,
                'level' => $this->level,
                'mode' => $this->mode,
                'score' => $this->totalScore,
                'total_questions' => count($this->questions),
                'correct_answers_count' => $this->correctCount,
                'time_taken_seconds' => $this->totalTimeTaken,
                'completed_at' => now(),
                'is_synced' => false,
            ]);

            // 2. Record Detailed Answers for Learning Intelligence
            foreach ($this->submittedAnswers as $ans) {
                QuizAttemptAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $ans['question_id'],
                    'category_id' => $ans['category_id'],
                    'selected_option_key' => $ans['selected_option_key'],
                    'is_correct' => $ans['is_correct'],
                ]);
            }

            // 3. Handle Daily Challenge Completion
            if ($this->challenge === 'today') {
                $dailyChallenge = DailyChallenge::where('challenge_date', now()->toDateString())->first();
                if ($dailyChallenge) {
                    UserChallengeParticipation::firstOrCreate(
                        ['user_id' => $user->id, 'daily_challenge_id' => $dailyChallenge->id],
                        [
                            'score' => $this->totalScore,
                            'xp_earned' => $dailyChallenge->xp_reward ?? 50,
                            'completed_at' => now(),
                        ]
                    );
                }
            }

            // 4. Handle Rally / Competition Completion (1-Try Rule)
            if ($this->competitionId) {
                \App\Models\RallyParticipant::updateOrCreate(
                    [
                        'rally_id' => $this->competitionId,
                        'user_id' => $user->id,
                    ],
                    [
                        'status' => 'completed',
                        'score' => $this->totalScore,
                        'completed_at' => now(),
                        'metadata' => [
                            'quiz_attempt_id' => (string) $attempt->id,
                            'correct_count' => $this->correctCount,
                            'total_questions' => count($this->questions),
                            'time_taken' => $this->totalTimeTaken,
                        ],
                    ]
                );
            }

            // 5. Compute XP & Update Formation Streak
            $xpToAward = match (true) {
                $this->challenge === 'today' => 50,
                $this->mode === 'ranked' => (int) max(10, round($this->totalScore / 15)),
                default => 10, // Practice mode
            };

            $xpResult = app(GamificationService::class)->awardXp(
                $user,
                $xpToAward,
                "Completed Quiz Attempt",
                'quiz_attempt',
                (string) $attempt->id
            );
            app(StreakService::class)->recordFormationActivity($user);
            $this->xpEarned = $xpResult['xp_gained'] ?? $xpToAward;

            // 5. Compute Mastered vs Weak topics for Results Screen
            $intelligence = app(LearningIntelligenceService::class)->analyzePerformance($user);
            $this->masteredTopics = $intelligence['mastered'];
            $this->weakTopics = $intelligence['weak'];

            // 6. Push sync to server
            app(OfflineSyncService::class)->syncAttemptToServer($attempt);
        }
    }

    public function submitReport(): void
    {
        if (!Auth::check() || !isset($this->questions[$this->currentIndex])) {
            return;
        }

        QuestionReport::create([
            'question_id' => $this->questions[$this->currentIndex]['id'],
            'user_id' => Auth::id(),
            'issue_type' => $this->reportType,
            'notes' => $this->reportNotes,
            'status' => 'pending',
        ]);

        $this->reportSubmitted = true;
    }

    public function render()
    {
        return view('livewire.quiz-runner')->layout('components.layouts.app', ['title' => 'Formation Arena • Livingstone Diocese']);
    }
}
