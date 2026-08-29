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
use App\Models\TaxonomyTrack;
use App\Models\UserChallengeParticipation;
use App\Services\AuditLogService;
use App\Services\DynamicContentImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ArenaHub extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $activeTab = 'practice'; // 'practice' or 'compete' for youth, 'bank' / 'rallies' for super admin, 'quizzes' / 'host' for parish admin
    public int $selectedLevel = 1; // 1: Junior, 2: Youth, 3: Advanced
    public string $rallyPin = '';
    public string $searchQuestion = '';
    public ?int $selectedCategoryFilter = null;
    public ?int $selectedLevelFilter = null;

    // Super Admin: Question CRUD (Q&A)
    public bool $showQuestionModal = false;
    public ?string $editQuestionId = null;
    public ?int $newQuestionCategoryId = null;
    public int $newQuestionLevel = 1;
    public string $newQuestionText = '';
    public string $optionA = '';
    public string $optionB = '';
    public string $optionC = '';
    public string $optionD = '';
    public string $correctOption = 'A';
    public string $newQuestionCitation = '';
    public string $newQuestionExplanation = '';

    // Super Admin: Dynamic Import Modal
    public bool $showImportModal = false;
    public $importFile = null;
    public ?int $importTrackId = null;
    public string $importDuplicateStrategy = 'skip';
    public ?array $importResults = null;
    public bool $isImporting = false;

    // Super Admin: Create & Edit Competition
    public bool $showDiocesanCompModal = false;
    public ?string $editCompId = null;
    public string $newCompTitle = '';
    public string $newCompDescription = '';
    public ?int $newCompCategoryId = null;
    public string $newCompStartTime = '';
    public string $newCompEndTime = '';
    public int $newCompTimeLimit = 300;
    public int $newCompQuestionCount = 15;

    // Parish Admin: Host Quiz
    public bool $showParishQuizModal = false;
    public string $newParishQuizTitle = '';
    public string $newParishQuizDescription = '';
    public ?int $newParishQuizCategoryId = null;
    public string $newParishQuizStartTime = '';
    public string $newParishQuizEndTime = '';
    public int $newParishQuizTimeLimit = 300;

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

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
        $this->resetPage();
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

    // =========================================================================
    // 1. SUPER ADMIN QUESTION CRUD (Q&A)
    // =========================================================================
    public function openCreateQuestionModal()
    {
        $this->reset(['editQuestionId', 'newQuestionCategoryId', 'newQuestionText', 'optionA', 'optionB', 'optionC', 'optionD', 'correctOption', 'newQuestionExplanation', 'newQuestionCitation', 'newQuestionLevel']);
        $this->showQuestionModal = true;
    }

    public function editQuestion(string $id)
    {
        $question = Question::findOrFail($id);
        $this->editQuestionId = $question->id;
        $this->newQuestionCategoryId = $question->category_id;
        $this->newQuestionText = $question->question_text;
        $this->newQuestionLevel = $question->level ?? 1;
        $this->newQuestionExplanation = $question->explanation ?? '';
        $this->newQuestionCitation = $question->reference_citation ?? '';
        $this->correctOption = $question->correct_option_key ?? 'A';

        $opts = (array) ($question->options ?? []);
        $this->optionA = $opts['A'] ?? '';
        $this->optionB = $opts['B'] ?? '';
        $this->optionC = $opts['C'] ?? '';
        $this->optionD = $opts['D'] ?? '';

        $this->showQuestionModal = true;
    }

    public function saveQuestion()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'newQuestionCategoryId' => 'required|exists:categories,id',
            'newQuestionText' => 'required|string|min:8',
            'optionA' => 'required|string',
            'optionB' => 'required|string',
            'optionC' => 'required|string',
            'optionD' => 'required|string',
            'correctOption' => 'required|in:A,B,C,D',
            'newQuestionExplanation' => 'required|string|min:8',
            'newQuestionCitation' => 'nullable|string',
        ]);

        $optionsMap = [
            'A' => $this->optionA,
            'B' => $this->optionB,
            'C' => $this->optionC,
            'D' => $this->optionD,
        ];

        if ($this->editQuestionId) {
            $question = Question::findOrFail($this->editQuestionId);
            $question->update([
                'category_id' => $this->newQuestionCategoryId,
                'level' => $this->newQuestionLevel,
                'question_text' => $this->newQuestionText,
                'options' => $optionsMap,
                'correct_option_key' => $this->correctOption,
                'explanation' => $this->newQuestionExplanation,
                'reference_citation' => $this->newQuestionCitation ?: 'CCC & Scripture',
            ]);

            app(AuditLogService::class)->log(
                'question_updated',
                $question,
                null,
                ['question_text' => $this->newQuestionText],
                $user
            );

            $this->successMessage = 'Question updated successfully!';
        } else {
            $question = Question::create([
                'category_id' => $this->newQuestionCategoryId,
                'level' => $this->newQuestionLevel,
                'question_text' => $this->newQuestionText,
                'options' => $optionsMap,
                'correct_option_key' => $this->correctOption,
                'explanation' => $this->newQuestionExplanation,
                'reference_citation' => $this->newQuestionCitation ?: 'CCC & Scripture',
                'is_active' => true,
            ]);

            app(AuditLogService::class)->log(
                'question_created',
                $question,
                null,
                ['question_text' => $this->newQuestionText],
                $user
            );

            $this->successMessage = 'New question successfully added to question bank!';
        }

        $this->reset(['editQuestionId', 'newQuestionCategoryId', 'newQuestionText', 'optionA', 'optionB', 'optionC', 'optionD', 'newQuestionExplanation', 'newQuestionCitation', 'showQuestionModal']);
    }

    public function deleteQuestion(string $id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $question = Question::findOrFail($id);
        $question->delete();

        app(AuditLogService::class)->log(
            'question_deleted',
            $question,
            ['id' => $id],
            null,
            $user
        );

        $this->successMessage = 'Question deleted from bank.';
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

    // =========================================================================
    // 2. DYNAMIC CONTENT IMPORT
    // =========================================================================
    public function openImportModal()
    {
        $this->reset(['importFile', 'importResults', 'importTrackId']);
        $this->importDuplicateStrategy = 'skip';
        $this->showImportModal = true;
    }

    public function downloadSampleTemplate(string $format = 'csv')
    {
        $importService = app(DynamicContentImportService::class);
        $template = $importService->generateSampleTemplate($format);

        return response()->streamDownload(function () use ($template) {
            echo $template['content'];
        }, $template['filename'], [
            'Content-Type' => $template['mime'],
        ]);
    }

    public function processDynamicImport(DynamicContentImportService $importService)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'importFile' => 'required|file|max:10240',
            'importDuplicateStrategy' => 'required|in:skip,overwrite,error',
        ]);

        $this->isImporting = true;

        try {
            $path = $this->importFile->getRealPath();
            $extension = strtolower($this->importFile->getClientOriginalExtension());
            $content = file_get_contents($path);

            $this->importResults = $importService->importFromFileContent(
                $content,
                $extension,
                $this->importTrackId,
                $this->importDuplicateStrategy,
                $user
            );

            $this->successMessage = "Import completed! {$this->importResults['successful']} questions imported successfully.";
        } catch (\Exception $e) {
            $this->errorMessage = 'Import failed: ' . $e->getMessage();
        } finally {
            $this->isImporting = false;
        }
    }

    // =========================================================================
    // 3. DIOCESAN COMPETITIONS / RALLIES CRUD
    // =========================================================================
    public function openCreateCompetitionModal()
    {
        $this->reset(['editCompId', 'newCompTitle', 'newCompDescription', 'newCompCategoryId']);
        $this->newCompTimeLimit = 300;
        $this->newCompQuestionCount = 15;
        $this->newCompStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newCompEndTime = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->showDiocesanCompModal = true;
    }

    public function editCompetition(string $id)
    {
        $comp = DiocesanCompetition::findOrFail($id);
        $this->editCompId = $comp->id;
        $this->newCompTitle = $comp->title;
        $this->newCompDescription = $comp->description ?? '';
        $this->newCompCategoryId = $comp->category_id;
        $this->newCompTimeLimit = $comp->time_limit_seconds;
        $this->newCompQuestionCount = $comp->question_count ?? 15;
        $this->newCompStartTime = $comp->start_time ? $comp->start_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        $this->newCompEndTime = $comp->end_time ? $comp->end_time->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i');
        $this->showDiocesanCompModal = true;
    }

    public function saveCompetition()
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
            'newCompTimeLimit' => 'required|integer|min:30|max:3600',
            'newCompQuestionCount' => 'required|integer|min:5|max:100',
        ]);

        if ($this->editCompId) {
            $competition = DiocesanCompetition::findOrFail($this->editCompId);
            $competition->update([
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
                'start_time' => $this->newCompStartTime,
                'end_time' => $this->newCompEndTime,
            ]);

            app(AuditLogService::class)->log(
                'diocesan_competition_updated',
                $competition,
                null,
                ['title' => $this->newCompTitle],
                $user
            );

            $this->successMessage = "Diocesan Rally '{$competition->title}' updated successfully!";
        } else {
            $competition = DiocesanCompetition::create([
                'created_by' => $user->id,
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'competition_type' => 'diocesan',
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'rally_pin' => (string) random_int(100000, 999999),
                'level' => 2,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
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

            $this->successMessage = "Diocesan Rally '{$competition->title}' (PIN: {$competition->rally_pin}) scheduled successfully!";
        }

        $this->reset(['editCompId', 'newCompTitle', 'newCompDescription', 'showDiocesanCompModal']);
    }

    public function deleteCompetition(string $id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $comp = DiocesanCompetition::findOrFail($id);
        $comp->delete();

        app(AuditLogService::class)->log(
            'diocesan_competition_deleted',
            $comp,
            ['title' => $comp->title],
            null,
            $user
        );

        $this->successMessage = 'Diocesan rally deleted.';
    }

    public function toggleCompetitionStatus(string $id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $comp = DiocesanCompetition::findOrFail($id);
        $newStatus = $comp->status === 'active' ? 'concluded' : 'active';
        $comp->update(['status' => $newStatus]);

        $this->successMessage = "Rally status changed to '{$newStatus}'.";
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

    public function openCreateQuestionModalForTrack(?int $categoryId = null, ?int $level = 1)
    {
        $this->reset(['editQuestionId', 'newQuestionText', 'optionA', 'optionB', 'optionC', 'optionD', 'correctOption', 'newQuestionExplanation', 'newQuestionCitation']);
        $this->newQuestionCategoryId = $categoryId ?: Category::first()?->id;
        $this->newQuestionLevel = $level ?: 1;
        $this->showQuestionModal = true;
    }

    public function openImportModalForTrack(?int $categoryId = null)
    {
        $this->reset(['importFile', 'importResults']);
        $this->importTrackId = $categoryId;
        $this->importDuplicateStrategy = 'skip';
        $this->showImportModal = true;
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
            $trackLevelSummaries = Question::query()
                ->select('category_id', 'level', \Illuminate\Support\Facades\DB::raw('count(*) as total_questions'), \Illuminate\Support\Facades\DB::raw('sum(case when is_active = 1 then 1 else 0 end) as active_questions'))
                ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
                ->when($this->selectedLevelFilter, fn($q) => $q->where('level', $this->selectedLevelFilter))
                ->groupBy('category_id', 'level')
                ->with('category')
                ->orderBy('category_id')
                ->orderBy('level')
                ->get();

            $totalQuestionsCount = Question::count();
            $totalActiveQuestionsCount = Question::where('is_active', true)->count();

            $diocesanCompetitions = DiocesanCompetition::with('category')
                ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
                ->latest()
                ->get();

            return view('livewire.arena-hub', [
                'user' => $user,
                'categories' => $categories,
                'trackLevelSummaries' => $trackLevelSummaries,
                'totalQuestionsCount' => $totalQuestionsCount,
                'totalActiveQuestionsCount' => $totalActiveQuestionsCount,
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
