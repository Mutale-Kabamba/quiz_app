<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Deanery;
use App\Models\DiocesanCompetition;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\DiocesanAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class DioceseDashboard extends Component
{
    // Active Tab
    public string $activeTab = 'overview'; // 'overview', 'parishes', 'admins', 'youth', 'questions', 'competitions', 'reports'

    // Search & Filters
    public string $searchQuery = '';
    public ?int $selectedParishFilter = null;

    // Modals
    public bool $showParishModal = false;
    public bool $showAdminModal = false;
    public bool $showQuestionModal = false;
    public bool $showCompetitionModal = false;
    public bool $showReportModal = false;

    // New Parish Form
    public string $newParishName = '';
    public string $newParishCode = '';
    public ?int $newParishDeaneryId = null;
    public string $newParishLocation = '';
    public string $newParishEmail = '';
    public string $newParishPhone = '';

    // New Parish Admin Form
    public string $newAdminName = '';
    public string $newAdminPhone = '';
    public string $newAdminEmail = '';
    public string $newAdminPassword = 'password';
    public ?int $newAdminParishId = null;

    // New Question Form
    public ?int $newQuestionCategoryId = null;
    public string $newQuestionText = '';
    public string $optionA = '';
    public string $optionB = '';
    public string $optionC = '';
    public string $optionD = '';
    public string $correctOption = 'A';
    public string $newQuestionExplanation = '';
    public string $newQuestionCitation = '';
    public int $newQuestionLevel = 1;

    // New Competition Form
    public string $newCompTitle = '';
    public string $newCompDescription = '';
    public string $newCompType = 'diocesan';
    public ?int $newCompCategoryId = null;
    public string $newCompStartTime = '';
    public string $newCompEndTime = '';
    public int $newCompTimeLimit = 300;
    public int $newCompQuestionCount = 15;

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
    }

    public function createParish()
    {
        $this->validate([
            'newParishName' => 'required|string|min:3|max:100',
            'newParishCode' => 'required|string|min:2|max:20|unique:parishes,code',
            'newParishDeaneryId' => 'required|exists:deaneries,id',
            'newParishLocation' => 'nullable|string|max:100',
            'newParishEmail' => 'nullable|email|max:100',
            'newParishPhone' => 'nullable|string|max:20',
        ]);

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

        $this->reset(['newParishName', 'newParishCode', 'newParishDeaneryId', 'newParishLocation', 'newParishEmail', 'newParishPhone', 'showParishModal']);
        $this->successMessage = "Parish '{$parish->name}' registered successfully!";
    }

    public function createParishAdmin()
    {
        $this->validate([
            'newAdminName' => 'required|string|min:3|max:100',
            'newAdminPhone' => 'required|string|min:8|max:20|unique:users,phone',
            'newAdminEmail' => 'nullable|email|max:100|unique:users,email',
            'newAdminPassword' => 'required|string|min:6',
            'newAdminParishId' => 'required|exists:parishes,id',
        ]);

        $admin = User::create([
            'parish_id' => $this->newAdminParishId,
            'name' => $this->newAdminName,
            'phone' => $this->newAdminPhone,
            'email' => $this->newAdminEmail ?: null,
            'password' => Hash::make($this->newAdminPassword),
            'role' => 'chairperson',
            'status' => 'approved',
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

        $this->reset(['newAdminName', 'newAdminPhone', 'newAdminEmail', 'newAdminPassword', 'newAdminParishId', 'showAdminModal']);
        $this->successMessage = "Parish Chairperson '{$admin->name}' successfully created and assigned.";
    }

    public function createQuestion()
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

        $options = [
            'A' => $this->optionA,
            'B' => $this->optionB,
            'C' => $this->optionC,
            'D' => $this->optionD,
        ];

        $question = Question::create([
            'category_id' => $this->newQuestionCategoryId,
            'level' => $this->newQuestionLevel,
            'question_text' => $this->newQuestionText,
            'options' => $options,
            'correct_option_key' => $this->correctOption,
            'explanation' => $this->newQuestionExplanation,
            'reference_citation' => $this->newQuestionCitation ?: 'CCC & Scripture',
            'is_active' => true,
        ]);

        app(AuditLogService::class)->log(
            'question_created_in_bank',
            $question,
            null,
            ['question_text' => $this->newQuestionText, 'category_id' => $this->newQuestionCategoryId],
            Auth::user()
        );

        $this->reset(['newQuestionCategoryId', 'newQuestionText', 'optionA', 'optionB', 'optionC', 'optionD', 'newQuestionExplanation', 'newQuestionCitation', 'showQuestionModal']);
        $this->successMessage = 'New formation question successfully published to universal question bank!';
    }

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

    public function createCompetition()
    {
        $this->validate([
            'newCompTitle' => 'required|string|min:4|max:120',
            'newCompDescription' => 'required|string|min:10',
            'newCompType' => 'required|in:diocesan,deanery,parish,youth_rally',
            'newCompStartTime' => 'required|date',
            'newCompEndTime' => 'required|date|after:newCompStartTime',
        ]);

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

        app(AuditLogService::class)->log(
            'diocesan_competition_created',
            $competition,
            null,
            ['title' => $this->newCompTitle, 'type' => $this->newCompType],
            Auth::user()
        );

        $this->reset(['newCompTitle', 'newCompDescription', 'showCompetitionModal']);
        $this->successMessage = "Diocesan Competition '{$competition->title}' successfully scheduled!";
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
        $deaneries = Deanery::withCount('parishes')->get();

        // 2. Parishes List
        $parishes = Parish::with('deanery')
            ->withCount(['users as youth_count' => fn($q) => $q->where('role', 'youth')])
            ->when($this->searchQuery && $this->activeTab === 'parishes', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orderBy('name')
            ->get();

        // 3. Parish Admins List
        $admins = User::whereIn('role', ['chairperson', 'deanery_admin'])
            ->with('parish')
            ->when($this->searchQuery && $this->activeTab === 'admins', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('email', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->get();

        // 4. Youth Members List
        $youths = User::where('role', 'youth')
            ->with('parish')
            ->when($this->selectedParishFilter, fn($q) => $q->where('parish_id', $this->selectedParishFilter))
            ->when($this->searchQuery && $this->activeTab === 'youth', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('phone', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->paginate(15);

        // 5. Pending Transfers
        $pendingTransfers = ParishTransfer::where('status', 'pending')
            ->with(['user', 'fromParish', 'toParish'])
            ->latest()
            ->get();

        // 6. Questions
        $questions = Question::with('category')
            ->when($this->searchQuery && $this->activeTab === 'questions', fn($q) => $q->where('question_text', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->take(20)
            ->get();

        $categories = Category::withCount(['lessons', 'questions'])->get();

        // 7. Competitions
        $competitions = DiocesanCompetition::with(['category', 'creator'])
            ->latest()
            ->get();

        // 8. Audit Logs
        $auditLogs = AuditLog::with('user')
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.diocese-dashboard', [
            'user' => $user,
            'kpis' => $kpis,
            'deaneries' => $deaneries,
            'parishes' => $parishes,
            'admins' => $admins,
            'youths' => $youths,
            'pendingTransfers' => $pendingTransfers,
            'questions' => $questions,
            'categories' => $categories,
            'competitions' => $competitions,
            'auditLogs' => $auditLogs,
        ])->layout('components.layouts.app', ['title' => 'Diocesan Command Center • Livingstone Diocese']);
    }
}
