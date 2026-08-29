<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\UserChallengeParticipation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ArenaHub extends Component
{
    public string $activeTab = 'practice'; // 'practice' or 'compete'
    public int $selectedLevel = 1; // 1: Junior, 2: Youth, 3: Advanced
    public string $rallyPin = '';

    public function mount()
    {
        $tab = request()->query('tab');
        if (in_array($tab, ['practice', 'compete'])) {
            $this->activeTab = $tab;
        }
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

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        // Fetch categories with active question count
        $categories = Category::withCount(['questions' => fn($q) => $q->where('is_active', true)])
            ->orderBy('display_order')
            ->get();

        // Today's Daily Challenge status
        $todayChallenge = DailyChallenge::where('challenge_date', now()->toDateString())
            ->where('is_active', true)
            ->first();

        $challengeCompleted = false;
        if ($todayChallenge) {
            $challengeCompleted = UserChallengeParticipation::where('user_id', $user->id)
                ->where('daily_challenge_id', $todayChallenge->id)
                ->exists();
        }

        // Recent performance stats
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
