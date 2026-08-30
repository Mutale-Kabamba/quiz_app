<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Lesson;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\QuizAttempt;
use App\Models\TaxonomyTrack;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\DiocesanAnalyticsService;
use App\Services\DynamicContentImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DioceseDashboard extends Component
{
    use WithFileUploads, WithPagination;

    // Active Navigation Tab
    public string $activeTab = 'overview'; // 'overview', 'deaneries', 'parishes', 'tracks', 'lessons', 'questions', 'competitions', 'admins', 'youth', 'reports'

    // Search & Filters
    public string $searchQuery = '';
    public ?int $selectedParishFilter = null;
    public ?int $selectedDeaneryFilter = null;
    public ?int $selectedCategoryFilter = null;

    // Global Modal Visibility Toggles
    public bool $showDeaneryModal = false;
    public bool $showParishModal = false;
    public bool $showTrackModal = false;
    public bool $showAdminModal = false;
    public bool $showQuestionModal = false;
    public bool $showLessonModal = false;
    public bool $showCompetitionModal = false;
    public bool $showImportModal = false;
    public bool $showReportModal = false;

    // 1. Deanery Form (Create & Edit)
    public ?int $editDeaneryId = null;
    public string $deaneryName = '';
    public string $deaneryCode = '';
    public string $deaneryHeadquarters = '';

    // 2. Parish Form (Create & Edit)
    public ?int $editParishId = null;
    public string $newParishName = '';
    public string $newParishCode = '';
    public ?int $newParishDeaneryId = null;
    public string $newParishLocation = '';
    public string $newParishEmail = '';
    public string $newParishPhone = '';

    // 3. Track / Category Form (Create & Edit)
    public ?int $editTrackId = null;
    public string $trackName = '';
    public string $trackSlug = '';
    public string $trackDescription = '';
    public string $trackIcon = 'cross';
    public int $trackDisplayOrder = 1;

    // 4. Parish Admin Form
    public ?string $editAdminId = null;
    public string $newAdminName = '';
    public string $newAdminPhone = '';
    public string $newAdminEmail = '';
    public string $newAdminPassword = '';
    public ?int $newAdminParishId = null;
    public string $newAdminRole = 'chairperson';
    public string $newAdminStatus = 'approved';

    // 5. Question Bank Form (Create & Edit)
    public ?string $editQuestionId = null;
    public ?int $newQuestionCategoryId = null;
    public ?int $newQuestionTrackId = null;
    public string $newQuestionText = '';
    public string $optionA = '';
    public string $optionB = '';
    public string $optionC = '';
    public string $optionD = '';
    public string $correctOption = 'A';
    public string $newQuestionExplanation = '';
    public string $newQuestionCitation = '';
    public int $newQuestionLevel = 1;

    // 6. Study Lesson Form (Create & Edit & In-App Preview)
    public ?string $editLessonId = null;
    public ?int $lessonCategoryId = null;
    public string $lessonSeriesIdentifier = '';
    public string $lessonSeriesTitle = '';
    public int $lessonSeriesOrder = 1;
    public bool $lessonIsProgressive = true;
    public string $lessonTitle = '';
    public string $lessonSubheading = '';
    public string $lessonContent = '';
    public string $lessonTakeaways = '';
    public int $lessonReadMinutes = 5;
    public int $lessonDifficulty = 1;
    public string $lessonScripture = '';
    public string $lessonCatechism = '';
    public string $lessonStatus = 'published';
    public ?string $previewLessonId = null;
    public bool $showLessonPreviewModal = false;

    // 7. Rally & Competition Form (Create & Edit)
    public ?string $editCompetitionId = null;
    public string $newCompTitle = '';
    public string $newCompDescription = '';
    public string $newCompType = 'diocesan';
    public ?int $newCompCategoryId = null;
    public string $newCompStartTime = '';
    public string $newCompEndTime = '';
    public int $newCompTimeLimit = 300;
    public int $newCompQuestionCount = 15;

    // 8. Dynamic File Import State for Questions (CSV, XLSX, JSON)
    public $importFile = null;
    public ?int $importTrackId = null;
    public string $importDuplicateStrategy = 'skip'; // 'skip', 'overwrite'
    public ?array $importResults = null;
    public bool $isImporting = false;

    // 9. Dynamic File Import State for Lessons (CSV, XLSX, JSON)
    public bool $showLessonImportModal = false;
    public $lessonImportFile = null;
    public ?int $lessonImportCategoryId = null;
    public string $lessonImportDuplicateStrategy = 'skip'; // 'skip', 'overwrite'
    public ?array $lessonImportResults = null;
    public bool $isLessonImporting = false;

    // 10. Track Q&A Bank Management Properties
    public bool $showManageTrackModal = false;
    public ?int $manageTrackCategoryId = null;
    public ?int $manageTrackLevel = null;
    public string $manageTrackName = '';
    public string $manageTrackDescription = '';
    public ?int $manageTargetLevel = null;
    public ?int $manageTargetCategoryId = null;
    public string $manageBatchActiveAction = 'keep'; // 'keep', 'activate_all', 'deactivate_all'

    // Report Summary Data
    public ?array $reportSummary = null;

    // Toast Notifications
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount()
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin diocesan privileges required.');
        }

        $this->newCompStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newCompEndTime = now()->addDays(7)->format('Y-m-d\TH:i');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // =========================================================================
    // 1. DEANERY CRUD
    // =========================================================================
    public function openCreateDeaneryModal()
    {
        $this->reset(['editDeaneryId', 'deaneryName', 'deaneryCode', 'deaneryHeadquarters']);
        $this->showDeaneryModal = true;
    }

    public function editDeanery(int $id)
    {
        $deanery = Deanery::findOrFail($id);
        $this->editDeaneryId = $deanery->id;
        $this->deaneryName = $deanery->name;
        $this->deaneryCode = $deanery->code ?? '';
        $this->deaneryHeadquarters = $deanery->headquarters ?? '';
        $this->showDeaneryModal = true;
    }

    public function saveDeanery()
    {
        $rules = [
            'deaneryName' => 'required|string|min:3|max:100',
            'deaneryCode' => 'required|string|min:2|max:20|unique:deaneries,code,' . $this->editDeaneryId,
            'deaneryHeadquarters' => 'nullable|string|max:100',
        ];
        $this->validate($rules);

        if ($this->editDeaneryId) {
            $deanery = Deanery::findOrFail($this->editDeaneryId);
            $deanery->update([
                'name' => $this->deaneryName,
                'code' => strtoupper($this->deaneryCode),
                'headquarters' => $this->deaneryHeadquarters ?: null,
            ]);

            app(AuditLogService::class)->log(
                'deanery_updated',
                $deanery,
                null,
                ['name' => $this->deaneryName, 'code' => $this->deaneryCode],
                Auth::user()
            );
            $this->successMessage = "Deanery '{$deanery->name}' updated successfully!";
        } else {
            $deanery = Deanery::create([
                'name' => $this->deaneryName,
                'code' => strtoupper($this->deaneryCode),
                'headquarters' => $this->deaneryHeadquarters ?: null,
            ]);

            app(AuditLogService::class)->log(
                'deanery_created',
                $deanery,
                null,
                ['name' => $this->deaneryName, 'code' => $this->deaneryCode],
                Auth::user()
            );
            $this->successMessage = "Deanery '{$deanery->name}' created successfully!";
        }

        $this->reset(['editDeaneryId', 'deaneryName', 'deaneryCode', 'deaneryHeadquarters', 'showDeaneryModal']);
    }

    public function deleteDeanery(int $id)
    {
        $deanery = Deanery::withCount('parishes')->findOrFail($id);
        if ($deanery->parishes_count > 0) {
            $this->errorMessage = "Cannot delete '{$deanery->name}' because it contains {$deanery->parishes_count} parishes. Please reassign the parishes first.";
            return;
        }

        app(AuditLogService::class)->log(
            'deanery_deleted',
            $deanery,
            ['name' => $deanery->name],
            null,
            Auth::user()
        );

        $deanery->delete();
        $this->successMessage = "Deanery '{$deanery->name}' deleted successfully.";
    }

    public function createParish()
    {
        return $this->saveParish();
    }

    public function createDeanery()
    {
        return $this->saveDeanery();
    }

    public function createTrack()
    {
        return $this->saveTrack();
    }

    // =========================================================================
    // 2. PARISH CRUD
    // =========================================================================
    public function openCreateParishModal()
    {
        $this->reset(['editParishId', 'newParishName', 'newParishCode', 'newParishDeaneryId', 'newParishLocation', 'newParishEmail', 'newParishPhone']);
        $this->showParishModal = true;
    }

    public function editParish(int $id)
    {
        $parish = Parish::findOrFail($id);
        $this->editParishId = $parish->id;
        $this->newParishName = $parish->name;
        $this->newParishCode = $parish->code ?? '';
        $this->newParishDeaneryId = $parish->deanery_id;
        $this->newParishLocation = $parish->location ?? '';
        $this->newParishEmail = $parish->contact_email ?? '';
        $this->newParishPhone = $parish->contact_phone ?? '';
        $this->showParishModal = true;
    }

    public function saveParish()
    {
        $rules = [
            'newParishName' => 'required|string|min:3|max:100',
            'newParishCode' => 'required|string|min:2|max:20|unique:parishes,code,' . $this->editParishId,
            'newParishDeaneryId' => 'required|exists:deaneries,id',
            'newParishLocation' => 'nullable|string|max:100',
            'newParishEmail' => 'nullable|email|max:100',
            'newParishPhone' => 'nullable|string|max:20',
        ];
        $this->validate($rules);

        if ($this->editParishId) {
            $parish = Parish::findOrFail($this->editParishId);
            $parish->update([
                'deanery_id' => $this->newParishDeaneryId,
                'name' => $this->newParishName,
                'code' => strtoupper($this->newParishCode),
                'location' => $this->newParishLocation ?: null,
                'contact_email' => $this->newParishEmail ?: null,
                'contact_phone' => $this->newParishPhone ?: null,
            ]);

            app(AuditLogService::class)->log(
                'parish_updated',
                $parish,
                null,
                ['name' => $this->newParishName, 'code' => $this->newParishCode],
                Auth::user()
            );
            $this->successMessage = "Parish '{$parish->name}' updated successfully!";
        } else {
            $parish = Parish::create([
                'deanery_id' => $this->newParishDeaneryId,
                'name' => $this->newParishName,
                'code' => strtoupper($this->newParishCode),
                'location' => $this->newParishLocation ?: null,
                'contact_email' => $this->newParishEmail ?: null,
                'contact_phone' => $this->newParishPhone ?: null,
                'is_active' => true,
            ]);

            app(AuditLogService::class)->log(
                'parish_created',
                $parish,
                null,
                ['name' => $this->newParishName, 'code' => $this->newParishCode],
                Auth::user()
            );
            $this->successMessage = "Parish '{$parish->name}' registered successfully!";
        }

        $this->reset(['editParishId', 'newParishName', 'newParishCode', 'newParishDeaneryId', 'newParishLocation', 'newParishEmail', 'newParishPhone', 'showParishModal']);
    }

    public function deleteParish(int $id)
    {
        $parish = Parish::withCount('users')->findOrFail($id);
        if ($parish->users_count > 0) {
            $this->errorMessage = "Cannot delete '{$parish->name}' because it has {$parish->users_count} registered users. Please reassign the members first.";
            return;
        }

        app(AuditLogService::class)->log(
            'parish_deleted',
            $parish,
            ['name' => $parish->name],
            null,
            Auth::user()
        );

        $parish->delete();
        $this->successMessage = "Parish '{$parish->name}' deleted successfully.";
    }

    // =========================================================================
    // 3. TRACK & CATEGORY CRUD
    // =========================================================================
    public function openCreateTrackModal()
    {
        $this->reset(['editTrackId', 'trackName', 'trackSlug', 'trackDescription', 'trackIcon', 'trackDisplayOrder']);
        $this->showTrackModal = true;
    }

    public function editTrack(int $id)
    {
        $track = TaxonomyTrack::findOrFail($id);
        $this->editTrackId = $track->id;
        $this->trackName = $track->name;
        $this->trackSlug = $track->slug ?? Str::slug($track->name);
        $this->trackDescription = $track->description ?? '';
        $this->trackIcon = $track->icon ?? 'cross';
        $this->trackDisplayOrder = $track->display_order ?? 1;
        $this->showTrackModal = true;
    }

    public function saveTrack()
    {
        $this->validate([
            'trackName' => 'required|string|min:3|max:100',
            'trackDescription' => 'nullable|string|max:500',
            'trackIcon' => 'nullable|string|max:50',
            'trackDisplayOrder' => 'required|integer|min:1',
        ]);

        $slug = Str::slug($this->trackName);
        $code = strtoupper(Str::slug($this->trackName, '_'));

        if ($this->editTrackId) {
            $track = TaxonomyTrack::findOrFail($this->editTrackId);
            $track->update([
                'name' => $this->trackName,
                'slug' => $slug,
                'code' => $track->code ?: $code,
                'description' => $this->trackDescription ?: null,
                'icon' => $this->trackIcon ?: 'cross',
                'display_order' => $this->trackDisplayOrder,
            ]);

            // Sync with category if exists
            $category = Category::where('name', $track->name)->orWhere('slug', $track->slug)->first();
            if ($category) {
                $category->update([
                    'name' => $this->trackName,
                    'slug' => $slug,
                    'description' => $this->trackDescription ?: null,
                    'icon' => $this->trackIcon ?: 'cross',
                ]);
            }

            $this->successMessage = "Track '{$track->name}' updated successfully!";
        } else {
            $track = TaxonomyTrack::create([
                'name' => $this->trackName,
                'slug' => $slug,
                'code' => $code,
                'description' => $this->trackDescription ?: null,
                'icon' => $this->trackIcon ?: 'cross',
                'display_order' => $this->trackDisplayOrder,
                'is_active' => true,
            ]);

            Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $this->trackName,
                    'description' => $this->trackDescription ?: null,
                    'icon' => $this->trackIcon ?: 'cross',
                    'order' => $this->trackDisplayOrder,
                ]
            );

            $this->successMessage = "Track '{$track->name}' created successfully!";
        }

        $this->reset(['editTrackId', 'trackName', 'trackSlug', 'trackDescription', 'trackIcon', 'trackDisplayOrder', 'showTrackModal']);
    }

    public function deleteTrack(int $id)
    {
        $track = TaxonomyTrack::findOrFail($id);
        $category = Category::where('name', $track->name)->orWhere('slug', $track->slug)->first();

        $track->delete();
        if ($category && $category->questions()->count() === 0) {
            $category->delete();
        }

        $this->successMessage = "Track deleted successfully.";
    }

    // =========================================================================
    // 4. PARISH ADMIN / CHAIRPERSON CRUD
    // =========================================================================
    public function openCreateAdminModal()
    {
        $this->reset(['editAdminId', 'newAdminName', 'newAdminPhone', 'newAdminEmail', 'newAdminPassword', 'newAdminParishId']);
        $this->newAdminRole = 'chairperson';
        $this->newAdminStatus = 'approved';
        $this->newAdminPassword = '';
        $this->showAdminModal = true;
    }

    public function editAdmin(string $id)
    {
        $admin = User::findOrFail($id);
        $this->editAdminId = $admin->id;
        $this->newAdminName = $admin->name;
        $this->newAdminPhone = $admin->phone ?? '';
        $this->newAdminEmail = $admin->email ?? '';
        $this->newAdminParishId = $admin->parish_id;
        $this->newAdminRole = $admin->role ?? 'chairperson';
        $this->newAdminStatus = $admin->status ?? 'approved';
        $this->newAdminPassword = '';
        $this->showAdminModal = true;
    }

    public function saveAdmin()
    {
        $rules = [
            'newAdminName' => 'required|string|min:3|max:100',
            'newAdminPhone' => 'required|string|min:6|max:20|unique:users,phone' . ($this->editAdminId ? ",{$this->editAdminId}" : ''),
            'newAdminEmail' => 'nullable|email|max:100|unique:users,email' . ($this->editAdminId ? ",{$this->editAdminId}" : ''),
            'newAdminParishId' => 'required|exists:parishes,id',
            'newAdminRole' => 'required|in:chairperson,deanery_admin',
            'newAdminStatus' => 'required|in:approved,pending,rejected',
        ];

        if (!$this->editAdminId) {
            $rules['newAdminPassword'] = 'required|string|min:6';
        } else {
            $rules['newAdminPassword'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        if ($this->editAdminId) {
            $admin = User::findOrFail($this->editAdminId);
            $updateData = [
                'parish_id' => $this->newAdminParishId,
                'name' => $this->newAdminName,
                'phone' => $this->newAdminPhone,
                'email' => $this->newAdminEmail ?: null,
                'role' => $this->newAdminRole,
                'status' => $this->newAdminStatus,
            ];

            if (!empty($this->newAdminPassword)) {
                $updateData['password'] = Hash::make($this->newAdminPassword);
            }

            $admin->update($updateData);

            app(AuditLogService::class)->log(
                'parish_admin_updated',
                $admin,
                null,
                ['name' => $this->newAdminName, 'parish_id' => $this->newAdminParishId, 'role' => $this->newAdminRole],
                Auth::user()
            );

            $this->successMessage = "Administrator '{$admin->name}' updated successfully!";
        } else {
            $admin = User::create([
                'parish_id' => $this->newAdminParishId,
                'name' => $this->newAdminName,
                'phone' => $this->newAdminPhone,
                'email' => $this->newAdminEmail ?: null,
                'password' => Hash::make($this->newAdminPassword ?: 'password'),
                'role' => $this->newAdminRole,
                'status' => $this->newAdminStatus,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            app(AuditLogService::class)->log(
                'parish_admin_created',
                $admin,
                null,
                ['name' => $this->newAdminName, 'parish_id' => $this->newAdminParishId],
                Auth::user()
            );

            $this->successMessage = "Parish Chairperson '{$admin->name}' successfully created and assigned.";
        }

        $this->reset(['editAdminId', 'newAdminName', 'newAdminPhone', 'newAdminEmail', 'newAdminPassword', 'newAdminParishId', 'showAdminModal']);
    }

    public function createParishAdmin()
    {
        $this->saveAdmin();
    }

    public function deleteAdmin(string $id)
    {
        $admin = User::findOrFail($id);

        if ($admin->id === Auth::id()) {
            $this->errorMessage = "You cannot delete your own super admin account.";
            return;
        }

        if ($admin->isSuperAdmin()) {
            $this->errorMessage = "Cannot delete super administrator accounts from this view.";
            return;
        }

        app(AuditLogService::class)->log(
            'parish_admin_deleted',
            $admin,
            ['name' => $admin->name, 'email' => $admin->email],
            null,
            Auth::user()
        );

        $admin->delete();
        $this->successMessage = "Administrator '{$admin->name}' deleted successfully.";
    }

    public function toggleAdminStatus(string $id)
    {
        $admin = User::findOrFail($id);
        if ($admin->id === Auth::id()) {
            $this->errorMessage = "You cannot suspend your own account.";
            return;
        }

        $newStatus = $admin->status === 'approved' ? 'rejected' : 'approved';
        $admin->update(['status' => $newStatus]);

        app(AuditLogService::class)->log(
            'parish_admin_status_toggled',
            $admin,
            ['status' => $admin->status],
            ['status' => $newStatus],
            Auth::user()
        );

        $statusLabel = $newStatus === 'approved' ? 'Active' : 'Suspended';
        $this->successMessage = "Administrator '{$admin->name}' status changed to {$statusLabel}.";
    }

    // =========================================================================
    // 5. QUESTION BANK CRUD (Q&A)
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

            $this->successMessage = 'New question successfully added to question bank!';
        }

        $this->reset(['editQuestionId', 'newQuestionCategoryId', 'newQuestionText', 'optionA', 'optionB', 'optionC', 'optionD', 'newQuestionExplanation', 'newQuestionCitation', 'showQuestionModal']);
    }

    public function deleteQuestion(string $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        $this->successMessage = 'Question deleted from bank.';
    }

    public function openManageTrackModal(int $categoryId, ?int $level = null)
    {
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
        $newSlug = Str::slug($this->manageTrackName);

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
            Auth::user()
        );

        $this->reset(['showManageTrackModal', 'manageTrackCategoryId', 'manageTrackLevel', 'manageTrackName', 'manageTrackDescription', 'manageTargetLevel', 'manageTargetCategoryId']);
        $this->successMessage = "Track Q&A Bank '{$category->name}' successfully updated! ({$affectedCount} questions updated).";
    }

    public function toggleTrackQuestionsActive(int $categoryId, ?int $level = null)
    {
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
        // If all are active, deactivate all; otherwise activate all
        $newActiveState = ($activeCount === $totalCount) ? false : true;
        $query->update(['is_active' => $newActiveState]);

        $stateText = $newActiveState ? 'activated' : 'deactivated';
        $this->successMessage = "All {$totalCount} questions in this track tier have been {$stateText}.";
    }

    public function deleteTrackQuestions(int $categoryId, ?int $level = null)
    {
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
            $category ?? Auth::user(),
            ['category_id' => $categoryId, 'level' => $level, 'count' => $count],
            null,
            Auth::user()
        );

        $levelText = $level !== null ? " (Level {$level})" : '';
        $this->successMessage = "Successfully deleted {$count} questions from track '{$catName}'{$levelText}.";
    }

    // =========================================================================
    // 6. STUDY LESSONS CRUD
    // =========================================================================
    public function openCreateLessonModal()
    {
        $this->reset([
            'editLessonId',
            'lessonCategoryId',
            'lessonSeriesIdentifier',
            'lessonSeriesTitle',
            'lessonTitle',
            'lessonSubheading',
            'lessonContent',
            'lessonTakeaways',
            'lessonScripture',
            'lessonCatechism',
        ]);
        $this->lessonSeriesOrder = 1;
        $this->lessonIsProgressive = true;
        $this->lessonReadMinutes = 5;
        $this->lessonDifficulty = 1;
        $this->lessonStatus = 'published';
        $this->showLessonModal = true;
    }

    public function editLesson(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        $this->editLessonId = $lesson->id;
        $this->lessonCategoryId = $lesson->category_id;
        $this->lessonSeriesIdentifier = $lesson->series_identifier ?? '';
        $this->lessonSeriesTitle = $lesson->series_title ?? '';
        $this->lessonSeriesOrder = $lesson->series_order ?? 1;
        $this->lessonIsProgressive = (bool) ($lesson->is_progressive ?? true);
        $this->lessonTitle = $lesson->title;
        $this->lessonSubheading = $lesson->subheading ?? '';
        
        // Flatten content sections to text
        if (is_array($lesson->content_sections)) {
            $sectionsText = [];
            foreach ($lesson->content_sections as $sec) {
                if (is_array($sec)) {
                    $heading = $sec['heading'] ?? '';
                    $body = $sec['body'] ?? '';
                    $sectionsText[] = ($heading && $heading !== $lesson->title ? "### {$heading}\n" : '') . $body;
                } else {
                    $sectionsText[] = (string) $sec;
                }
            }
            $this->lessonContent = implode("\n\n", $sectionsText);
        } else {
            $this->lessonContent = (string) ($lesson->content_sections ?? '');
        }

        if (is_array($lesson->summary_takeaways)) {
            $this->lessonTakeaways = implode("\n", $lesson->summary_takeaways);
        } else {
            $this->lessonTakeaways = (string) ($lesson->summary_takeaways ?? '');
        }

        $this->lessonReadMinutes = $lesson->estimated_read_minutes ?? 5;
        $this->lessonDifficulty = $lesson->difficulty ?? 1;
        $this->lessonScripture = $lesson->scripture_citations ?? '';
        $this->lessonCatechism = $lesson->catechism_citations ?? '';
        $this->lessonStatus = $lesson->status ?? 'published';
        $this->showLessonModal = true;
    }

    public function saveLesson()
    {
        $this->validate([
            'lessonCategoryId' => 'required|exists:categories,id',
            'lessonTitle' => 'required|string|min:3|max:150',
            'lessonSubheading' => 'nullable|string|max:255',
            'lessonSeriesIdentifier' => 'nullable|string|max:100',
            'lessonSeriesTitle' => 'nullable|string|max:150',
            'lessonSeriesOrder' => 'nullable|integer|min:1|max:100',
            'lessonIsProgressive' => 'boolean',
            'lessonContent' => 'required|string|min:10',
            'lessonReadMinutes' => 'required|integer|min:1|max:60',
            'lessonDifficulty' => 'required|in:1,2,3',
            'lessonStatus' => 'required|in:published,draft',
        ]);

        $takeawaysArray = array_values(array_filter(
            array_map('trim', explode("\n", $this->lessonTakeaways)),
            fn($item) => !empty($item)
        ));

        $contentSections = [
            [
                'heading' => $this->lessonTitle,
                'body' => $this->lessonContent,
            ]
        ];

        $slug = Str::slug($this->lessonTitle);

        // Resolve series identifier
        $seriesId = !empty(trim($this->lessonSeriesIdentifier)) 
            ? Str::slug($this->lessonSeriesIdentifier) 
            : (!empty(trim($this->lessonSeriesTitle)) ? Str::slug($this->lessonSeriesTitle) : null);
        $seriesTitle = !empty(trim($this->lessonSeriesTitle)) 
            ? trim($this->lessonSeriesTitle) 
            : (!empty($seriesId) ? ucwords(str_replace('-', ' ', $seriesId)) : null);

        if ($this->editLessonId) {
            $lesson = Lesson::findOrFail($this->editLessonId);
            $lesson->update([
                'category_id' => $this->lessonCategoryId,
                'series_identifier' => $seriesId,
                'series_title' => $seriesTitle,
                'series_order' => $seriesId ? ($this->lessonSeriesOrder ?: 1) : null,
                'is_progressive' => $this->lessonIsProgressive,
                'title' => $this->lessonTitle,
                'slug' => $slug,
                'subheading' => $this->lessonSubheading ?: null,
                'summary_takeaways' => $takeawaysArray,
                'content_sections' => $contentSections,
                'estimated_read_minutes' => $this->lessonReadMinutes,
                'difficulty' => $this->lessonDifficulty,
                'scripture_citations' => $this->lessonScripture ?: null,
                'catechism_citations' => $this->lessonCatechism ?: null,
                'status' => $this->lessonStatus,
            ]);

            $this->successMessage = "Lesson '{$lesson->title}' updated successfully!";
        } else {
            $lesson = Lesson::create([
                'category_id' => $this->lessonCategoryId,
                'series_identifier' => $seriesId,
                'series_title' => $seriesTitle,
                'series_order' => $seriesId ? ($this->lessonSeriesOrder ?: 1) : null,
                'is_progressive' => $this->lessonIsProgressive,
                'title' => $this->lessonTitle,
                'slug' => $slug . '-' . Str::random(4),
                'subheading' => $this->lessonSubheading ?: null,
                'summary_takeaways' => $takeawaysArray,
                'content_sections' => $contentSections,
                'estimated_read_minutes' => $this->lessonReadMinutes,
                'difficulty' => $this->lessonDifficulty,
                'scripture_citations' => $this->lessonScripture ?: null,
                'catechism_citations' => $this->lessonCatechism ?: null,
                'display_order' => Lesson::where('category_id', $this->lessonCategoryId)->count() + 1,
                'status' => $this->lessonStatus,
            ]);

            $this->successMessage = "Study lesson '{$lesson->title}' created successfully!";
        }

        $this->reset([
            'editLessonId',
            'lessonCategoryId',
            'lessonSeriesIdentifier',
            'lessonSeriesTitle',
            'lessonTitle',
            'lessonSubheading',
            'lessonContent',
            'lessonTakeaways',
            'lessonScripture',
            'lessonCatechism',
            'showLessonModal',
        ]);
        $this->lessonSeriesOrder = 1;
        $this->lessonIsProgressive = true;
    }

    public function toggleLessonStatus(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        $newStatus = $lesson->status === 'published' ? 'draft' : 'published';
        $lesson->update(['status' => $newStatus]);
        $this->successMessage = "Lesson '{$lesson->title}' status set to {$newStatus}.";
    }

    public function deleteLesson(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        $this->successMessage = "Lesson deleted successfully.";
    }

    public function previewLesson(string $id)
    {
        $this->previewLessonId = $id;
        $this->showLessonPreviewModal = true;
    }

    public function closeLessonPreview()
    {
        $this->previewLessonId = null;
        $this->showLessonPreviewModal = false;
    }

    public function openLessonImportModalForTrack(?int $categoryId = null)
    {
        $this->reset(['lessonImportFile', 'lessonImportResults', 'isLessonImporting']);
        $this->lessonImportCategoryId = $categoryId;
        $this->lessonImportDuplicateStrategy = 'skip';
        $this->showLessonImportModal = true;
    }

    public function toggleTrackLessonsStatus(int $categoryId)
    {
        $query = Lesson::where('category_id', $categoryId);
        $total = (clone $query)->count();
        if ($total === 0) {
            $this->errorMessage = "No lessons found in this track.";
            return;
        }

        $publishedCount = (clone $query)->where('status', 'published')->count();
        $newStatus = ($publishedCount === $total) ? 'draft' : 'published';
        $query->update(['status' => $newStatus]);

        $statusText = $newStatus === 'published' ? 'published' : 'moved to draft';
        $this->successMessage = "All {$total} lessons in this track have been {$statusText}.";
    }

    public function deleteTrackLessons(int $categoryId)
    {
        $category = Category::find($categoryId);
        $catName = $category?->name ?? 'Track';

        $query = Lesson::where('category_id', $categoryId);
        $count = $query->count();
        $query->delete();

        app(AuditLogService::class)->log(
            'track_lessons_deleted',
            $category ?? Auth::user(),
            ['category_id' => $categoryId, 'count' => $count],
            null,
            Auth::user()
        );

        $this->successMessage = "Successfully deleted {$count} lessons from track '{$catName}'.";
    }

    // =========================================================================
    // 7. RALLIES & COMPETITIONS CRUD
    // =========================================================================
    public function openCreateCompetitionModal()
    {
        $this->reset(['editCompetitionId', 'newCompTitle', 'newCompDescription', 'newCompType', 'newCompCategoryId', 'newCompTimeLimit', 'newCompQuestionCount']);
        $this->newCompStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->newCompEndTime = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->showCompetitionModal = true;
    }

    public function editCompetition(string $id)
    {
        $competition = DiocesanCompetition::findOrFail($id);
        $this->editCompetitionId = $competition->id;
        $this->newCompTitle = $competition->title;
        $this->newCompDescription = $competition->description ?? '';
        $this->newCompType = $competition->competition_type ?? 'diocesan';
        $this->newCompCategoryId = $competition->category_id;
        $this->newCompStartTime = $competition->start_time ? $competition->start_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        $this->newCompEndTime = $competition->end_time ? $competition->end_time->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i');
        $this->newCompTimeLimit = $competition->time_limit_seconds ?? 300;
        $this->newCompQuestionCount = $competition->question_count ?? 15;
        $this->showCompetitionModal = true;
    }

    public function saveCompetition()
    {
        $this->validate([
            'newCompTitle' => 'required|string|min:4|max:120',
            'newCompDescription' => 'required|string|min:10',
            'newCompType' => 'required|in:diocesan,deanery,parish,youth_rally',
            'newCompStartTime' => 'required|date',
            'newCompEndTime' => 'required|date|after:newCompStartTime',
        ]);

        if ($this->editCompetitionId) {
            $competition = DiocesanCompetition::findOrFail($this->editCompetitionId);
            $competition->update([
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'competition_type' => $this->newCompType,
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
                'start_time' => $this->newCompStartTime,
                'end_time' => $this->newCompEndTime,
            ]);

            $this->successMessage = "Competition '{$competition->title}' updated successfully!";
        } else {
            $competition = DiocesanCompetition::create([
                'created_by' => Auth::id(),
                'title' => $this->newCompTitle,
                'description' => $this->newCompDescription,
                'competition_type' => $this->newCompType,
                'category_id' => $this->newCompCategoryId ?: Category::first()?->id,
                'rally_pin' => (string) random_int(100000, 999999),
                'level' => 2,
                'time_limit_seconds' => $this->newCompTimeLimit,
                'question_count' => $this->newCompQuestionCount,
                'status' => 'active',
                'start_time' => $this->newCompStartTime,
                'end_time' => $this->newCompEndTime,
                'scoring_rules' => [
                    'base_xp_per_correct' => 15,
                    'speed_bonus' => true,
                    'penalty_for_wrong' => false,
                ],
            ]);

            $this->successMessage = "Diocesan Competition '{$competition->title}' scheduled!";
        }

        $this->reset(['editCompetitionId', 'newCompTitle', 'newCompDescription', 'showCompetitionModal']);
    }

    public function toggleCompetitionStatus(string $id)
    {
        $comp = DiocesanCompetition::findOrFail($id);
        $newStatus = $comp->status === 'active' ? 'concluded' : 'active';
        $comp->update(['status' => $newStatus]);

        $this->successMessage = "Competition '{$comp->title}' is now {$newStatus}.";
    }

    public function deleteCompetition(string $id)
    {
        $comp = DiocesanCompetition::findOrFail($id);
        $comp->delete();
        $this->successMessage = "Competition deleted successfully.";
    }

    // =========================================================================
    // 8. DYNAMIC FILE IMPORT (CSV, XLSX, JSON)
    // =========================================================================
    public function openImportModal()
    {
        $this->reset(['importFile', 'importResults', 'isImporting']);
        $this->importTrackId = null;
        $this->importDuplicateStrategy = 'skip';
        $this->showImportModal = true;
    }

    public function openImportModalForTrack(?int $trackId = null)
    {
        $this->reset(['importFile', 'importResults', 'isImporting']);
        $this->importTrackId = $trackId;
        $this->importDuplicateStrategy = 'skip';
        $this->showImportModal = true;
    }

    public function processDynamicImport()
    {
        $this->validate([
            'importFile' => 'required|file|max:15360',
            'importDuplicateStrategy' => 'required|in:skip,overwrite',
        ]);

        $this->isImporting = true;
        $importService = app(DynamicContentImportService::class);

        try {
            // Parse file into rows
            $rows = $importService->parseFile($this->importFile);

            if (empty($rows)) {
                $this->errorMessage = 'No valid question rows found in the uploaded file. Please verify file format and columns.';
                $this->isImporting = false;
                return;
            }

            // Execute import
            $results = $importService->importQuestions(
                rows: $rows,
                fallbackTrackId: $this->importTrackId,
                duplicateStrategy: $this->importDuplicateStrategy,
                uploader: Auth::user()
            );

            $this->importResults = $results;
            $this->isImporting = false;

            app(AuditLogService::class)->log(
                'bulk_questions_imported',
                Auth::user(),
                null,
                [
                    'successful' => $results['successful'],
                    'duplicates_skipped' => $results['duplicates_skipped'],
                    'failed' => $results['failed'],
                ],
                Auth::user()
            );

            $this->successMessage = "Import completed! Successfully imported {$results['successful']} questions ({$results['duplicates_skipped']} duplicates skipped).";
        } catch (\Throwable $e) {
            $this->isImporting = false;
            $this->errorMessage = "Import failed: {$e->getMessage()}";
        }
    }

    public function downloadSampleTemplate(string $format = 'csv')
    {
        $importService = app(DynamicContentImportService::class);

        if ($format === 'json') {
            return response()->streamDownload(function () use ($importService) {
                echo $importService->getSampleJson();
            }, 'catholic_questions_template.json', ['Content-Type' => 'application/json']);
        }

        return response()->streamDownload(function () use ($importService) {
            echo $importService->getSampleCsv();
        }, 'catholic_questions_template.csv', ['Content-Type' => 'text/csv']);
    }

    // =========================================================================
    // 9. DYNAMIC FILE IMPORT FOR LESSONS (CSV, XLSX, JSON)
    // =========================================================================
    public function openLessonImportModal()
    {
        $this->reset(['lessonImportFile', 'lessonImportResults', 'isLessonImporting']);
        $this->lessonImportDuplicateStrategy = 'skip';
        $this->showLessonImportModal = true;
    }

    public function processLessonImport()
    {
        $this->validate([
            'lessonImportFile' => 'required|file|max:15360',
            'lessonImportDuplicateStrategy' => 'required|in:skip,overwrite',
        ]);

        $this->isLessonImporting = true;
        $importService = app(DynamicContentImportService::class);

        try {
            // Parse file into rows
            $rows = $importService->parseFile($this->lessonImportFile);

            if (empty($rows)) {
                $this->errorMessage = 'No valid lesson rows found in the uploaded file. Please verify file format and columns.';
                $this->isLessonImporting = false;
                return;
            }

            // Execute lesson import
            $results = $importService->importLessons(
                rows: $rows,
                fallbackCategoryId: $this->lessonImportCategoryId,
                duplicateStrategy: $this->lessonImportDuplicateStrategy,
                uploader: Auth::user()
            );

            $this->lessonImportResults = $results;
            $this->isLessonImporting = false;

            app(AuditLogService::class)->log(
                'bulk_lessons_imported',
                Auth::user(),
                null,
                [
                    'successful' => $results['successful'],
                    'duplicates_skipped' => $results['duplicates_skipped'],
                    'failed' => $results['failed'],
                ],
                Auth::user()
            );

            $this->successMessage = "Lesson import completed! Successfully imported {$results['successful']} lessons ({$results['duplicates_skipped']} duplicates skipped).";
        } catch (\Throwable $e) {
            $this->isLessonImporting = false;
            $this->errorMessage = "Lesson import failed: {$e->getMessage()}";
        }
    }

    public function downloadSampleLessonTemplate(string $format = 'csv')
    {
        $importService = app(DynamicContentImportService::class);

        if ($format === 'json') {
            return response()->streamDownload(function () use ($importService) {
                echo $importService->getSampleLessonJson();
            }, 'catholic_lessons_template.json', ['Content-Type' => 'application/json']);
        }

        return response()->streamDownload(function () use ($importService) {
            echo $importService->getSampleLessonCsv();
        }, 'catholic_lessons_template.csv', ['Content-Type' => 'text/csv']);
    }

    // =========================================================================
    // 10. TRANSFERS & REPORTS
    // =========================================================================
    public function approveTransfer(string $transferId)
    {
        $transfer = ParishTransfer::findOrFail($transferId);

        $user = $transfer->user;
        $oldParishId = $user->parish_id;
        $user->update(['parish_id' => $transfer->to_parish_id]);

        $transfer->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => 'Approved by Super Admin command center.',
        ]);

        app(AuditLogService::class)->log(
            'parish_transfer_approved_by_super_admin',
            $transfer,
            ['from_parish_id' => $oldParishId],
            ['to_parish_id' => $transfer->to_parish_id],
            Auth::user()
        );

        $this->successMessage = "Parish transfer for {$user->name} approved.";
    }

    public function rejectTransfer(string $transferId)
    {
        $transfer = ParishTransfer::findOrFail($transferId);

        $transfer->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => 'Transfer request declined by Diocesan leadership.',
        ]);

        app(AuditLogService::class)->log(
            'parish_transfer_rejected_by_super_admin',
            $transfer,
            ['status' => 'pending'],
            ['status' => 'rejected'],
            Auth::user()
        );

        $this->successMessage = "Parish transfer rejected.";
    }

    public function generateExecutiveReport()
    {
        $analyticsService = app(DiocesanAnalyticsService::class);
        $this->reportSummary = [
            'kpis' => $analyticsService->getDiocesanKpis(),
            'topParishes' => Parish::withCount(['users' => fn($q) => $q->where('role', 'youth')])->orderByDesc('users_count')->take(5)->get(),
            'deaneries' => $analyticsService->getDeaneryPerformance(),
        ];
        $this->showReportModal = true;
    }

    public function render()
    {
        $user = Auth::user();
        $analyticsService = app(DiocesanAnalyticsService::class);

        // 1. Live Diocesan KPIs
        $kpis = $analyticsService->getDiocesanKpis();

        // 2. Deaneries List
        $deaneries = Deanery::withCount('parishes')
            ->when($this->searchQuery && $this->activeTab === 'deaneries', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('code', 'like', "%{$this->searchQuery}%"))
            ->orderBy('name')
            ->get();

        // 3. Parishes List
        $parishes = Parish::with('deanery')
            ->withCount(['users as youth_count' => fn($q) => $q->where('role', 'youth')])
            ->when($this->selectedDeaneryFilter, fn($q) => $q->where('deanery_id', $this->selectedDeaneryFilter))
            ->when($this->searchQuery && $this->activeTab === 'parishes', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('code', 'like', "%{$this->searchQuery}%"))
            ->orderBy('name')
            ->get();

        // 4. Tracks / Categories List
        $tracks = TaxonomyTrack::withCount(['categories', 'questions'])
            ->when($this->searchQuery && $this->activeTab === 'tracks', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orderBy('display_order')
            ->get();

        // 5. Study Lessons Track Summaries
        $trackLessonSummaries = Lesson::query()
            ->select('category_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_lessons'), \Illuminate\Support\Facades\DB::raw('sum(case when status = "published" then 1 else 0 end) as published_lessons'))
            ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
            ->when($this->searchQuery && $this->activeTab === 'lessons', function ($q) {
                $q->whereHas('category', fn($c) => $c->where('name', 'like', "%{$this->searchQuery}%"));
            })
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('category_id')
            ->get();

        $totalLessonsCount = Lesson::count();
        $totalPublishedLessonsCount = Lesson::where('status', 'published')->count();

        // 6. Parish Admins List
        $admins = User::whereIn('role', ['chairperson', 'deanery_admin'])
            ->with('parish')
            ->when($this->searchQuery && $this->activeTab === 'admins', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('email', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->get();

        // 7. Youth Members List
        $youths = User::where('role', 'youth')
            ->with('parish')
            ->when($this->selectedParishFilter, fn($q) => $q->where('parish_id', $this->selectedParishFilter))
            ->when($this->searchQuery && $this->activeTab === 'youth', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('phone', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->paginate(15);

        // 8. Pending Transfers
        $pendingTransfers = ParishTransfer::where('status', 'pending')
            ->with(['user', 'fromParish', 'toParish'])
            ->latest()
            ->get();

        // 9. Questions Bank (Q&A) Track Summaries
        $trackLevelSummaries = Question::query()
            ->select('category_id', 'level', \Illuminate\Support\Facades\DB::raw('count(*) as total_questions'), \Illuminate\Support\Facades\DB::raw('sum(case when is_active = 1 then 1 else 0 end) as active_questions'))
            ->when($this->selectedCategoryFilter, fn($q) => $q->where('category_id', $this->selectedCategoryFilter))
            ->when($this->searchQuery && $this->activeTab === 'questions', function ($q) {
                $q->whereHas('category', fn($c) => $c->where('name', 'like', "%{$this->searchQuery}%"));
            })
            ->groupBy('category_id', 'level')
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('level')
            ->get();

        $totalQuestionsCount = Question::count();
        $totalActiveQuestionsCount = Question::where('is_active', true)->count();

        $categories = Category::withCount(['lessons', 'questions'])->get();

        // 10. Competitions & Rallies
        $competitions = DiocesanCompetition::with(['category', 'creator'])
            ->when($this->searchQuery && $this->activeTab === 'competitions', fn($q) => $q->where('title', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->get();

        // 11. Audit Logs
        $auditLogs = AuditLog::with('user')
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.diocese-dashboard', [
            'user' => $user,
            'kpis' => $kpis,
            'deaneries' => $deaneries,
            'parishes' => $parishes,
            'tracks' => $tracks,
            'trackLessonSummaries' => $trackLessonSummaries,
            'totalLessonsCount' => $totalLessonsCount,
            'totalPublishedLessonsCount' => $totalPublishedLessonsCount,
            'admins' => $admins,
            'youths' => $youths,
            'pendingTransfers' => $pendingTransfers,
            'trackLevelSummaries' => $trackLevelSummaries,
            'totalQuestionsCount' => $totalQuestionsCount,
            'totalActiveQuestionsCount' => $totalActiveQuestionsCount,
            'categories' => $categories,
            'competitions' => $competitions,
            'auditLogs' => $auditLogs,
        ])->layout('components.layouts.app', ['title' => 'Diocesan Command Center • Livingstone Diocese']);
    }
}
