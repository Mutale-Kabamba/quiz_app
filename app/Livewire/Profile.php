<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\FlashcardReview;
use App\Models\LessonProgress;
use App\Models\Parish;
use App\Models\ParishTransfer;
use App\Models\QuizAttempt;
use App\Models\UserTopicMastery;
use App\Services\AuditLogService;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    // Modals
    public bool $showAvatarModal = false;
    public bool $showEditProfileModal = false;
    public bool $showPasswordModal = false;
    public bool $showParishModal = false;
    public bool $showPreferencesModal = false;
    public bool $showDeactivateModal = false;

    // Avatar Upload
    public $avatarFile = null;

    // Edit Profile Fields
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public ?string $dob = null;
    public string $gender = '';

    // Password Fields
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    // Parish Transfer Fields
    public ?int $targetParishId = null;
    public string $transferReason = '';

    // Preferences & Privacy Fields
    public bool $notifyFormation = true;
    public bool $notifyStudy = true;
    public bool $notifyReviews = true;
    public bool $notifyQuizzes = true;
    public bool $notifyCompetitions = true;
    public bool $notifyParish = true;
    public string $theme = 'system';
    public bool $showAvatarInRankings = true;
    public bool $showNameInRankings = true;
    public bool $showParishInRankings = true;
    public bool $biometricEnabled = false;

    // Notifications / Toasts
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->loadUserData($user);
        }
    }

    protected function loadUserData($user)
    {
        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->email = $user->email ?? '';
        $this->dob = $user->dob ? $user->dob->format('Y-m-d') : null;
        $this->gender = $user->gender ?? '';
        $this->biometricEnabled = $user->hasBiometricEnabled();

        $prefs = $user->preferences ?? [];
        $this->notifyFormation = $prefs['notifications']['formation'] ?? true;
        $this->notifyStudy = $prefs['notifications']['study'] ?? true;
        $this->notifyReviews = $prefs['notifications']['reviews'] ?? true;
        $this->notifyQuizzes = $prefs['notifications']['quizzes'] ?? true;
        $this->notifyCompetitions = $prefs['notifications']['competitions'] ?? true;
        $this->notifyParish = $prefs['notifications']['parish'] ?? true;
        $this->theme = $prefs['appearance']['theme'] ?? 'system';
        $this->showAvatarInRankings = $prefs['privacy']['show_avatar'] ?? true;
        $this->showNameInRankings = $prefs['privacy']['show_name'] ?? true;
        $this->showParishInRankings = $prefs['privacy']['show_parish'] ?? true;
    }

    public function updatedAvatarFile()
    {
        $this->validate([
            'avatarFile' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    }

    public function uploadAvatar()
    {
        $user = Auth::user();
        $this->validate([
            'avatarFile' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $this->avatarFile->store('avatars', 'public');
        $oldPath = $user->avatar_path;
        $user->update(['avatar_path' => $path]);

        app(AuditLogService::class)->log(
            'profile_avatar_updated',
            $user,
            ['avatar_path' => $oldPath],
            ['avatar_path' => $path],
            $user
        );

        $this->avatarFile = null;
        $this->showAvatarModal = false;
        $this->successMessage = 'Profile photo successfully updated.';
    }

    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $oldPath = $user->avatar_path;
        $user->update(['avatar_path' => null]);

        app(AuditLogService::class)->log(
            'profile_avatar_removed',
            $user,
            ['avatar_path' => $oldPath],
            ['avatar_path' => null],
            $user
        );

        $this->showAvatarModal = false;
        $this->successMessage = 'Profile photo removed.';
    }

    public function saveProfile()
    {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|min:2|max:100',
            'phone' => [
                'required',
                'string',
                'min:8',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        $oldValues = [
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'dob' => $user->dob,
            'gender' => $user->gender,
        ];

        $user->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'dob' => $this->dob ?: null,
            'gender' => $this->gender ?: null,
        ]);

        app(AuditLogService::class)->log(
            'profile_info_updated',
            $user,
            $oldValues,
            [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'dob' => $this->dob,
                'gender' => $this->gender,
            ],
            $user
        );

        $this->showEditProfileModal = false;
        $this->successMessage = 'Personal details successfully updated.';
    }

    public function changePassword()
    {
        $user = Auth::user();

        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6',
            'newPasswordConfirmation' => 'required|same:newPassword',
        ]);

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Current password does not match our records.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
            'last_password_changed_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            'profile_password_changed',
            $user,
            null,
            ['last_password_changed_at' => now()],
            $user
        );

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->showPasswordModal = false;
        $this->successMessage = 'Account password successfully updated.';
    }

    public function enableBiometrics(?string $credentialId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $rawToken = $user->generateBiometricToken($credentialId);
        $this->biometricEnabled = true;

        app(AuditLogService::class)->log(
            'profile_biometrics_enabled',
            $user,
            null,
            ['enabled_at' => now(), 'has_credential_id' => !empty($credentialId)],
            $user
        );

        $this->dispatch('biometric-enrolled-on-device', [
            'userId' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'parish' => $user->parish?->name,
            'token' => $rawToken,
            'credentialId' => $credentialId,
        ]);

        $this->successMessage = 'Biometric sign-in (Face ID / Fingerprint) successfully registered for this device.';
    }

    public function disableBiometrics()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $user->disableBiometrics();
        $this->biometricEnabled = false;

        app(AuditLogService::class)->log(
            'profile_biometrics_disabled',
            $user,
            null,
            ['disabled_at' => now()],
            $user
        );

        $this->dispatch('biometric-revoked-on-device');
        $this->successMessage = 'Biometric sign-in has been disabled for this account.';
    }

    public function requestParishTransfer()
    {
        $user = Auth::user();

        $this->validate([
            'targetParishId' => [
                'required',
                'exists:parishes,id',
                Rule::notIn([$user->parish_id]),
            ],
            'transferReason' => 'required|string|min:5|max:300',
        ]);

        $existingPending = ParishTransfer::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->addError('targetParishId', 'You already have a pending parish change request awaiting review.');
            return;
        }

        $transfer = ParishTransfer::create([
            'user_id' => $user->id,
            'from_parish_id' => $user->parish_id,
            'to_parish_id' => $this->targetParishId,
            'requested_by' => $user->id,
            'reason' => $this->transferReason,
            'status' => 'pending',
        ]);

        app(AuditLogService::class)->log(
            'parish_transfer_requested_by_youth',
            $transfer,
            ['from_parish_id' => $user->parish_id],
            ['to_parish_id' => $this->targetParishId, 'reason' => $this->transferReason],
            $user
        );

        $this->targetParishId = null;
        $this->transferReason = '';
        $this->showParishModal = false;
        $this->successMessage = 'Parish change request submitted for Parish Chairperson approval.';
    }

    public function savePreferences()
    {
        $user = Auth::user();

        $preferences = [
            'notifications' => [
                'formation' => $this->notifyFormation,
                'study' => $this->notifyStudy,
                'reviews' => $this->notifyReviews,
                'quizzes' => $this->notifyQuizzes,
                'competitions' => $this->notifyCompetitions,
                'parish' => $this->notifyParish,
            ],
            'appearance' => [
                'theme' => $this->theme,
            ],
            'privacy' => [
                'show_avatar' => $this->showAvatarInRankings,
                'show_name' => $this->showNameInRankings,
                'show_parish' => $this->showParishInRankings,
            ],
        ];

        $user->update(['preferences' => $preferences]);

        app(AuditLogService::class)->log(
            'profile_preferences_updated',
            $user,
            null,
            $preferences,
            $user
        );

        $this->showPreferencesModal = false;
        $this->successMessage = 'Account preferences successfully updated.';
    }

    public function deactivateAccount()
    {
        $user = Auth::user();

        $user->update([
            'deactivated_at' => now(),
            'status' => 'pending',
        ]);

        app(AuditLogService::class)->log(
            'profile_account_deactivated',
            $user,
            null,
            ['deactivated_at' => now()],
            $user
        );

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        session()->flash('message', 'Your account has been deactivated. You may contact your Parish Chairperson to reactivate.');
        return redirect()->to('/login');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/login');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $gamificationService = app(GamificationService::class);

        // Learning Metrics
        $totalScore = QuizAttempt::where('user_id', $user->id)->sum('score');
        $totalQuizzes = QuizAttempt::where('user_id', $user->id)->count();
        $completedLessonsCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
        $masteredFlashcardsCount = FlashcardReview::where('user_id', $user->id)->where('rating', 3)->count();

        // Topic Mastery Average
        $masteryScores = UserTopicMastery::where('user_id', $user->id)->pluck('mastery_score');
        $averageMastery = $masteryScores->isNotEmpty() ? (int) round($masteryScores->avg()) : 65;

        // Level & XP Progress
        $currentLevel = $user->level ?? 1;
        $currentXp = $user->xp ?? 0;
        $currentBaseline = $gamificationService->getCurrentLevelBaseline($currentLevel);
        $nextThreshold = $gamificationService->getNextLevelThreshold($currentLevel);
        $levelXpSpan = max(1, $nextThreshold - $currentBaseline);
        $levelProgressPercentage = min(100, (int) round((($currentXp - $currentBaseline) / $levelXpSpan) * 100));

        // Achievements (Unlocked vs Locked)
        $unlockedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();
        $allAchievements = Achievement::all();

        // Bookmarked Lessons
        $bookmarkedLessons = LessonProgress::where('user_id', $user->id)
            ->where('is_bookmarked', true)
            ->with('lesson.category')
            ->get()
            ->pluck('lesson');

        // All Parishes for transfer selector
        $parishes = Parish::orderBy('name')->get();

        // Latest Transfer Request
        $pendingTransfer = $user->latestPendingTransfer;

        return view('livewire.profile', [
            'user' => $user,
            'totalScore' => $totalScore,
            'totalQuizzes' => $totalQuizzes,
            'completedLessonsCount' => $completedLessonsCount,
            'masteredFlashcardsCount' => $masteredFlashcardsCount,
            'averageMastery' => $averageMastery,
            'currentLevel' => $currentLevel,
            'currentXp' => $currentXp,
            'nextThreshold' => $nextThreshold,
            'levelProgressPercentage' => $levelProgressPercentage,
            'allAchievements' => $allAchievements,
            'unlockedAchievementIds' => $unlockedAchievementIds,
            'bookmarkedLessons' => $bookmarkedLessons,
            'parishes' => $parishes,
            'pendingTransfer' => $pendingTransfer,
        ])->layout('components.layouts.app', ['title' => 'My Profile • Diocese of Livingstone']);
    }
}
