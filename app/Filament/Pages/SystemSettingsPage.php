<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Services\AuditLogService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SystemSettingsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | UnitEnum | null $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'Diocesan Configuration';
    protected string $view = 'filament.pages.system-settings-page';
    protected static ?string $title = 'Platform & Gamification Configuration';

    public string $dioceseName = 'Diocese of Livingstone';
    public string $motto = 'Catholic Youth Ministry Formation & Quiz Arena';
    public int $xpLesson = 20;
    public int $xpChallenge = 50;
    public int $xpFlashcard = 15;
    public int $xpRankedQuiz = 100;
    public bool $openRegistration = true;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->dioceseName = SystemSetting::get('diocese_name', 'Diocese of Livingstone');
        $this->motto = SystemSetting::get('diocese_motto', 'Catholic Youth Ministry Formation & Quiz Arena');
        $this->xpLesson = (int) SystemSetting::get('xp_lesson', 20);
        $this->xpChallenge = (int) SystemSetting::get('xp_challenge', 50);
        $this->xpFlashcard = (int) SystemSetting::get('xp_flashcard', 15);
        $this->xpRankedQuiz = (int) SystemSetting::get('xp_ranked_quiz', 100);
        $this->openRegistration = (bool) SystemSetting::get('open_registration', true);
    }

    public function saveSettings(): void
    {
        SystemSetting::set('diocese_name', $this->dioceseName, 'general', 'Diocese Name');
        SystemSetting::set('diocese_motto', $this->motto, 'general', 'Motto');
        SystemSetting::set('xp_lesson', $this->xpLesson, 'gamification', 'XP for Lesson completion');
        SystemSetting::set('xp_challenge', $this->xpChallenge, 'gamification', 'XP for Daily Challenge');
        SystemSetting::set('xp_flashcard', $this->xpFlashcard, 'gamification', 'XP for Flashcard mastery');
        SystemSetting::set('xp_ranked_quiz', $this->xpRankedQuiz, 'gamification', 'XP for Ranked Quiz win');
        SystemSetting::set('open_registration', $this->openRegistration, 'general', 'Open Youth Registration');

        app(AuditLogService::class)->log(
            'system_settings_updated',
            null,
            null,
            [
                'diocese_name' => $this->dioceseName,
                'xp_lesson' => $this->xpLesson,
                'xp_challenge' => $this->xpChallenge,
            ]
        );

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}
