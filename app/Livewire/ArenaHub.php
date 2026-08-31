<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DailyChallenge;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Parish;
use App\Models\ParishCompetition;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Models\UserChallengeParticipation;
use App\Services\AuditLogService;
use App\Services\DynamicContentImportService;
use App\Models\RallyParticipant;
use App\Models\RallyJoinRequest;
use App\Services\RallyAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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

    // Youth Rally Join & Code Entry
    public bool $showJoinModal = false;
    public ?string $selectedRallyForJoinId = null;
    public string $joinRequestMessage = '';

    // Youth Rally Review Modal (Score & Answers)
    public bool $showRallyReviewModal = false;
    public ?array $rallyReviewData = null;

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

    // Super Admin: Manage & Update Track Q&A Bank
    public bool $showManageTrackModal = false;
    public ?int $manageTrackCategoryId = null;
    public ?int $manageTrackLevel = null;
    public string $manageTrackName = '';
    public string $manageTrackDescription = '';
    public ?int $manageTargetLevel = null;
    public ?int $manageTargetCategoryId = null;
    public string $manageBatchActiveAction = 'keep'; // 'keep', 'activate_all', 'deactivate_all'

    // Super Admin: Create & Edit Competition
    public bool $showDiocesanCompModal = false;
    public ?string $editCompId = null;
    public string $newCompTitle = '';
    public string $newCompDescription = '';
    public string $newCompScopeType = 'diocese'; // diocese, deanery, parish, custom
    public string $newCompClassification = 'diocesan';
    public ?int $newCompDeaneryId = null;
    public ?int $newCompParishId = null;
    public ?int $newCompCategoryId = null;
    public string $newCompStartTime = '';
    public string $newCompEndTime = '';
    public ?string $newCompRegistrationOpenAt = null;
    public ?string $newCompRegistrationCloseAt = null;
    public bool $newCompJoinRequestsEnabled = true;
    public int $newCompTimeLimit = 15;
    public int $newCompQuestionCount = 15;
    public array $selectedCustomUserIds = [];
    public string $youthSearchTerm = '';

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

    public function openManageTrackModal(int $categoryId, ?int $level = null)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $category = Category::findOrFail($categoryId);
        $this->manageTrackCategoryId = $categoryId;
        $this->manageTrackLevel = $level;
        $this->manageTrackName = $category->name;
        $this->manageTrackDescription = $category->description ?? '';
        $this->manageTargetLevel = $level;
        $this->manageTargetCategoryId = $categoryId;
        $this->manageBatchActiveAction = 'keep';
        $this->showManageTrackModal = true;
    }

    public function saveTrackQAManagement()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate([
            'manageTrackCategoryId' => 'required|exists:categories,id',
            'manageTrackName' => 'required|string|min:3|max:120',
            'manageTrackDescription' => 'nullable|string|max:500',
            'manageTargetLevel' => 'nullable|integer|in:1,2,3,4',
            'manageTargetCategoryId' => 'nullable|exists:categories,id',
            'manageBatchActiveAction' => 'required|in:keep,activate_all,deactivate_all',
        ]);

        $category = Category::findOrFail($this->manageTrackCategoryId);
        $oldName = $category->name;
        $newSlug = \Illuminate\Support\Str::slug($this->manageTrackName);

        // 1. Update Category metadata
        $category->update([
            'name' => $this->manageTrackName,
            'slug' => $newSlug,
            'description' => $this->manageTrackDescription ?: null,
        ]);

        // Sync with TaxonomyTrack if present
        $taxTrack = TaxonomyTrack::where('slug', $category->slug)->orWhere('name', $oldName)->first();
        if ($taxTrack) {
            $taxTrack->update([
                'name' => $this->manageTrackName,
                'slug' => $newSlug,
                'description' => $this->manageTrackDescription ?: null,
            ]);
        }

        // 2. Batch update questions matching category (and level if selected)
        $questionQuery = Question::where('category_id', $this->manageTrackCategoryId);
        if ($this->manageTrackLevel !== null) {
            $questionQuery->where('level', $this->manageTrackLevel);
        }

        $updates = [];
        if ($this->manageTargetCategoryId && $this->manageTargetCategoryId !== $this->manageTrackCategoryId) {
            $updates['category_id'] = $this->manageTargetCategoryId;
        }
        if ($this->manageTargetLevel !== null && $this->manageTargetLevel !== $this->manageTrackLevel) {
            $updates['level'] = $this->manageTargetLevel;
        }
        if ($this->manageBatchActiveAction === 'activate_all') {
            $updates['is_active'] = true;
        } elseif ($this->manageBatchActiveAction === 'deactivate_all') {
            $updates['is_active'] = false;
        }

        $affectedCount = 0;
        if (!empty($updates)) {
            $affectedCount = $questionQuery->update($updates);
        }

        app(AuditLogService::class)->log(
            'track_qa_bank_updated',
            $category,
            ['old_name' => $oldName, 'level' => $this->manageTrackLevel],
            ['name' => $this->manageTrackName, 'updates' => $updates, 'affected_questions' => $affectedCount],
            $user
        );

        $this->reset(['showManageTrackModal', 'manageTrackCategoryId', 'manageTrackLevel', 'manageTrackName', 'manageTrackDescription', 'manageTargetLevel', 'manageTargetCategoryId']);
        $this->successMessage = "Track Q&A Bank '{$category->name}' successfully updated! ({$affectedCount} questions updated).";
    }

    public function toggleTrackQuestionsActive(int $categoryId, ?int $level = null)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $query = Question::where('category_id', $categoryId);
        if ($level !== null) {
            $query->where('level', $level);
        }

        $totalCount = (clone $query)->count();
        if ($totalCount === 0) {
            $this->errorMessage = "No questions found in this track tier.";
            return;
        }

        $activeCount = (clone $query)->where('is_active', true)->count();
        $newActiveState = ($activeCount === $totalCount) ? false : true;
        $query->update(['is_active' => $newActiveState]);

        $stateText = $newActiveState ? 'activated' : 'deactivated';
        $this->successMessage = "All {$totalCount} questions in this track tier have been {$stateText}.";
    }

    public function deleteTrackQuestions(int $categoryId, ?int $level = null)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $category = Category::find($categoryId);
        $catName = $category?->name ?? 'Track';

        $query = Question::where('category_id', $categoryId);
        if ($level !== null) {
            $query->where('level', $level);
        }

        $count = $query->count();
        $query->delete();

        app(AuditLogService::class)->log(
            'track_questions_deleted',
            $category ?? $user,
            ['category_id' => $categoryId, 'level' => $level, 'count' => $count],
            null,
            $user
        );

        $levelText = $level !== null ? " (Level {$level})" : '';
        $this->successMessage = "Successfully deleted {$count} questions from track '{$catName}'{$levelText}.";
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
            'importFile' => 'required|file|max:15360',
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
        $this->reset([
            'editCompId',
            'newCompTitle',
            'newCompDescription',
            'newCompScopeType',
            'newCompClassification',
            'newCompDeaneryId',
            'newCompParishId',
            'newCompCategoryId',
            'selectedCustomUserIds',
            'youthSearchTerm',
        ]);
        $this->newCompTimeLimit = 15;
        $this->newCompQuestionCount = 15;
        $this->newCompScopeType = 'diocese';
        $this->newCompClassification = 'diocesan';
        $this->newCompJoinRequestsEnabled = true;
        $this->newCompStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newCompEndTime = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->newCompRegistrationOpenAt = now()->format('Y-m-d\TH:i');
        $this->newCompRegistrationCloseAt = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->showDiocesanCompModal = true;
    }

    public function editCompetition(string $id)
    {
        $comp = DiocesanCompetition::with('participants')->findOrFail($id);
        $this->editCompId = $comp->id;
        $this->newCompTitle = $comp->title;
        $this->newCompDescription = $comp->description ?? '';
        $this->newCompScopeType = $comp->scope_type ?? 'diocese';
        $this->newCompClassification = $comp->competition_type ?? 'diocesan';
        $this->newCompDeaneryId = $comp->deanery_id;
        $this->newCompParishId = $comp->parish_id;
        $this->newCompCategoryId = $comp->category_id;
        $this->newCompTimeLimit = $comp->time_limit_seconds ?: 15;
        $this->newCompQuestionCount = $comp->question_count ?? 15;
        $this->newCompStartTime = $comp->start_time ? $comp->start_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        $this->newCompEndTime = $comp->end_time ? $comp->end_time->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i');
        $this->newCompRegistrationOpenAt = $comp->registration_open_at ? $comp->registration_open_at->format('Y-m-d\TH:i') : null;
        $this->newCompRegistrationCloseAt = $comp->registration_close_at ? $comp->registration_close_at->format('Y-m-d\TH:i') : null;
        $this->newCompJoinRequestsEnabled = (bool) ($comp->join_requests_enabled ?? true);
        $this->selectedCustomUserIds = $comp->participants->pluck('user_id')->map(fn($uid) => (string) $uid)->toArray();
        $this->showDiocesanCompModal = true;
    }

    public function toggleCustomUser(string $userId)
    {
        if (in_array($userId, $this->selectedCustomUserIds)) {
            $this->selectedCustomUserIds = array_values(array_diff($this->selectedCustomUserIds, [$userId]));
        } else {
            $this->selectedCustomUserIds[] = $userId;
        }
    }

    public function removeCustomUser(string $userId)
    {
        $this->selectedCustomUserIds = array_values(array_diff($this->selectedCustomUserIds, [$userId]));
    }

    public function saveCompetition()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validationRules = [
            'newCompTitle' => 'required|string|min:4|max:120',
            'newCompDescription' => 'required|string|min:10',
            'newCompScopeType' => 'required|in:diocese,deanery,parish,custom',
            'newCompClassification' => 'required|in:diocesan,deanery,parish,youth_rally',
            'newCompStartTime' => 'required|date',
            'newCompEndTime' => 'required|date|after:newCompStartTime',
            'newCompTimeLimit' => 'required|integer|min:5|max:3600',
            'newCompQuestionCount' => 'required|integer|min:5|max:100',
        ];

        if ($this->newCompScopeType === 'deanery') {
            $validationRules['newCompDeaneryId'] = 'required|exists:deaneries,id';
        } elseif ($this->newCompScopeType === 'parish') {
            $validationRules['newCompParishId'] = 'required|exists:parishes,id';
        }

        $this->validate($validationRules);

        $accessService = app(RallyAccessService::class);

        if ($this->editCompId) {
            $competition = DiocesanCompetition::findOrFail($this->editCompId);
            $competition->update([
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'scope_type' => $this->newCompScopeType,
                'competition_type' => $this->newCompClassification,
                'deanery_id' => $this->newCompScopeType === 'deanery' ? $this->newCompDeaneryId : null,
                'parish_id' => $this->newCompScopeType === 'parish' ? $this->newCompParishId : null,
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
                'start_time' => $this->newCompStartTime,
                'end_time' => $this->newCompEndTime,
                'registration_open_at' => $this->newCompRegistrationOpenAt ?: null,
                'registration_close_at' => $this->newCompRegistrationCloseAt ?: null,
                'join_requests_enabled' => $this->newCompJoinRequestsEnabled,
            ]);

            // If custom rally, synchronize custom participants
            if ($this->newCompScopeType === 'custom') {
                foreach ($this->selectedCustomUserIds as $customUserId) {
                    $youthUser = User::find($customUserId);
                    if ($youthUser) {
                        $accessService->addCustomParticipant($competition, $youthUser, $user);
                    }
                }
            }

            app(AuditLogService::class)->log(
                'diocesan_competition_updated',
                $competition,
                null,
                ['title' => $this->newCompTitle, 'scope' => $this->newCompScopeType],
                $user
            );

            $this->successMessage = "Rally '{$competition->title}' updated successfully!";
        } else {
            $pin = 'LV-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

            $competition = DiocesanCompetition::create([
                'created_by' => $user->id,
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'scope_type' => $this->newCompScopeType,
                'competition_type' => $this->newCompClassification,
                'deanery_id' => $this->newCompScopeType === 'deanery' ? $this->newCompDeaneryId : null,
                'parish_id' => $this->newCompScopeType === 'parish' ? $this->newCompParishId : null,
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'rally_pin' => $pin,
                'level' => 2,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
                'status' => 'active',
                'start_time' => $this->newCompStartTime,
                'end_time' => $this->newCompEndTime,
                'registration_open_at' => $this->newCompRegistrationOpenAt ?: null,
                'registration_close_at' => $this->newCompRegistrationCloseAt ?: null,
                'join_requests_enabled' => $this->newCompJoinRequestsEnabled,
                'is_public' => true,
            ]);

            // If custom rally, add each selected youth
            if ($this->newCompScopeType === 'custom') {
                foreach ($this->selectedCustomUserIds as $customUserId) {
                    $youthUser = User::find($customUserId);
                    if ($youthUser) {
                        $accessService->addCustomParticipant($competition, $youthUser, $user);
                    }
                }
            }

            app(AuditLogService::class)->log(
                'diocesan_competition_created',
                $competition,
                null,
                ['title' => $this->newCompTitle, 'scope' => $this->newCompScopeType, 'pin' => $pin],
                $user
            );

            $this->successMessage = "Rally '{$competition->title}' (Scope: " . strtoupper($competition->scope_type) . ") scheduled successfully!";
        }

        $this->reset([
            'editCompId',
            'newCompTitle',
            'newCompDescription',
            'newCompScopeType',
            'newCompClassification',
            'newCompDeaneryId',
            'newCompParishId',
            'selectedCustomUserIds',
            'youthSearchTerm',
            'showDiocesanCompModal',
        ]);
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

        $this->successMessage = "Rally '{$comp->title}' is now {$newStatus}.";
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

    public function enterRallyWithPin()
    {
        $this->reset(['errorMessage', 'successMessage']);
        $code = strtoupper(trim($this->rallyPin));
        if (empty($code)) {
            $this->errorMessage = 'Please enter your Rally Entry PIN or personal Access Code.';
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        // Rate Limiting: max 5 code entry attempts per 5 minutes per user
        $rateLimitKey = 'rally-entry:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->errorMessage = "Too many access attempts. Please wait {$seconds} seconds before trying again.";
            return;
        }

        // Find candidate rally by shared PIN or custom participant access code
        $rally = DiocesanCompetition::whereRaw('UPPER(rally_pin) = ?', [$code])->first();
        if (!$rally) {
            // Check if it matches a personal access code for custom rallies
            $participant = RallyParticipant::where('access_code', $code)->first();
            if ($participant) {
                $rally = $participant->rally;
            }
        }

        if (!$rally) {
            RateLimiter::hit($rateLimitKey, 300);
            $this->errorMessage = 'Invalid Rally PIN or access code. Please check and try again.';
            return;
        }

        // Run authoritative server-side security & eligibility validation
        $accessService = app(RallyAccessService::class);
        $result = $accessService->validateRallyAccess($rally, $user, $code);

        if (!$result['allowed']) {
            RateLimiter::hit($rateLimitKey, 300);
            $this->errorMessage = $result['message'];
            return;
        }

        RateLimiter::clear($rateLimitKey);
        return redirect()->route('quiz.runner', [
            'competition' => $rally->id,
            'code' => $code,
        ]);
    }

    public function openJoinRequestModal(string $rallyId)
    {
        $this->selectedRallyForJoinId = $rallyId;
        $this->joinRequestMessage = '';
        $this->showJoinModal = true;
    }

    public function submitJoinRequest()
    {
        $this->reset(['errorMessage', 'successMessage']);
        $user = Auth::user();
        if (!$user || !$this->selectedRallyForJoinId) {
            return;
        }

        $rally = DiocesanCompetition::findOrFail($this->selectedRallyForJoinId);
        $accessService = app(RallyAccessService::class);

        $request = $accessService->submitJoinRequest($rally, $user, $this->joinRequestMessage);

        $this->reset(['showJoinModal', 'selectedRallyForJoinId', 'joinRequestMessage']);
        $this->successMessage = "Your join request for '{$rally->title}' has been submitted to the diocesan coordinators.";
    }

    public function openRallyReview(string $participantId)
    {
        $participant = RallyParticipant::with(['rally.category', 'user'])->findOrFail($participantId);
        if ($participant->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $attemptId = $participant->metadata['quiz_attempt_id'] ?? null;
        $attempt = null;
        if ($attemptId) {
            $attempt = QuizAttempt::with(['answers.question.category'])->find($attemptId);
        }
        if (!$attempt) {
            $attempt = QuizAttempt::with(['answers.question.category'])
                ->where('user_id', $participant->user_id)
                ->where('category_id', $participant->rally->category_id ?? 1)
                ->latest()
                ->first();
        }

        $this->rallyReviewData = [
            'rally' => $participant->rally,
            'participant' => $participant,
            'attempt' => $attempt,
            'score' => $participant->score,
            'completed_at' => $participant->completed_at,
            'answers' => $attempt?->answers ?? collect(),
        ];
        $this->showRallyReviewModal = true;
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

            $diocesanCompetitions = DiocesanCompetition::with(['category', 'deanery', 'parish', 'participants.user'])
                ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
                ->latest()
                ->get();

            $deaneries = Deanery::orderBy('name')->get();
            $parishes = Parish::with('deanery')->orderBy('name')->get();
            
            $youthQuery = User::where('role', 'youth')->with('parish.deanery');
            if (!empty($this->youthSearchTerm)) {
                $term = '%' . trim($this->youthSearchTerm) . '%';
                $youthQuery->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term)
                      ->orWhereHas('parish', fn($pq) => $pq->where('name', 'like', $term));
                });
            }
            $allYouth = $youthQuery->orderBy('name')->limit(50)->get();

            return view('livewire.arena-hub', [
                'user' => $user,
                'categories' => $categories,
                'deaneries' => $deaneries,
                'parishes' => $parishes,
                'allYouth' => $allYouth,
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

        $accessService = app(RallyAccessService::class);
        $availableRallies = $accessService->getAvailableRalliesForUser($user);
        $myRallies = $accessService->getUserRallies($user);

        return view('livewire.arena-hub', [
            'user' => $user,
            'categories' => $categories,
            'todayChallenge' => $todayChallenge,
            'challengeCompleted' => $challengeCompleted,
            'rankedAttemptsCount' => $rankedAttemptsCount,
            'practiceAttemptsCount' => $practiceAttemptsCount,
            'availableRallies' => $availableRallies,
            'myRallies' => $myRallies,
        ])->layout('components.layouts.app', ['title' => 'Formation Arena • Diocese of Livingstone']);
    }
}
