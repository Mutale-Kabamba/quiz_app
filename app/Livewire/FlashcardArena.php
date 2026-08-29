<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Flashcard;
use App\Services\FlashcardService;
use App\Services\GamificationService;
use App\Services\StreakService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FlashcardArena extends Component
{
    public ?int $categoryId = null;
    public ?Category $category = null;

    public array $cards = [];
    public int $currentIndex = 0;
    public bool $isFlipped = false;
    public bool $sessionCompleted = false;

    public int $againCount = 0;
    public int $goodCount = 0;
    public int $easyCount = 0;
    public int $xpEarned = 0;

    public function mount(?int $categoryId = null)
    {
        $this->categoryId = $categoryId;
        if ($categoryId) {
            $this->category = Category::find($categoryId);
        }

        $this->loadCards();
    }

    public function loadCards()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $flashcardService = app(FlashcardService::class);
        $dueCards = $flashcardService->getDueFlashcards($user, $this->categoryId, 10);

        if ($dueCards->isEmpty()) {
            // Fallback to any published cards in category or system
            $query = Flashcard::where('status', 'published');
            if ($this->categoryId) {
                $query->where('category_id', $this->categoryId);
            }
            $dueCards = $query->inRandomOrder()->limit(10)->get();
        }

        $this->cards = $dueCards->toArray();
        $this->currentIndex = 0;
        $this->isFlipped = false;
        $this->sessionCompleted = false;
    }

    public function flipCard()
    {
        $this->isFlipped = !$this->isFlipped;
    }

    public function rateCard(int $rating)
    {
        $user = Auth::user();
        if (!$user || !isset($this->cards[$this->currentIndex])) {
            return;
        }

        $currentCard = $this->cards[$this->currentIndex];
        app(FlashcardService::class)->recordReview($user, $currentCard['id'], $rating);

        if ($rating === 1) {
            $this->againCount++;
        } elseif ($rating === 2) {
            $this->goodCount++;
        } else {
            $this->easyCount++;
        }

        // Advance to next card or complete
        if ($this->currentIndex + 1 < count($this->cards)) {
            $this->currentIndex++;
            $this->isFlipped = false;
        } else {
            $this->finishSession();
        }
    }

    public function finishSession()
    {
        $user = Auth::user();
        if ($user) {
            $xpResult = app(GamificationService::class)->awardXp($user, 15, "Completed Flashcards Spaced Review");
            app(StreakService::class)->recordFormationActivity($user);
            $this->xpEarned = $xpResult['xp_gained'] ?? 15;
        }

        $this->sessionCompleted = true;
    }

    public function restartSession()
    {
        $this->againCount = 0;
        $this->goodCount = 0;
        $this->easyCount = 0;
        $this->loadCards();
    }

    public function render()
    {
        $currentCard = $this->cards[$this->currentIndex] ?? null;

        return view('livewire.flashcard-arena', [
            'currentCard' => $currentCard,
            'totalCards' => count($this->cards),
        ])->layout('components.layouts.app', ['title' => 'Flashcard Arena • Livingstone Diocese']);
    }
}
