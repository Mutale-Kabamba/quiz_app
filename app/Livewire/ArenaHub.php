<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\DiocesanCompetition;
use App\Models\Parish;
use App\Models\ParishCompetition;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\UserChallengeParticipation;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ArenaHub extends Component
{
    public string $activeTab = 'practice'; // 'practice' or 'compete' for youth, 'bank' / 'rallies' for super admin, 'quizzes' / 'host' for parish admin
    public int $selectedLevel = 1; // 1: Junior, 2: Youth, 3: Advanced
    public string $rallyPin = '';
    public string $searchQuestion = '';
    public ?int $selectedCategoryFilter = null;

    // Super Admin: Create Competition
    public bool $showDiocesanCompModal = false;
    public string $newCompTitle = '';
    public string $newCompDescription = '';
    public ?int $newCompCategoryId = null;
    public string $newCompStartTime = '';
    public string $newCompEndTime = '';
    public int $newCompTimeLimit = 300;

    // Parish Admin: Host Quiz
    public bool $showParishQuizModal = false;
    public string $newParishQuizTitle = '';
    public string $newParishQuizDescription = '';
    public ?int $newParishQuizCategoryId = null;
    public string $newParishQuizStartTime = '';
    public string $newParishQuizEndTime = '';
    public int $newParishQuizTimeLimit = 300;

    public ?string $successMessage = null;

    public function mount()
    {
        $tab = request()->query('tab');
        if ($tab) {
            $this->activeTab = $tab;
        } else {
            $user = Auth::user();
            if ($user?->isSuperAdmin()) {
                $this->activeTab = 'bank';
            } elseif ($user?->isChairperson()) {
                $this->activeTab = 'quizzes';
            } else {
                $this->activeTab = 'practice';
            }
        }

        $this->newCompStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newCompEndTime = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->newParishQuizStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newParishQuizEndTime = now()->addDays(5)->format('Y-m-d\TH:i');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function setLevel(int $level)
    {
        $this->selectedLevel = $level;
    }

    public function joinRally()
    {
        $this->validate([
            'rallyPin' => 'required|numeric|digits:6',
        ]);

        return redirect()->to('/quiz/play?mode=ranked&rally=' . $this->rallyPin);
    }

    public function toggleQuestionStatus(string $questionId)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $question = Question::findOrFail($questionId);
        $newStatus = !$question->is_active;
        $question->update(['is_active' => $newStatus]);

        app(AuditLogService::class)->log(
            'question_status_toggled',
            $question,
            ['is_active' => !$newStatus],
            ['is_active' => $newStatus],
            $user
        );

        $this->successMessage = "Question status updated to " . ($newStatus ? 'Active' : 'Inactive');
    }

    public function createDiocesanCompetition()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'newCompTitle' => 'required|string|min:4|max:120',
            'newCompDescription' => 'required|string|min:10',
            'newCompStartTime' => 'required|date',
            'newCompEndTime' => 'required|date|after:newCompStartTime',
        ]);

        $competition = DiocesanCompetition::create([
            'created_by' => $user->id,
            'title' => $this->newCompTitle,
            'description' => $this->newCompDescription,
            'competition_type' => 'diocesan',
            'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
            'rally_pin' => (string) random_int(100000, 999999),
            'level' => 2,
            'time_limit_seconds' => $this->newCompTimeLimit,
            'question_count' => 15,
            'status' => 'active',
            'start_time' => $this->newCompStartTime,
            'end_time' => $this->newCompEndTime,
        ]);

        app(AuditLogService::class)->log(
            'diocesan_competition_created',
            $competition,
            null,
            ['title' => $this->newCompTitle],
            $user
        );

        $this->reset(['newCompTitle', 'newCompDescription', 'showDiocesanCompModal']);
        $this->successMessage = "Diocesan Rally '{$competition->title}' (PIN: {$competition->rally_pin}) scheduled successfully!";
    }

    public function createParishQuiz()
    {
        $user = Auth::user();
        if (!$user->isChairperson()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'newParishQuizTitle' => 'required|string|min:4|max:120',
            'newParishQuizDescription' => 'required|string|min:10',
            'newParishQuizStartTime' => 'required|date',
            'newParishQuizEndTime' => 'required|date|after:newParishQuizStartTime',
        ]);

        $comp = ParishCompetition::create([
            'parish_id' => $user->parish_id,
            'created_by' => $user->id,
            'title' => $this->newParishQuizTitle,
            'description' => $this->newParishQuizDescription,
            'rally_pin' => (string) random_int(100000, 999999),
            'category_id' => $this->newParishQuizCategoryId ?: Category::first()?->id,
            'level' => 1,
            'time_limit_seconds' => $this->newParishQuizTimeLimit,
            'question_count' => 10,
            'status' => 'active',
            'start_time' => $this->newParishQuizStartTime,
            'end_time' => $this->newParishQuizEndTime,
        ]);

        app(AuditLogService::class)->log(
            'parish_competition_created',
            $comp,
            null,
            ['title' => $this->newParishQuizTitle],
            $user
        );

        $this->reset(['newParishQuizTitle', 'newParishQuizDescription', 'showParishQuizModal']);
        $this->successMessage = "Parish Quiz '{$comp->title}' (PIN: {$comp->rally_pin}) scheduled successfully!";
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $categories = Category::withCount(['questions' => fn($q) => $q->where('is_active', true)])
            ->orderBy('display_order')
            ->get();

        // =========================================================================
        // A. SUPER ADMIN QUESTION BANK & COMPETITIONS HUB
        // =========================================================================
        if ($user->isSuperAdmin()) {
            $questions = Question::with('category')
                ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
                ->when($this->searchQuestion, fn($q) => $q->where('question_text', 'like', "%{$this->searchQuestion}%"))
                ->latest()
                ->paginate(15);

            $diocesanCompetitions = DiocesanCompetition::with('category')->latest()->get();

            return view('livewire.arena-hub', [
                'user' => $user,
                'categories' => $categories,
                'questions' => $questions,
                'diocesanCompetitions' => $diocesanCompetitions,
            ])->layout('components.layouts.app', ['title' => 'Question Bank & Competitions • Diocese of Livingstone']);
        }

        // =========================================================================
        // B. PARISH ADMIN (CHAIRPERSON) PARISH QUIZ MANAGEMENT
        // =========================================================================
        if ($user->isChairperson()) {
            $parish = $user->parish ?? Parish::first();
            $parishCompetitions = ParishCompetition::where('parish_id', $parish->id)->with('category')->latest()->get();
            $totalAttempts = QuizAttempt::whereHas('user', fn($q) => $q->where('parish_id', $parish->id))->count();

            return view('livewire.arena-hub', [
                'user' => $user,
                'parish' => $parish,
                'categories' => $categories,
                'parishCompetitions' => $parishCompetitions,
                'totalAttempts' => $totalAttempts,
            ])->layout('components.layouts.app', ['title' => "Parish Quizzes & Arena • {$parish->name}"]);
        }

        // =========================================================================
        // C. YOUTH PRACTICE & COMPETE ARENA
        // =========================================================================
        $todayChallenge = DailyChallenge::where('challenge_date', now()->toDateString())
            ->where('is_active', true)
            ->first();

        $challengeCompleted = false;
        if ($todayChallenge) {
            $challengeCompleted = UserChallengeParticipation::where('user_id', $user->id)
                ->where('daily_challenge_id', $todayChallenge->id)
                ->exists();
        }

        $rankedAttemptsCount = QuizAttempt::where('user_id', $user->id)->where('mode', 'ranked')->count();
        $practiceAttemptsCount = QuizAttempt::where('user_id', $user->id)->where('mode', 'practice')->count();

        return view('livewire.arena-hub', [
            'user' => $user,
            'categories' => $categories,
            'todayChallenge' => $todayChallenge,
            'challengeCompleted' => $challengeCompleted,
            'rankedAttemptsCount' => $rankedAttemptsCount,
            'practiceAttemptsCount' => $practiceAttemptsCount,
        ])->layout('components.layouts.app', ['title' => 'Formation Arena • Diocese of Livingstone']);
    }
}
