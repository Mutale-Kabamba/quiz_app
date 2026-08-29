<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Lesson;
use App\Services\FlashcardService;
use App\Services\LearningProgressService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudyHub extends Component
{
    public ?int $selectedCategoryId = null;
    public string $search = '';

    public function mount(?int $selectedCategoryId = null)
    {
        $this->selectedCategoryId = $selectedCategoryId;
    }

    public function selectCategory(?int $id)
    {
        $this->selectedCategoryId = $id;
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $categories = Category::withCount(['lessons' => fn($q) => $q->where('status', 'published')])
            ->orderBy('display_order')
            ->get();

        $lessonsQuery = Lesson::where('status', 'published')
            ->with(['category', 'progress' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('display_order');

        if ($this->selectedCategoryId) {
            $lessonsQuery->where('category_id', $this->selectedCategoryId);
        }

        if (!empty($this->search)) {
            $lessonsQuery->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('subheading', 'like', "%{$this->search}%")
                  ->orWhere('scripture_citations', 'like', "%{$this->search}%")
                  ->orWhere('catechism_citations', 'like', "%{$this->search}%");
            });
        }

        $lessons = $lessonsQuery->get();

        $flashcardStats = app(FlashcardService::class)->getUserStats($user);
        $categoryProgress = app(LearningProgressService::class)->getCategoryProgress($user);

        return view('livewire.study-hub', [
            'categories' => $categories,
            'lessons' => $lessons,
            'flashcardStats' => $flashcardStats,
            'categoryProgress' => $categoryProgress,
        ])->layout('components.layouts.app', ['title' => 'Catechetical Study Hub • Diocese of Livingstone']);
    }
}
