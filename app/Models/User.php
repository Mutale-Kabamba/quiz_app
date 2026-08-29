<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'parish_id',
        'name',
        'phone',
        'email',
        'password',
        'role',
        'status',
        'xp',
        'level',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'avatar_path',
        'dob',
        'gender',
        'preferences',
        'last_password_changed_at',
        'deactivated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'last_activity_date' => 'date',
            'dob' => 'date',
            'last_password_changed_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'preferences' => 'array',
            'xp' => 'integer',
            'level' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            if (Storage::disk('public')->exists($this->avatar_path)) {
                return asset('storage/' . $this->avatar_path);
            }
            if (file_exists(public_path('storage/' . $this->avatar_path))) {
                return asset('storage/' . $this->avatar_path);
            }
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }

        return $initials ?: 'Y';
    }

    public function getProfileCompletionPercentageAttribute(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'email' => !empty($this->email),
            'phone' => !empty($this->phone),
            'parish' => !empty($this->parish_id),
            'avatar' => !empty($this->avatar_path),
            'dob' => !empty($this->dob),
        ];

        $completed = count(array_filter($fields));
        return (int) round(($completed / count($fields)) * 100);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isChairperson(): bool
    {
        return $this->role === 'chairperson';
    }

    public function isYouth(): bool
    {
        return $this->role === 'youth';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDeactivated(): bool
    {
        return !is_null($this->deactivated_at);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'deanery_admin', 'chairperson']);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function questionReports(): HasMany
    {
        return $this->hasMany(QuestionReport::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function flashcardReviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    public function challengeParticipations(): HasMany
    {
        return $this->hasMany(UserChallengeParticipation::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function parishTransfers(): HasMany
    {
        return $this->hasMany(ParishTransfer::class);
    }

    public function latestPendingTransfer(): HasOne
    {
        return $this->hasOne(ParishTransfer::class)->where('status', 'pending')->latestOfMany();
    }
}
