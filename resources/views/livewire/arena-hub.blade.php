<div class="space-y-5 pb-6">

    <!-- ARENA HEADER -->
    <div class="pt-1">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Quiz &amp; Competition Arena</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Practice self-paced questions or compete for your Parish</p>
    </div>

    <!-- 2-OPTION SEGMENTED SWITCHER: PRACTICE vs COMPETE -->
    <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
        <button 
            type="button"
            wire:click="setTab('practice')"
            class="py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $activeTab === 'practice' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Practice</span>
        </button>

        <button 
            type="button"
            wire:click="setTab('compete')"
            class="py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $activeTab === 'compete' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Compete</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- OPTION 1: PRACTICE MODE                                                   -->
    <!-- ========================================================================= -->
    @if($activeTab === 'practice')
        <div class="space-y-4">
            
            <!-- DIFFICULTY TIER SELECTOR -->
            <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
                <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider block">Difficulty Level</span>
                <div class="grid grid-cols-3 gap-1.5 text-xs font-medium">
                    <button 
                        type="button"
                        wire:click="setLevel(1)"
                        class="py-1.5 rounded-lg transition-colors {{ $selectedLevel === 1 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800 font-semibold' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                        Level 1 (Junior)
                    </button>
                    <button 
                        type="button"
                        wire:click="setLevel(2)"
                        class="py-1.5 rounded-lg transition-colors {{ $selectedLevel === 2 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800 font-semibold' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                        Level 2 (Youth)
                    </button>
                    <button 
                        type="button"
                        wire:click="setLevel(3)"
                        class="py-1.5 rounded-lg transition-colors {{ $selectedLevel === 3 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800 font-semibold' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                        Level 3 (Advanced)
                    </button>
                </div>
            </div>

            <!-- DAILY PRACTICE CHALLENGE -->
            @if($todayChallenge)
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Daily Formation Challenge</h4>
                                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 px-1.5 py-0.2 rounded">+50 XP</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">5 questions &bull; Streak active</p>
                        </div>
                    </div>

                    <a href="/quiz/play?mode=practice&challenge=today" class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs transition-colors">
                        {{ $challengeCompleted ? 'Review' : 'Start' }}
                    </a>
                </div>
            @endif

            <!-- AVAILABLE PRACTICE QUIZZES BY CATEGORY -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Available Practice Quizzes
                    </h3>
                    <span class="text-[11px] text-slate-400 font-medium">{{ $categories->count() }} Topics</span>
                </div>

                <div class="space-y-2">
                    @foreach($categories as $category)
                        <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-colors flex items-center justify-between group">
                            <div class="flex-1 pr-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/30 px-2 py-0.5 rounded">
                                        {{ $category->name }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">&bull; {{ $category->questions_count }} Questions</span>
                                </div>
                                <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $category->description ?? 'Catholic Doctrine, Scripture and Tradition' }}
                                </h4>
                            </div>

                            <a href="/quiz/play/{{ $category->id }}?mode=practice&level={{ $selectedLevel }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 group-hover:bg-purple-600 group-hover:text-white text-slate-700 dark:text-slate-300 font-semibold text-xs flex-shrink-0 transition-colors">
                                Practice &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- OPTION 2: COMPETE MODE                                                    -->
    <!-- ========================================================================= -->
    @else
        <div class="space-y-4">

            <!-- 1. LIVE DIOCESAN RANKED ARENA (Clean Flat Card) -->
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border-2 border-purple-500/40 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300">
                        Diocesan Ranked Season
                    </span>
                    <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold flex items-center gap-1">
                        Active Season
                    </span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Livingstone Diocesan Ranked Arena</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                        Timed 10-question challenge. Your score contributes directly to your Parish, Deanery, and Diocesan rankings!
                    </p>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        <span>15s per question</span> &bull; <span>Streak multipliers</span>
                    </div>

                    <a href="/quiz/play?mode=ranked&level=2" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs transition-colors touch-press">
                        Enter Competition &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. PARISH LIVE RALLY MULTIPLAYER PIN -->
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">Live Youth Rally Lobby</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Join a live rally competition hosted by your Parish</p>
                    </div>
                </div>

                <form wire:submit.prevent="joinRally" class="flex items-center gap-2 pt-1">
                    <input 
                        type="text" 
                        wire:model="rallyPin" 
                        maxlength="6"
                        placeholder="Enter 6-Digit PIN"
                        class="flex-1 px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white font-mono text-center tracking-widest placeholder-slate-400 focus:outline-none focus:border-purple-500">

                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs rounded-lg transition-colors">
                        Join Rally
                    </button>
                </form>
                @error('rallyPin') <span class="text-[10px] text-red-500 font-medium block">{{ $message }}</span> @enderror
            </div>

            <!-- 3. UPCOMING DEANERY RALLIES SCHEDULE -->
            <div class="space-y-2.5">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Upcoming Deanery Rallies
                </h3>

                <div class="space-y-2">
                    <div class="p-3 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-bold uppercase text-purple-600 dark:text-purple-400 block">Livingstone Deanery</span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">St. Theresa Cathedral Youth Rally</h4>
                            <span class="text-[11px] text-slate-400">Saturday, 14:00 CAT</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold">Upcoming</span>
                    </div>

                    <div class="p-3 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-bold uppercase text-amber-600 dark:text-amber-400 block">Sesheke Deanery</span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">Inter-Parish Catechism Championship</h4>
                            <span class="text-[11px] text-slate-400">Registration Open</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold">Upcoming</span>
                    </div>
                </div>
            </div>

            <!-- 4. LEADERBOARD LINK -->
            <div class="pt-1">
                <a href="/leaderboard" class="block w-full py-2.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center text-xs font-semibold text-purple-600 dark:text-purple-400 hover:border-purple-300 dark:hover:border-purple-700 transition-colors">
                    View Current Diocesan Rankings &rarr;
                </a>
            </div>
        </div>
    @endif
</div>
