<?php

namespace App\Livewire;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\LearningProgressService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LessonViewer extends Component
{
    public Lesson $lesson;
    public bool $isCompleted = false;
    public bool $isBookmarked = false;
    public bool $showCompletionCelebration = false;
    public int $xpEarned = 0;

    public function mount(Lesson $lesson)
    {
        $this->lesson = $lesson;

        $user = Auth::user();
        if ($user) {
            $progress = LessonProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            $this->isCompleted = $progress?->is_completed ?? false;
            $this->isBookmarked = $progress?->is_bookmarked ?? false;

            // Track reading visit
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['last_read_at' => now()]
            );
        }
    }

    public function toggleBookmark()
    {
        $user = Auth::user();
        if ($user) {
            $this->isBookmarked = app(LearningProgressService::class)->toggleBookmark($user, $this->lesson);
        }
    }

    public function markAsCompleted()
    {
        $user = Auth::user();
        if ($user) {
            $result = app(LearningProgressService::class)->completeLesson($user, $this->lesson);
            $this->isCompleted = true;
            $this->xpEarned = $result['xp_result']['xp_gained'] ?? 20;
            $this->showCompletionCelebration = true;
        }
    }

    public function render()
    {
        $nextLesson = app(LearningProgressService::class)->getNextLesson($this->lesson);

        return view('livewire.lesson-viewer', [
            'nextLesson' => $nextLesson,
        ])->layout('components.layouts.app', ['title' => "{$this->lesson->title} • Catholic Formation"]);
    }
}
