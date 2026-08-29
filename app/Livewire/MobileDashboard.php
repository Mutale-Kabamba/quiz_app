<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MobileDashboard extends Component
{
    public string $rallyPin = '';
    public bool $showRallyModal = false;

    public function joinLiveRally()
    {
        $this->validate([
            'rallyPin' => 'required|numeric|digits:6',
        ]);

        // Route to live rally arena session
        return redirect()->to('/rally/' . $this->rallyPin);
    }

    public function render()
    {
        $user = Auth::user();
        $categories = Category::withCount('questions')->orderBy('display_order')->get();

        $recentAttempts = $user ? QuizAttempt::where('user_id', $user->id)
            ->latest('completed_at')
            ->take(3)
            ->get() : collect();

        // Chairperson scoped statistics
        $chairpersonStats = null;
        if ($user && ($user->isChairperson() || $user->isSuperAdmin())) {
            $chairpersonStats = [
                'pending_approvals' => User::where('role', 'youth')
                    ->where('status', 'pending')
                    ->when($user->isChairperson(), fn($q) => $q->where('parish_id', $user->parish_id))
                    ->count(),
                'total_parish_youth' => User::where('role', 'youth')
                    ->when($user->isChairperson(), fn($q) => $q->where('parish_id', $user->parish_id))
                    ->count(),
                'active_this_week' => QuizAttempt::whereHas('user', function ($q) use ($user) {
                        if ($user->isChairperson()) {
                            $q->where('parish_id', $user->parish_id);
                        }
                    })
                    ->where('completed_at', '>=', now()->subDays(7))
                    ->count(),
            ];
        }

        return view('livewire.mobile-dashboard', [
            'user' => $user,
            'categories' => $categories,
            'recentAttempts' => $recentAttempts,
            'chairpersonStats' => $chairpersonStats,
        ])->layout('components.layouts.app', ['title' => 'Youth Dashboard • Diocese of Livingstone']);
    }
}
