<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Parish;
use App\Models\ParishAnnouncement;
use App\Models\ParishCompetition;
use App\Models\ParishEvent;
use App\Models\ParishFormationChallenge;
use App\Models\ParishTransfer;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserTopicMastery;
use App\Services\AuditLogService;
use App\Services\ParishDashboardService;
use App\Services\ParishMonthlyReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ParishAdminDashboard extends Component
{
    use WithPagination;

    // Active Navigation Tab
    public string $activeTab = 'overview'; // 'overview', 'youth', 'leaderboard', 'challenges', 'communication', 'activities', 'reports'

    // Search & Filters
    public string $searchQuery = '';

    // Modals
    public bool $showAddYouthModal = false;
    public bool $showTransferModal = false;
    public bool $showChallengeModal = false;
    public bool $showAnnouncementModal = false;
    public bool $showEventModal = false;
    public bool $showQuizModal = false;
    public bool $showReportModal = false;

    // Add Youth Form
    public string $newYouthName = '';
    public string $newYouthPhone = '';
    public string $newYouthEmail = '';
    public string $newYouthPassword = 'password123';

    // Transfer Request Form
    public ?string $transferUserId = null;
    public ?int $transferToParishId = null;
    public string $transferReason = '';

    // Challenge Form
    public string $challengeTitle = '';
    public string $challengeDescription = '';
    public string $challengeType = 'collective_xp';
    public int $challengeTarget = 5000;
    public int $challengeXpReward = 150;
    public string $challengeEndDate = '';

    // Announcement Form
    public string $announcementTitle = '';
    public string $announcementContent = '';
    public string $announcementPriority = 'normal';

    // Event Form
    public string $eventTitle = '';
    public string $eventDescription = '';
    public string $eventLocation = 'Parish Hall';
    public string $eventDate = '';

    // Competition Form
    public string $quizTitle = '';
    public string $quizDescription = '';
    public ?int $quizCategoryId = null;
    public int $quizTimeLimit = 300;
    public string $quizStartTime = '';
    public string $quizEndTime = '';

    // Monthly Report Data
    public ?array $monthlyReportData = null;

    // Feedback Notifications
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount()
    {
        $user = Auth::user();
        if (!$user || (!$user->isChairperson() && !$user->isSuperAdmin())) {
            abort(403, 'Unauthorized access to Parish Administration dashboard.');
        }

        $this->challengeEndDate = now()->addDays(7)->format('Y-m-d');
        $this->eventDate = now()->addDays(3)->format('Y-m-d');
        $this->quizStartTime = now()->addDays(1)->format('Y-m-d\TH:i');
        $this->quizEndTime = now()->addDays(5)->format('Y-m-d\TH:i');
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function approveYouth(string $userId)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($userId);

        if ($currentUser->isChairperson() && $user->parish_id !== $currentUser->parish_id) {
            abort(403, 'Unauthorized. You may only approve youth belonging to your assigned parish.');
        }

        $user->update([
            'status' => 'approved',
            'approved_by' => $currentUser->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        app(AuditLogService::class)->log(
            'parish_youth_approved',
            $user,
            ['status' => 'pending'],
            ['status' => 'approved'],
            $currentUser
        );

        $this->successMessage = "{$user->name} has been verified and approved.";
    }

    public function rejectYouth(string $userId)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($userId);

        if ($currentUser->isChairperson() && $user->parish_id !== $currentUser->parish_id) {
            abort(403, 'Unauthorized. You may only reject youth belonging to your assigned parish.');
        }

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => 'Declined by parish leadership.',
        ]);

        app(AuditLogService::class)->log(
            'parish_youth_rejected',
            $user,
            ['status' => 'pending'],
            ['status' => 'rejected'],
            $currentUser
        );

        $this->successMessage = "Registration for {$user->name} rejected.";
    }

    public function toggleYouthStatus(string $userId)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($userId);

        if ($currentUser->isChairperson() && $user->parish_id !== $currentUser->parish_id) {
            abort(403, 'Unauthorized.');
        }

        $newStatus = $user->status === 'suspended' ? 'approved' : 'suspended';
        $user->update(['status' => $newStatus]);

        app(AuditLogService::class)->log(
            'parish_youth_status_toggled',
            $user,
            ['status' => $user->getOriginal('status')],
            ['status' => $newStatus],
            $currentUser
        );

        $this->successMessage = "Youth status for {$user->name} updated to {$newStatus}.";
    }

    public function addYouth()
    {
        $currentUser = Auth::user();
        $parishId = $currentUser->parish_id ?? Parish::first()?->id;

        $this->validate([
            'newYouthName' => 'required|string|min:3|max:100',
            'newYouthPhone' => 'required|string|min:8|max:20|unique:users,phone',
            'newYouthEmail' => 'nullable|email|max:100|unique:users,email',
            'newYouthPassword' => 'required|string|min:6',
        ]);

        $youth = User::create([
            'parish_id' => $parishId,
            'name' => $this->newYouthName,
            'phone' => $this->newYouthPhone,
            'email' => $this->newYouthEmail ?: null,
            'password' => Hash::make($this->newYouthPassword),
            'role' => 'youth',
            'status' => 'approved',
            'approved_by' => $currentUser->id,
            'approved_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            'parish_youth_created_by_admin',
            $youth,
            null,
            ['name' => $this->newYouthName, 'parish_id' => $parishId],
            $currentUser
        );

        $this->reset(['newYouthName', 'newYouthPhone', 'newYouthEmail', 'newYouthPassword', 'showAddYouthModal']);
        $this->successMessage = "Youth member '{$youth->name}' registered and activated successfully.";
    }

    public function requestTransfer()
    {
        $currentUser = Auth::user();
        $this->validate([
            'transferUserId' => 'required|exists:users,id',
            'transferToParishId' => 'required|exists:parishes,id',
            'transferReason' => 'required|string|min:6|max:255',
        ]);

        $user = User::findOrFail($this->transferUserId);

        $transfer = ParishTransfer::create([
            'user_id' => $user->id,
            'from_parish_id' => $user->parish_id,
            'to_parish_id' => $this->transferToParishId,
            'requested_by' => $currentUser->id,
            'reason' => $this->transferReason,
            'status' => 'pending',
        ]);

        app(AuditLogService::class)->log(
            'parish_transfer_requested_by_chairperson',
            $transfer,
            null,
            ['user_id' => $user->id, 'to_parish_id' => $this->transferToParishId],
            $currentUser
        );

        $this->reset(['transferUserId', 'transferToParishId', 'transferReason', 'showTransferModal']);
        $this->successMessage = "Transfer request for {$user->name} submitted to Diocesan Administration.";
    }

    public function createChallenge()
    {
        $user = Auth::user();
        $parishId = $user->parish_id ?? Parish::first()?->id;

        $this->validate([
            'challengeTitle' => 'required|string|min:4|max:100',
            'challengeDescription' => 'required|string|min:10',
            'challengeTarget' => 'required|integer|min:100',
            'challengeXpReward' => 'required|integer|min:20',
            'challengeEndDate' => 'required|date|after:today',
        ]);

        $challenge = ParishFormationChallenge::create([
            'parish_id' => $parishId,
            'title' => $this->challengeTitle,
            'description' => $this->challengeDescription,
            'challenge_type' => $this->challengeType,
            'target_value' => $this->challengeTarget,
            'current_value' => 0,
            'xp_reward' => $this->challengeXpReward,
            'starts_at' => now(),
            'ends_at' => $this->challengeEndDate,
            'is_active' => true,
        ]);

        app(AuditLogService::class)->log(
            'parish_challenge_created',
            $challenge,
            null,
            ['title' => $this->challengeTitle, 'parish_id' => $parishId],
            $user
        );

        $this->reset(['challengeTitle', 'challengeDescription', 'showChallengeModal']);
        $this->successMessage = 'New Parish Formation Challenge successfully launched!';
    }

    public function postAnnouncement()
    {
        $user = Auth::user();
        $parishId = $user->parish_id ?? Parish::first()?->id;

        $this->validate([
            'announcementTitle' => 'required|string|min:4|max:100',
            'announcementContent' => 'required|string|min:10',
        ]);

        $announcement = ParishAnnouncement::create([
            'parish_id' => $parishId,
            'author_id' => $user->id,
            'title' => $this->announcementTitle,
            'content' => $this->announcementContent,
            'priority' => $this->announcementPriority,
            'is_published' => true,
            'published_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            'parish_announcement_posted',
            $announcement,
            null,
            ['title' => $this->announcementTitle, 'parish_id' => $parishId],
            $user
        );

        $this->reset(['announcementTitle', 'announcementContent', 'showAnnouncementModal']);
        $this->successMessage = 'Parish announcement successfully published to youth!';
    }

    public function scheduleEvent()
    {
        $user = Auth::user();
        $parishId = $user->parish_id ?? Parish::first()?->id;

        $this->validate([
            'eventTitle' => 'required|string|min:3|max:100',
            'eventDescription' => 'required|string|min:8',
            'eventLocation' => 'required|string|max:100',
            'eventDate' => 'required|date',
        ]);

        $event = ParishEvent::create([
            'parish_id' => $parishId,
            'created_by' => $user->id,
            'title' => $this->eventTitle,
            'description' => $this->eventDescription,
            'event_type' => 'formation',
            'event_date' => $this->eventDate,
            'location' => $this->eventLocation,
            'organizer' => $user->name,
            'status' => 'scheduled',
        ]);

        app(AuditLogService::class)->log(
            'parish_event_scheduled',
            $event,
            null,
            ['title' => $this->eventTitle, 'parish_id' => $parishId],
            $user
        );

        $this->reset(['eventTitle', 'eventDescription', 'showEventModal']);
        $this->successMessage = 'Parish activity/event scheduled successfully!';
    }

    public function createParishQuiz()
    {
        $user = Auth::user();
        $parishId = $user->parish_id ?? Parish::first()?->id;

        $this->validate([
            'quizTitle' => 'required|string|min:4|max:100',
            'quizDescription' => 'required|string|min:8',
            'quizStartTime' => 'required|date',
            'quizEndTime' => 'required|date|after:quizStartTime',
        ]);

        $comp = ParishCompetition::create([
            'parish_id' => $parishId,
            'created_by' => $user->id,
            'title' => $this->quizTitle,
            'description' => $this->quizDescription,
            'rally_pin' => (string) random_int(100000, 999999),
            'category_id' => $this->quizCategoryId ?: Category::first()?->id,
            'level' => 1,
            'time_limit_seconds' => $this->quizTimeLimit,
            'question_count' => 10,
            'status' => 'active',
            'start_time' => $this->quizStartTime,
            'end_time' => $this->quizEndTime,
        ]);

        app(AuditLogService::class)->log(
            'parish_competition_created',
            $comp,
            null,
            ['title' => $this->quizTitle, 'parish_id' => $parishId],
            $user
        );

        $this->reset(['quizTitle', 'quizDescription', 'showQuizModal']);
        $this->successMessage = "Parish Quiz '{$comp->title}' (PIN: {$comp->rally_pin}) successfully scheduled!";
    }

    public function generateReport()
    {
        $user = Auth::user();
        $parishId = $user->parish_id ?? Parish::first()?->id;

        $reportService = app(ParishMonthlyReportService::class);
        $this->monthlyReportData = $reportService->generateMonthlySummary($parishId, now()->month, now()->year);
        $this->showReportModal = true;
    }

    public function render()
    {
        $user = Auth::user();
        $parish = $user->parish ?? Parish::first();

        $dashboardService = app(ParishDashboardService::class);
        $kpis = $dashboardService->getParishKpis($parish->id);
        $formationHealth = $dashboardService->getFormationHealth($parish->id);
        $attentionYouth = $dashboardService->getAttentionRequiredYouth($parish->id);

        // 1. Parish Youth Roster
        $youths = User::where('role', 'youth')
            ->where('parish_id', $parish->id)
            ->when($this->searchQuery && $this->activeTab === 'youth', fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%")->orWhere('phone', 'like', "%{$this->searchQuery}%"))
            ->latest()
            ->paginate(15);

        // 2. Pending Approvals
        $pendingYouths = User::where('role', 'youth')
            ->where('status', 'pending')
            ->where('parish_id', $parish->id)
            ->latest()
            ->get();

        // 3. Leaderboard
        $leaderboard = User::where('role', 'youth')
            ->where('parish_id', $parish->id)
            ->where('status', 'approved')
            ->orderByDesc('xp')
            ->take(10)
            ->get();

        // 4. Categories & Topic Mastery
        $categories = Category::withCount('questions')->get();

        // 5. Challenges
        $activeChallenges = ParishFormationChallenge::where('parish_id', $parish->id)
            ->where('is_active', true)
            ->get();

        // 6. Announcements
        $announcements = ParishAnnouncement::where('parish_id', $parish->id)
            ->latest()
            ->get();

        // 7. Events
        $events = ParishEvent::where('parish_id', $parish->id)
            ->latest()
            ->get();

        // 8. Competitions
        $parishCompetitions = ParishCompetition::where('parish_id', $parish->id)
            ->latest()
            ->get();

        // All Parishes for transfer dropdown
        $allParishes = Parish::where('id', '!=', $parish->id)->orderBy('name')->get();

        return view('livewire.parish-admin-dashboard', [
            'user' => $user,
            'parish' => $parish,
            'kpis' => $kpis,
            'formationHealth' => $formationHealth,
            'attentionYouth' => $attentionYouth,
            'youths' => $youths,
            'pendingYouths' => $pendingYouths,
            'leaderboard' => $leaderboard,
            'categories' => $categories,
            'activeChallenges' => $activeChallenges,
            'announcements' => $announcements,
            'events' => $events,
            'parishCompetitions' => $parishCompetitions,
            'allParishes' => $allParishes,
        ])->layout('components.layouts.app', ['title' => "Parish Administration • {$parish->name}"]);
    }
}
