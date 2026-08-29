<div class="space-y-6 pb-20">

    <!-- TOAST NOTIFICATIONS -->
    @if($successMessage)
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if($errorMessage)
        <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl text-xs text-red-800 dark:text-red-200 font-semibold flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', null)" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. PROFILE HEADER CARD                                                    -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-4">
        
        <!-- AVATAR CONTAINER WITH CAMERA ACTION -->
        <div class="relative inline-block mx-auto">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-purple-300 dark:border-purple-700 shadow-sm mx-auto">
            @else
                <div class="w-20 h-20 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-2xl mx-auto border-2 border-purple-200 dark:border-purple-800">
                    {{ $user->initials }}
                </div>
            @endif

            <button 
                type="button" 
                wire:click="$set('showAvatarModal', true)"
                class="absolute bottom-0 right-0 p-1.5 rounded-full bg-purple-600 hover:bg-purple-700 text-white border-2 border-white dark:border-[#121826] shadow-sm transition-transform active:scale-95"
                title="Change Profile Photo">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->parish?->name ?? 'Livingstone Diocese' }}</p>
            
            <div class="flex items-center justify-center gap-2 mt-2">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900/40">
                    Level {{ $currentLevel }} &bull; {{ number_format($currentXp) }} XP
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/40">
                    {{ $averageMastery }}% Mastery
                </span>
            </div>
        </div>

        <!-- PROFILE COMPLETION METER -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400 font-medium text-[11px]">Profile Completion</span>
                <span class="text-purple-700 dark:text-purple-400 font-bold text-xs">{{ $user->profile_completion_percentage }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300" style="width: {{ $user->profile_completion_percentage }}%"></div>
            </div>
        </div>

        <button 
            type="button" 
            wire:click="$set('showEditProfileModal', true)"
            class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span>Edit Profile Details</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. PERSONAL ACCOUNT DETAILS                                               -->
    <!-- ========================================================================= -->
    <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Personal Information
            </h3>
            <button wire:click="$set('showEditProfileModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                Edit
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-semibold block">Full Name</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-semibold block">Phone Number</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->phone }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-semibold block">Email Address</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->email ?? 'Not provided' }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-semibold block">Date of Birth</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->dob ? $user->dob->format('M d, Y') : 'Not set' }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. FORMATION MASTERY & METRICS                                            -->
    <!-- ========================================================================= -->
    <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 tracking-wider block">Formation Rank</span>
                <h3 class="text-xs font-bold text-slate-900 dark:text-white">
                    @php
                        $levelTitle = match($currentLevel) {
                            1 => 'Seeker of Truth',
                            2 => 'Faithful Disciple',
                            3 => 'Catechetical Scholar',
                            4 => 'Scripture Pillar',
                            5 => 'Diocesan Evangelist',
                            default => 'Youth Champion',
                        };
                    @endphp
                    Level {{ $currentLevel }}: {{ $levelTitle }}
                </h3>
            </div>
            <span class="text-xs font-bold text-purple-600 dark:text-purple-400">{{ number_format($currentXp) }} XP</span>
        </div>

        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
            <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                 style="width: {{ $levelProgressPercentage }}%"></div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
            <span>Progress to Level {{ $currentLevel + 1 }}</span>
            <span>{{ $nextThreshold - $currentXp }} XP needed</span>
        </div>

        <!-- 4-GRID STATS -->
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Lessons Mastered</span>
                <span class="text-base font-bold text-slate-900 dark:text-white block mt-0.5">{{ $completedLessonsCount }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Flashcards Mastered</span>
                <span class="text-base font-bold text-slate-900 dark:text-white block mt-0.5">{{ $masteredFlashcardsCount }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Quizzes Completed</span>
                <span class="text-base font-bold text-slate-900 dark:text-white block mt-0.5">{{ $totalQuizzes }}</span>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Formation Streak</span>
                <span class="text-base font-bold text-amber-600 dark:text-amber-400 block mt-0.5">{{ $user->current_streak ?? 0 }} Days</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. PARISH MEMBERSHIP & TRANSFER                                           -->
    <!-- ========================================================================= -->
    <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Parish Community
            </h3>
            <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                {{ $user->parish?->deanery?->name ?? 'Livingstone Deanery' }}
            </span>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-semibold block">Registered Parish</span>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">{{ $user->parish?->name ?? 'Diocesan Youth' }}</h4>
            </div>

            @if($pendingTransfer)
                <span class="px-2.5 py-1 rounded bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 text-[11px] font-semibold">
                    Transfer to {{ $pendingTransfer->toParish?->name }} Pending
                </span>
            @else
                <button 
                    type="button" 
                    wire:click="$set('showParishModal', true)"
                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors">
                    Request Parish Change
                </button>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. ACCOUNT SECURITY                                                       -->
    <!-- ========================================================================= -->
    <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Account Security
            </h3>
            <button 
                type="button" 
                wire:click="$set('showPasswordModal', true)"
                class="text-xs text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                Change Password
            </button>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-900 dark:text-white font-medium block">Password</span>
                    <span class="text-[11px] text-slate-400">
                        {{ $user->last_password_changed_at ? 'Last changed ' . $user->last_password_changed_at->diffForHumans() : 'Secured via standard password' }}
                    </span>
                </div>
                <button wire:click="$set('showPasswordModal', true)" class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                    Update
                </button>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-slate-900 dark:text-white font-medium block">Phone Identity</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Registered ({{ $user->phone }})</span>
                </div>
                <button wire:click="$set('showEditProfileModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-semibold">
                    Change
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. PREFERENCES & PRIVACY                                                  -->
    <!-- ========================================================================= -->
    <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Preferences &amp; Privacy
            </h3>
            <button wire:click="$set('showPreferencesModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                Customize
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Formation Reminders</span>
                <span class="text-slate-800 dark:text-slate-200 font-semibold mt-0.5 block">{{ $notifyFormation ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Rankings Visibility</span>
                <span class="text-slate-800 dark:text-slate-200 font-semibold mt-0.5 block">{{ $showNameInRankings ? 'Public Name' : 'Anonymous' }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. ACHIEVEMENTS BADGES GALLERY                                            -->
    <!-- ========================================================================= -->
    <div class="space-y-2.5">
        <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
            Achievements
        </h3>

        <div class="grid grid-cols-2 gap-2">
            @foreach($allAchievements as $ach)
                @php
                    $isUnlocked = in_array($ach->id, $unlockedAchievementIds);
                @endphp
                <div class="p-3 rounded-xl border transition-colors {{ $isUnlocked ? 'bg-white dark:bg-[#121826] border-purple-300 dark:border-purple-800' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $isUnlocked ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $ach->title }}</h4>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block line-clamp-1 mt-0.5">{{ $ach->description }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. BOOKMARKED LESSONS                                                     -->
    <!-- ========================================================================= -->
    @if($bookmarkedLessons->isNotEmpty())
        <div class="space-y-2.5">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Bookmarked Lessons
            </h3>

            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($bookmarkedLessons as $bLesson)
                    <a href="/lesson/{{ $bLesson->id }}" class="p-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <div>
                            <span class="text-[9px] font-bold uppercase text-purple-600 dark:text-purple-400 block">{{ $bLesson->category?->name }}</span>
                            <h4 class="text-xs font-semibold text-slate-900 dark:text-white">{{ $bLesson->title }}</h4>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 9. SIGNOUT & DEACTIVATE ACTIONS                                           -->
    <!-- ========================================================================= -->
    <div class="pt-2 space-y-2">
        <button 
            type="button" 
            wire:click="logout" 
            class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl transition-colors">
            Sign Out
        </button>

        <button 
            type="button" 
            wire:click="$set('showDeactivateModal', true)" 
            class="w-full py-2 text-center text-red-600 dark:text-red-400 text-xs font-semibold hover:underline">
            Deactivate Account
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: AVATAR UPLOAD MODAL                                              -->
    <!-- ========================================================================= -->
    @if($showAvatarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-xs w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Profile Photo</h3>
                    <button wire:click="$set('showAvatarModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-3">
                    <input 
                        type="file" 
                        wire:model="avatarFile" 
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 dark:file:bg-purple-950/40 dark:file:text-purple-300">
                    
                    @error('avatarFile') 
                        <span class="text-[10px] text-red-500 font-medium block">{{ $message }}</span> 
                    @enderror

                    <div wire:loading wire:target="avatarFile" class="text-xs text-purple-600 flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Uploading image...
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    @if($avatarFile)
                        <button 
                            type="button" 
                            wire:click="uploadAvatar" 
                            class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs transition-colors">
                            Save Photo
                        </button>
                    @endif

                    @if($user->avatar_path)
                        <button 
                            type="button" 
                            wire:click="removeAvatar" 
                            class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-lg text-xs transition-colors">
                            Remove Photo
                        </button>
                    @endif

                    <button 
                        type="button" 
                        wire:click="$set('showAvatarModal', false)" 
                        class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT PROFILE MODAL                                               -->
    <!-- ========================================================================= -->
    @if($showEditProfileModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Edit Personal Info</h3>
                    <button wire:click="$set('showEditProfileModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="saveProfile" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('name') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (+260...)</label>
                        <input type="text" wire:model="phone" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('phone') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('email') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Date of Birth</label>
                            <input type="date" wire:model="dob" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                            <select wire:model="gender" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showEditProfileModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: CHANGE PASSWORD MODAL                                            -->
    <!-- ========================================================================= -->
    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-xs w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Change Password</h3>
                    <button wire:click="$set('showPasswordModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="changePassword" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Current Password</label>
                        <input type="password" wire:model="currentPassword" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('currentPassword') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">New Password</label>
                        <input type="password" wire:model="newPassword" placeholder="At least 6 characters" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newPassword') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm New Password</label>
                        <input type="password" wire:model="newPasswordConfirmation" placeholder="Repeat new password" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newPasswordConfirmation') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showPasswordModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: REQUEST PARISH TRANSFER MODAL                                    -->
    <!-- ========================================================================= -->
    @if($showParishModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Request Parish Change</h3>
                    <button wire:click="$set('showParishModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="requestParishTransfer" class="space-y-3">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Parish change requests are reviewed by your Parish Chairperson to maintain diocesan roster integrity.
                    </p>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">New Target Parish</label>
                        <select wire:model="targetParishId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="">-- Select Parish --</option>
                            @foreach($parishes as $p)
                                @if($p->id !== $user->parish_id)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->deanery?->name }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('targetParishId') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason for Change</label>
                        <textarea wire:model="transferReason" rows="2" placeholder="e.g. Relocated to Maramba parish outstation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        @error('transferReason') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showParishModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 5: PREFERENCES & PRIVACY MODAL                                      -->
    <!-- ========================================================================= -->
    @if($showPreferencesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Account Preferences</h3>
                    <button wire:click="$set('showPreferencesModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="savePreferences" class="space-y-3">
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Notifications</span>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="notifyFormation" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Daily Formation &amp; Streak Reminders</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="notifyCompetitions" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Diocesan Youth Rally &amp; Competitions</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="notifyParish" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Parish Announcements &amp; Challenges</span>
                        </label>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Privacy in Public Rankings</span>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="showNameInRankings" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Show my name on Diocesan Leaderboard</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="showAvatarInRankings" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Show my avatar photo in rankings</span>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showPreferencesModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 6: ACCOUNT DEACTIVATION CONFIRMATION MODAL                           -->
    <!-- ========================================================================= -->
    @if($showDeactivateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-xs w-full space-y-3 shadow-xl">
                <h3 class="text-xs font-bold text-red-600 dark:text-red-400">Deactivate Your Account?</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Your profile and active formation streak will be temporarily suspended. Your historical quiz records and achievements will be safely preserved for diocesan records.
                </p>

                <div class="flex gap-2 pt-2">
                    <button type="button" wire:click="$set('showDeactivateModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="button" wire:click="deactivateAccount" class="w-1/2 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">
                        Deactivate
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
