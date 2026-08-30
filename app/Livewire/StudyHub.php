<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Parish;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\FlashcardService;
use App\Services\LearningProgressService;
use App\Services\ParishDashboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudyHub extends Component
{
    public int|string|null $selectedCategoryId = null;
    public string $search = '';
    public string $adminTab = 'tracks'; // 'tracks', 'lessons', 'gaps'

    // Add Category Form (Super Admin)
    public bool $showCategoryModal = false;
    public string $newCatName = '';
    public string $newCatCode = '';
    public string $newCatDescription = '';
    public string $newCatIcon = 'book-open';

    public ?string $successMessage = null;

    public bool $showAllSeries = false;

    public function mount(mixed $selectedCategoryId = null)
    {
        if ($selectedCategoryId !== null && $selectedCategoryId !== '') {
            if (is_numeric($selectedCategoryId)) {
                $this->selectedCategoryId = (int) $selectedCategoryId;
            } else {
                // Check if it's a category slug/code
                $cat = Category::where('slug', $selectedCategoryId)
                    ->orWhere('code', strtoupper($selectedCategoryId))
                    ->first();

                if ($cat) {
                    $this->selectedCategoryId = $cat->id;
                } else {
                    // Check if it's a lesson ID or slug and redirect to lesson viewer
                    $lesson = Lesson::where('id', $selectedCategoryId)
                        ->orWhere('slug', $selectedCategoryId)
                        ->first();

                    if ($lesson) {
                        return redirect()->route('lesson.show', $lesson->id);
                    }

                    $this->selectedCategoryId = null;
                }
            }
        }
    }

    public function selectCategory(mixed $id)
    {
        $this->selectedCategoryId = $id ? (int) $id : null;
    }

    public function setAdminTab(string $tab)
    {
        $this->adminTab = $tab;
    }

    public function toggleShowAllSeries()
    {
        $this->showAllSeries = !$this->showAllSeries;
    }

    public function toggleLessonStatus(string $lessonId)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $lesson = Lesson::findOrFail($lessonId);
        $newStatus = $lesson->status === 'published' ? 'draft' : 'published';
        $lesson->update(['status' => $newStatus]);

        app(AuditLogService::class)->log(
            'lesson_status_toggled',
            $lesson,
            ['status' => $lesson->getOriginal('status')],
            ['status' => $newStatus],
            $user
        );

        $this->successMessage = "Lesson '{$lesson->title}' status changed to {$newStatus}.";
    }

    public function createCategory()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'newCatName' => 'required|string|min:3|max:100',
            'newCatCode' => 'required|string|min:2|max:20|unique:categories,code',
            'newCatDescription' => 'nullable|string',
        ]);

        $cat = Category::create([
            'name' => $this->newCatName,
            'code' => strtoupper($this->newCatCode),
            'description' => $this->newCatDescription,
            'icon' => $this->newCatIcon,
            'display_order' => Category::count() + 1,
            'is_active' => true,
        ]);

        app(AuditLogService::class)->log(
            'category_created',
            $cat,
            null,
            ['name' => $this->newCatName, 'code' => $this->newCatCode],
            $user
        );

        $this->reset(['newCatName', 'newCatCode', 'newCatDescription', 'showCategoryModal']);
        $this->successMessage = "Curriculum Track '{$cat->name}' registered successfully.";
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        // =========================================================================
        // A. SUPER ADMIN STUDY & CURRICULUM MANAGEMENT
        // =========================================================================
        if ($user->isSuperAdmin()) {
            $categories = Category::withCount(['lessons', 'questions'])
                ->orderBy('display_order')
                ->get();

            $lessons = Lesson::with('category')
                ->when($this->selectedCategoryId, fn($q) => $q->where('category_id', $this->selectedCategoryId))
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->orderBy('display_order')
                ->paginate(15);

            return view('livewire.study-hub', [
                'user' => $user,
                'categories' => $categories,
                'lessons' => $lessons,
            ])->layout('components.layouts.app', ['title' => 'Curriculum Studio • Diocese of Livingstone']);
        }

        // =========================================================================
        // B. PARISH ADMIN (CHAIRPERSON) FORMATION OVERSIGHT
        // =========================================================================
        if ($user->isChairperson()) {
            $parish = $user->parish ?? Parish::first();
            $categories = Category::withCount('questions')->get();
            $formationHealth = app(ParishDashboardService::class)->getFormationHealth($parish->id);

            $lessons = Lesson::where('status', 'published')
                ->with('category')
                ->withCount(['progress as completions_count' => fn($q) => $q->where('is_completed', true)->whereHas('user', fn($uq) => $uq->where('parish_id', $parish->id))])
                ->when($this->selectedCategoryId, fn($q) => $q->where('category_id', $this->selectedCategoryId))
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->paginate(15);

            return view('livewire.study-hub', [
                'user' => $user,
                'parish' => $parish,
                'categories' => $categories,
                'formationHealth' => $formationHealth,
                'lessons' => $lessons,
            ])->layout('components.layouts.app', ['title' => "Parish Study Formation • {$parish->name}"]);
        }

        // =========================================================================
        // C. YOUTH LEARNER FORMATION STUDY HUB (GROUPED BY SERIES)
        // =========================================================================
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

        $allLessons = $lessonsQuery->get();
        $flashcardStats = app(FlashcardService::class)->getUserStats($user);
        $categoryProgress = app(LearningProgressService::class)->getCategoryProgress($user);

        // Group lessons into Series
        $seriesCollection = collect();
        $grouped = $allLessons->groupBy(function (Lesson $lesson) {
            if (!empty($lesson->series_identifier)) {
                return $lesson->series_identifier;
            }
            if (preg_match('/^(.*?)(?:\s*\((?:Part|Lesson)\s*\d+\)|\s+(?:Part|Lesson)\s*\d+)/i', $lesson->title, $matches)) {
                return trim($matches[1]);
            }
            return 'standalone_' . $lesson->id;
        });

        foreach ($grouped as $key => $groupLessons) {
            $firstLesson = $groupLessons->first();
            $category = $firstLesson->category;

            // Clean series name
            if (preg_match('/^(.*?)(?:\s*\((?:Part|Lesson)\s*\d+\)|\s+(?:Part|Lesson)\s*\d+)/i', $firstLesson->title, $matches)) {
                $seriesName = trim($matches[1]);
            } elseif (!empty($firstLesson->series_identifier) && !str_starts_with($key, 'standalone_')) {
                $seriesName = ucwords(str_replace(['_', '-'], ' ', $firstLesson->series_identifier));
            } else {
                $seriesName = $firstLesson->title;
            }

            $totalReadMinutes = $groupLessons->sum('estimated_read_minutes') ?: ($groupLessons->count() * 5);
            $completedCount = $groupLessons->filter(fn($l) => $l->progress->first()?->is_completed)->count();
            $totalCount = $groupLessons->count();

            $seriesCollection->push([
                'key' => md5($key . '_' . ($category?->id ?? 0)),
                'series_identifier' => $firstLesson->series_identifier ?? $key,
                'name' => $seriesName,
                'category' => $category,
                'category_name' => $category?->name ?? 'Faith Formation',
                'lessons_count' => $totalCount,
                'total_read_minutes' => $totalReadMinutes,
                'completed_count' => $completedCount,
                'is_completed' => ($completedCount === $totalCount && $totalCount > 0),
                'in_progress' => ($completedCount > 0 && $completedCount < $totalCount),
                'lessons' => $groupLessons->sortBy('display_order')->values(),
                'summary' => $firstLesson->summary ?? $firstLesson->subheading,
                'citation' => $firstLesson->catechism_citations ?? $firstLesson->scripture_citations,
            ]);
        }

        $totalSeriesCount = $seriesCollection->count();
        $displayedSeries = $this->showAllSeries ? $seriesCollection : $seriesCollection->take(5);

        return view('livewire.study-hub', [
            'user' => $user,
            'categories' => $categories,
            'lessons' => $allLessons,
            'seriesList' => $displayedSeries,
            'totalSeriesCount' => $totalSeriesCount,
            'flashcardStats' => $flashcardStats,
            'categoryProgress' => $categoryProgress,
        ])->layout('components.layouts.app', ['title' => 'Catechetical Study Hub • Diocese of Livingstone']);
    }
}
