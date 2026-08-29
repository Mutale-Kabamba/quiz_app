<?php

namespace App\Livewire;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $totalScore = QuizAttempt::where('user_id', $user->id)->sum('score');
        $totalSessions = QuizAttempt::where('user_id', $user->id)->count();

        return view('livewire.profile', [
            'user' => $user,
            'totalScore' => $totalScore,
            'totalSessions' => $totalSessions,
        ])->layout('components.layouts.app', ['title' => 'My Profile • Diocese of Livingstone']);
    }
}
