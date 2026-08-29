<div class="space-y-5 pb-16">

    <!-- USER PROFILE IDENTITY CARD -->
    <div class="p-6 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-3">
        <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-xl mx-auto border border-purple-200 dark:border-purple-800">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->phone }} &bull; {{ $user->parish?->name ?? 'Livingstone Diocese' }}</p>
            <span class="inline-block mt-2 px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                {{ ucfirst($user->role) }}
            </span>
        </div>
    </div>

    <!-- LEVEL PROGRESS & XP LADDER -->
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
    </div>

    <!-- FORMATION METRICS (FLAT 4-GRID) -->
    <div class="grid grid-cols-2 gap-2.5">
        <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Lessons Mastered</span>
            <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $completedLessonsCount }}</span>
            <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold mt-1 block">Catechetical tracks</span>
        </div>
        <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Flashcards Mastered</span>
            <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $masteredFlashcardsCount }}</span>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 block">Spaced reviews</span>
        </div>
        <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Quizzes Completed</span>
            <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $totalQuizzes }}</span>
            <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold mt-1 block">{{ number_format($totalScore) }} Total Pts</span>
        </div>
        <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Formation Streak</span>
            <span class="text-base font-bold text-amber-600 dark:text-amber-400 mt-0.5 block">{{ $user->current_streak ?? 0 }} Days</span>
            <span class="text-[10px] text-slate-400 font-medium mt-1 block">Longest: {{ $user->longest_streak ?? 0 }} days</span>
        </div>
    </div>

    <!-- ACHIEVEMENTS BADGES GALLERY -->
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

    <!-- BOOKMARKED LESSONS -->
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

    <!-- LOGOUT BUTTON -->
    <div class="pt-2">
        <button 
            type="button" 
            wire:click="logout" 
            class="w-full py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900/50 rounded-xl font-semibold text-xs transition-colors">
            Sign Out
        </button>
    </div>
</div>
