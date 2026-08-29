<?php

use App\Livewire\ArenaHub;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\ChairpersonApproval;
use App\Livewire\FlashcardArena;
use App\Livewire\LeaderboardView;
use App\Livewire\LessonViewer;
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
    Route::get('/lesson/{lesson}', LessonViewer::class)->name('lesson.show');
    Route::get('/flashcards/{categoryId?}', FlashcardArena::class)->name('flashcards.arena');
    Route::get('/quiz', ArenaHub::class)->name('arena.hub');
    Route::get('/quiz/play/{categoryId?}', QuizRunner::class)->name('quiz.runner');
    Route::get('/leaderboard', LeaderboardView::class)->name('leaderboard');
    Route::get('/study/{selectedCategoryId?}', StudyHub::class)->name('study.hub');
    Route::get('/approvals', ChairpersonApproval::class)->middleware('parish_admin')->name('chairperson.approvals');
    Route::get('/parish', \App\Livewire\ParishAdminDashboard::class)->middleware('parish_admin')->name('parish.dashboard');
    Route::get('/diocese', \App\Livewire\DioceseDashboard::class)->middleware('super_admin')->name('diocese.dashboard');
    Route::get('/profile', Profile::class)->name('profile');
});

// Authentication & Session Termination
Route::match(['get', 'post'], '/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Storage fallback route for Windows environment
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

