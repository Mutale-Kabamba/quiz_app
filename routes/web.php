<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\ChairpersonApproval;
use App\Livewire\LeaderboardView;
use App\Livewire\MobileDashboard;
use App\Livewire\Profile;
use App\Livewire\QuizRunner;
use App\Livewire\StudyHub;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes (Entry Screen)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Authenticated Mobile Application Routes (Protected - Login required)
Route::middleware('auth')->group(function () {
    Route::get('/', MobileDashboard::class)->name('home');
    Route::get('/quiz/{categoryId?}', QuizRunner::class)->name('quiz.runner');
    Route::get('/leaderboard', LeaderboardView::class)->name('leaderboard');
    Route::get('/study/{selectedCategoryId?}', StudyHub::class)->name('study.hub');
    Route::get('/approvals', ChairpersonApproval::class)->name('chairperson.approvals');
    Route::get('/profile', Profile::class)->name('profile');
});
