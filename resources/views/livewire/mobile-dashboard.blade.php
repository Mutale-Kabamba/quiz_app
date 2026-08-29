<div class="space-y-5 pb-6">

    <!-- 1. GREETING & FORMATION STATUS (M3 Flat) -->
    <div class="pt-1">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    {{ now()->format('l, M j') }}
                </p>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight mt-0.5">
                    Peace be with you, {{ explode(' ', $user->name)[0] }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Catholic Youth Formation &bull; {{ $user->parish?->name ?? 'Livingstone Diocese' }}
                </p>
            </div>

            <!-- Level & XP Micro-Badge -->
            <div class="text-right flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>Level {{ $currentLevel }}</span>
                </span>
                <span class="text-[10px] text-slate-400 block mt-1 font-medium">{{ number_format($currentXp) }} XP</span>
            </div>
        </div>

        <!-- Level XP Linear Progress Bar -->
        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-3 border border-slate-200/50 dark:border-slate-700/50">
            <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                 style="width: {{ $levelProgressPercentage }}%"></div>
        </div>
    </div>

    <!-- 2. SPACED REVIEWS REMINDER (If mistakes due for review) -->
    @if($spacedReviewsCount > 0)
        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/60 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white">Spaced Review Ready</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $spacedReviewsCount }} concepts ready for scheduled reinforcement</p>
                </div>
            </div>
            <a href="/flashcards" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold transition-colors">
                Review
            </a>
        </div>
    @endif

    <!-- 3. TODAY'S FORMATION ("LEARN IN 5 MINUTES") -->
    @if($microLesson)
        <div class="bg-white dark:bg-[#121826] border-2 border-purple-500/30 rounded-xl p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase tracking-wider">
                    Today's Formation &bull; 5-Min Micro-Learning
                </span>
                <span class="text-purple-600 dark:text-purple-400 font-bold text-xs">+{{ $microLesson->xp_reward }} XP</span>
            </div>

            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                    {{ $microLesson->title }}
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                    {{ $microLesson->hook_question ?? 'Master the essentials of Catholic faith in 5 minutes.' }}
                </p>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-semibold text-slate-600 dark:text-slate-400 py-1">
                <div class="p-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg">1. 4-Min Read</div>
                <div class="p-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg">2. 3 Flashcards</div>
                <div class="p-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg">3. 3 Questions</div>
            </div>

            <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-400 text-[11px] font-mono">{{ $microLesson->reference_citation ?? 'CCC 811-870' }}</span>
                <a href="/lesson/{{ $continueLesson?->id ?? 'four-marks' }}" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-colors">
                    {{ $microLessonCompleted ? 'Review Lesson' : 'Start 5-Min Formation' }}
                </a>
            </div>
        </div>
    @endif

    <!-- 4. DIOCESAN RALLY PREPARATION ("PREPARE FOR THE RALLY") -->
    @if($rallyPrep)
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Prepare for the Rally
                </span>
                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                    18 Days Remaining
                </span>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Livingstone Diocesan Youth Rally</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Target: 200 Questions across 5 Domains</p>
                </div>
                <div class="text-right">
                    <span class="text-base font-bold text-purple-600 dark:text-purple-400">{{ $rallyReadiness->overall_readiness_percentage }}%</span>
                    <span class="text-[10px] text-slate-400 block">Readiness</span>
                </div>
            </div>

            <!-- Domain Readiness Meters -->
            <div class="grid grid-cols-2 gap-2 text-[10px] font-medium text-slate-500 dark:text-slate-400 pt-1">
                <div>
                    <div class="flex justify-between mb-0.5">
                        <span>Scripture</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $rallyReadiness->scripture_readiness }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-600" style="width: {{ $rallyReadiness->scripture_readiness }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-0.5">
                        <span>Catechism (CCC)</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $rallyReadiness->catechism_readiness }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-600" style="width: {{ $rallyReadiness->catechism_readiness }}%"></div>
                    </div>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Today's Training: 10 Questions</span>
                <a href="/quiz/play?mode=ranked&rally=1" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-lg font-semibold transition-colors">
                    Train for Rally &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- 5. WEAK AREAS ENGINE RECOMMENDATION -->
    @if(!empty($weakAreas))
        <div class="bg-white dark:bg-[#121826] border border-red-200 dark:border-red-950/50 rounded-xl p-4 space-y-2.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-red-700 dark:text-red-400 uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Recommended Formation Focus
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Adaptive Engine</span>
            </div>

            @foreach($weakAreas as $weak)
                <div class="flex items-center justify-between pt-1">
                    <div>
                        <h4 class="text-xs font-semibold text-slate-900 dark:text-white">{{ $weak['topic_name'] }}</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $weak['mastery_score'] }}% current mastery &bull; Retest available</p>
                    </div>
                    <a href="/quiz/play/{{ $weak['topic_id'] }}?mode=practice" class="px-3 py-1 bg-red-50 hover:bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900/50 rounded-lg text-xs font-semibold transition-colors">
                        Practice &rarr;
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 6. PARISH COMMUNITY CHALLENGE (Parish vs Parish) -->
    @if($activeParishChallenge)
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Parish Formation Challenge
                </span>
                <span class="text-purple-600 dark:text-purple-400 font-bold text-xs">{{ $activeParishChallenge->xp_reward_pool }} XP Pool</span>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $activeParishChallenge->title }}</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $activeParishChallenge->description }}</p>
            </div>

            @if($challengeStandings)
                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-lg bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/40">
                        <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 block truncate">{{ $challengeStandings['parish_1']['parish']?->name ?? 'Your Parish' }}</span>
                        <span class="font-bold text-slate-900 dark:text-white text-xs">{{ number_format($challengeStandings['parish_1']['total_xp']) }} XP</span>
                        <span class="text-[10px] text-slate-400 block">{{ $challengeStandings['parish_1']['youth_count'] }} youth</span>
                    </div>

                    <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 block truncate">{{ $challengeStandings['parish_2']['parish']?->name ?? 'Challenger' }}</span>
                        <span class="font-bold text-slate-900 dark:text-white text-xs">{{ number_format($challengeStandings['parish_2']['total_xp']) }} XP</span>
                        <span class="text-[10px] text-slate-400 block">{{ $challengeStandings['parish_2']['youth_count'] }} youth</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- 7. CONTINUE FORMATION -->
    @if($continueLesson)
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-purple-700 dark:text-purple-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Continue Lesson
                </span>
                <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $continueLesson->estimated_read_minutes }} min</span>
            </div>

            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                    {{ $continueLesson->title }}
                </h3>
                @if($continueLesson->subheading)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                        {{ $continueLesson->subheading }}
                    </p>
                @endif
            </div>

            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between text-[11px] font-medium text-slate-500 dark:text-slate-400">
                    <span>Formation Progress</span>
                    <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $continueLesson->is_completed ? '100%' : '65%' }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-600 dark:bg-emerald-500 rounded-full" style="width: {{ $continueLesson->is_completed ? 100 : 65 }}%"></div>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80">
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $continueLesson->category?->name }}</span>
                <a href="/lesson/{{ $continueLesson->id }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold transition-colors touch-press">
                    <span>Continue</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    @endif

    <!-- 8. QUICK QUIZ & DAILY CHALLENGE -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Quick Quiz Card -->
        <a href="/quiz" class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 space-y-2 hover:border-purple-400 transition-colors group">
            <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                    Quick Quiz
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">5 Questions &bull; Practice</p>
            </div>
            <span class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 flex items-center gap-1">
                Start &rarr;
            </span>
        </a>

        <!-- Daily Challenge Card -->
        <a href="/quiz/play?mode=practice&challenge=today" class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 space-y-2 hover:border-amber-400 transition-colors group">
            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                    Daily Challenge
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">+50 XP &bull; Streak</p>
            </div>
            <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                {{ $challengeCompleted ? 'Completed' : 'Play →' }}
            </span>
        </a>
    </div>

    <!-- 9. SAINT OF THE DAY & "EXPLAIN THIS" REFLECTION -->
    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-2">
        <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                </svg>
                Saint of the Day
            </span>
            <span class="text-slate-400 text-[11px]">Aug 28</span>
        </div>

        <div>
            <h4 class="text-xs font-bold text-slate-900 dark:text-white">St. Augustine of Hippo</h4>
            <p class="text-xs text-slate-600 dark:text-slate-300 italic mt-1 leading-relaxed">
                "You have made us for yourself, O Lord, and our heart is restless until it rests in you."
            </p>
        </div>

        <div class="pt-1 flex items-center justify-between">
            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Bishop and Doctor of the Church &bull; North African Father
            </span>
            <button 
                type="button"
                wire:click="openExplainModal('St. Augustine & Grace')"
                class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                Explain Grace &rarr;
            </button>
        </div>
    </div>

    <!-- 10. FORMATION PROGRESS (CATECHETICAL PILLARS) -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Formation Progress
            </h3>
            <a href="/study" class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                Explore Tracks
            </a>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            @foreach($categoryProgress as $cat)
                <a href="/study?category={{ $cat['category_id'] }}" class="p-3 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-400 transition-colors group">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors truncate max-w-[110px]">
                            {{ $cat['name'] }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $cat['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium mt-1.5 block">{{ $cat['total_lessons'] }} Lessons</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 11. "EXPLAIN THIS" DOCTRINAL DRAWER MODAL -->
    @if($showExplainModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300">Catholic Doctrine &bull; Explain This</span>
                    <button wire:click="$set('showExplainModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                @php
                    $explanation = app(\App\Services\CatholicDoctrinalExplanationService::class)->getExplanation($explainConcept);
                @endphp

                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $explanation['concept_title'] }}</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ $explanation['simple_explanation'] }}</p>
                </div>

                <div class="p-3 bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/40 rounded-lg text-xs text-slate-700 dark:text-slate-300 space-y-1">
                    <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 block">Apostolic &amp; Catechetical Foundation</span>
                    <p class="leading-relaxed">{{ $explanation['doctrinal_explanation'] }}</p>
                    <span class="text-[10px] font-mono text-purple-600 dark:text-purple-400 block pt-1">{{ $explanation['catechism_citation'] }} &bull; {{ $explanation['scripture_citation'] }}</span>
                </div>

                <button wire:click="$set('showExplainModal', false)" class="w-full py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold">
                    Understood &bull; Close
                </button>
            </div>
        </div>
    @endif
</div>
