<?php

namespace App\Livewire;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\MicroLesson;
use App\Services\LearningProgressService;
use App\Services\MicroLearningService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LessonViewer extends Component
{
    public $lesson;
    public bool $isMicroLesson = false;
    public bool $isCompleted = false;
    public bool $isBookmarked = false;
    public bool $showCompletionCelebration = false;
    public int $xpEarned = 0;

    public function mount($lesson)
    {
        if ($lesson instanceof Lesson) {
            $this->lesson = $lesson;
            $this->isMicroLesson = false;
        } elseif ($lesson instanceof MicroLesson) {
            $this->lesson = $lesson;
            $this->isMicroLesson = true;
        } else {
            $foundLesson = Lesson::where('id', $lesson)->orWhere('slug', $lesson)->first();
            if ($foundLesson) {
                $this->lesson = $foundLesson;
                $this->isMicroLesson = false;
            } else {
                $this->lesson = MicroLesson::where('id', $lesson)->orWhere('slug', $lesson)->firstOrFail();
                $this->isMicroLesson = true;
            }
        }

        $user = Auth::user();
        if ($user) {
            if ($this->isMicroLesson && $this->lesson instanceof MicroLesson) {
                $this->isCompleted = app(MicroLearningService::class)->hasUserCompleted($user, $this->lesson);
                $this->isBookmarked = false;
            } elseif ($this->lesson instanceof Lesson) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $this->lesson->id)
                    ->first();

                $this->isCompleted = $progress?->is_completed ?? false;
                $this->isBookmarked = $progress?->is_bookmarked ?? false;

                // Track reading visit
                LessonProgress::updateOrCreate(
                    ['user_id' => $user->id, 'lesson_id' => $this->lesson->id],
                    ['last_read_at' => now()]
                );
            }
        }
    }

    public function toggleBookmark()
    {
        $user = Auth::user();
        if ($user && !$this->isMicroLesson && $this->lesson instanceof Lesson) {
            $this->isBookmarked = app(LearningProgressService::class)->toggleBookmark($user, $this->lesson);
        }
    }

    public function markAsCompleted()
    {
        $user = Auth::user();
        if ($user) {
            if ($this->isMicroLesson && $this->lesson instanceof MicroLesson) {
                $completion = app(MicroLearningService::class)->completeMicroLesson($user, $this->lesson, 3, 3);
                $this->isCompleted = true;
                $this->xpEarned = $completion->xp_earned ?? $this->lesson->xp_reward ?? 40;
                $this->showCompletionCelebration = true;
            } elseif ($this->lesson instanceof Lesson) {
                $result = app(LearningProgressService::class)->completeLesson($user, $this->lesson);
                $this->isCompleted = true;
                $this->xpEarned = $result['xp_result']['xp_gained'] ?? 20;
                $this->showCompletionCelebration = true;
            }
        }
    }

    public function render()
    {
        $nextLesson = $this->isMicroLesson ? Lesson::first() : app(LearningProgressService::class)->getNextLesson($this->lesson);

        return view('livewire.lesson-viewer', [
            'nextLesson' => $nextLesson,
        ])->layout('components.layouts.app', ['title' => "{$this->lesson->title} • Catholic Formation"]);
    }
}
