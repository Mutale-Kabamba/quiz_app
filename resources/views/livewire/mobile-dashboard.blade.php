<div class="space-y-6 pb-6">

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN EXECUTIVE HOME OVERVIEW                               -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-5">
            <!-- DIOCESAN HERO HEADER -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-xs">
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-2.5">
                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 whitespace-nowrap">
                            Diocesan Curia
                        </span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            {{ $liturgicalContext['season'] }}
                        </span>
                    </div>

                    <a href="/diocese" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all touch-press flex items-center gap-1 shadow-xs whitespace-nowrap flex-shrink-0">
                        <span>Command Center</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="space-y-0.5">
                    <h2 class="text-lg sm:text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight leading-snug">
                        Livingstone Diocese Headquarters
                    </h2>
                    <p class="text-xs text-slate-500">Administrator: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $user->name }}</span> &bull; Pastoral Territory</p>
                </div>

                <!-- QUICK CURIA SHORTCUTS -->
                <div class="grid grid-cols-4 gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/80 text-center">
                    <a href="/diocese" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <svg class="w-4 h-4 mx-auto text-purple-600 dark:text-purple-400 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-[10px] font-bold block leading-none">Parishes</span>
                    </a>
                    <a href="/study" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <svg class="w-4 h-4 mx-auto text-purple-600 dark:text-purple-400 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="text-[10px] font-bold block leading-none">Curriculum</span>
                    </a>
                    <a href="/quiz" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <svg class="w-4 h-4 mx-auto text-purple-600 dark:text-purple-400 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-bold block leading-none">Rallies</span>
                    </a>
                    <a href="/leaderboard" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <svg class="w-4 h-4 mx-auto text-purple-600 dark:text-purple-400 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="text-[10px] font-bold block leading-none">Standings</span>
                    </a>
                </div>
            </div>

            <!-- DIOCESAN STATISTICAL GRID -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Diocesan Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($diocesanKpis['total_youth']) }}</span>
                        <span class="text-xs text-slate-500 font-semibold">registered</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold block mt-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $diocesanKpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Territory &amp; Parishes</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-purple-700 dark:text-purple-400">{{ $diocesanKpis['total_parishes'] }}</span>
                        <span class="text-xs text-slate-500 font-semibold">Parishes</span>
                    </div>
                    <span class="text-[11px] text-purple-600 dark:text-purple-400 font-bold block mt-1.5">
                        {{ $deaneries->count() }} Pastoral Deaneries
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formation Mastery</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $diocesanKpis['average_mastery'] }}%</span>
                        <span class="text-xs text-slate-500 font-semibold">accuracy</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1.5">
                        {{ number_format($diocesanKpis['total_xp']) }} collective XP
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Catechetical Sessions</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($diocesanKpis['quizzes_completed']) }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1.5">
                        {{ number_format($diocesanKpis['lessons_completed']) }} lessons completed
                    </span>
                </div>
            </div>

            <!-- DEANERIES HEALTH STREAM -->
            <div class="p-5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Deanery Performance Overview</h3>
                    <a href="/leaderboard" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">Full Standings &rarr;</a>
                </div>

                <div class="space-y-2">
                    @foreach($deaneries as $deanery)
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl flex items-center justify-between text-xs border border-slate-200/60 dark:border-slate-800">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $deanery->name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $deanery->parishes_count }} Parishes in jurisdiction</span>
                            </div>
                            <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold text-[10px] border border-purple-200/50 dark:border-purple-800/50">
                                Deanery Code: {{ $deanery->code }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) EXECUTIVE HOME OVERVIEW                -->
    <!-- ========================================================================= -->
    @elseif($user->isChairperson())
        <div class="space-y-5">
            <!-- PARISH EXECUTIVE BANNER -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-xs">
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-2.5">
                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 whitespace-nowrap">
                            Parish Youth Ministry
                        </span>
                        @if($parish->deanery)
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap flex items-center gap-1">
                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                {{ $parish->deanery->name }}
                            </span>
                        @endif
                    </div>

                    <a href="/parish" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all touch-press flex items-center gap-1 shadow-xs whitespace-nowrap flex-shrink-0">
                        <span>Parish Desk</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="space-y-0.5">
                    <h2 class="text-lg sm:text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight leading-snug">
                        {{ $parish->name }}
                    </h2>
                    <p class="text-xs text-slate-500">Chairperson: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $user->name }}</span> &bull; Roster Management</p>
                </div>

                <!-- QUICK SHORTCUTS -->
                <div class="grid grid-cols-4 gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/80 text-center">
                    <a href="/parish" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <span class="text-[10px] font-bold block">+ Member</span>
                    </a>
                    <a href="/study" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <span class="text-[10px] font-bold block">Lessons</span>
                    </a>
                    <a href="/quiz" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <span class="text-[10px] font-bold block">Host Quiz</span>
                    </a>
                    <a href="/leaderboard" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 dark:bg-slate-900/60 dark:hover:bg-purple-950/20 border border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-colors touch-press">
                        <span class="text-[10px] font-bold block">Roster Ranks</span>
                    </a>
                </div>
            </div>

            <!-- PARISH KPIS -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Registered Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $parishKpis['total_youth'] }}</span>
                        <span class="text-xs text-slate-500 font-semibold">members</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold block mt-1.5">
                        {{ $parishKpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formation Health</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-2xl font-black text-purple-700 dark:text-purple-400">{{ $parishKpis['engagement_level'] }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1.5">
                        {{ $parishKpis['engagement_pct'] }}% participation
                    </span>
                </div>
            </div>

            <!-- PENDING VERIFICATIONS ALERT -->
            @if($pendingApprovals->isNotEmpty())
                <div class="p-4 bg-amber-50/80 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Pending Roster Sign-Offs ({{ $pendingApprovals->count() }})
                        </span>
                        <a href="/parish" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">Review &rarr;</a>
                    </div>
                    <p class="text-xs text-amber-800 dark:text-amber-300/90">
                        New parish youth have registered and are waiting for your confirmation.
                    </p>
                </div>
            @endif
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER FORMATION DASHBOARD (RICH MINIMALISM)                -->
    <!-- ========================================================================= -->
    @else
        <!-- 1. EDITORIAL GREETING & LITURGICAL SEASON CUE -->
        <div class="space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $liturgicalContext['color_bg'] }} {{ $liturgicalContext['color_text'] }} border {{ $liturgicalContext['color_border'] }}">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $liturgicalContext['color_hex'] }}"></span>
                            {{ $liturgicalContext['season'] }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">{{ $liturgicalContext['date_formatted'] }}</span>
                    </div>
                    <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">
                        Peace be with you, {{ explode(' ', $user->name)[0] }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $user->parish?->name ?? 'Livingstone Diocese Youth Ministry' }}
                    </p>
                </div>

                <!-- Streak Flame Counter -->
                <div class="text-right flex-shrink-0">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 text-amber-800 dark:text-amber-300 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.123 1.956-1.517 3.085-.395 1.13-.578 2.21-.578 2.867 0 .54.1 1.053.284 1.518-1.077-.92-1.762-2.31-1.762-3.868 0-.447-.07-1.002-.27-1.436a1 1 0 00-1.79.232c-.52 1.488-.89 3.25-.89 4.904 0 4.418 3.582 8 8 8s8-3.582 8-8c0-3.64-2.022-6.84-5.065-8.796z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ $user->current_streak ?? 0 }}d Streak</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. HERO FORMATION FEATURE (DAILY SCRIPTURE & CATHOLIC ARTWORK ANCHOR) -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 text-white p-5 space-y-3.5 border border-purple-800/40 shadow-sm">
            <!-- Subtle Catholic Watermark Cross Art SVG -->
            <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none text-white">
                <svg class="w-48 h-48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
                </svg>
            </div>

            <div class="relative z-10 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 font-bold uppercase tracking-wider text-[10px] border border-white/15">
                        Daily Spiritual Bread
                    </span>
                    <span class="text-purple-300/80 text-[11px] font-medium">{{ $liturgicalContext['verse_ref'] }}</span>
                </div>

                <blockquote class="font-serif italic text-base sm:text-lg leading-relaxed text-purple-50">
                    &ldquo;{{ $liturgicalContext['verse'] }}&rdquo;
                </blockquote>

                <div class="pt-2 flex items-center justify-between border-t border-white/10 text-xs">
                    <span class="text-purple-200/80 text-[11px] truncate max-w-[200px]">
                        Patroness: {{ $liturgicalContext['diocesan_patroness'] }}
                    </span>
                    <a href="/study" class="text-white font-bold hover:underline flex items-center gap-1">
                        <span>Study Library</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. FORMATION PROGRESS METRICS (CLEAN RICH SURFACE) -->
        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400 block">Formation Progress</span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                        Level {{ $currentLevel }} &bull; {{ $levelTitle }}
                    </h3>
                </div>
                <div class="text-right">
                    <span class="text-xs font-black text-purple-600 dark:text-purple-400">{{ number_format($currentXp) }} XP</span>
                    <span class="text-[10px] text-slate-400 block">Next: {{ number_format($nextThreshold) }} XP</span>
                </div>
            </div>

            <!-- Linear Level Progress -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden border border-slate-200/50 dark:border-slate-700/50">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-500"
                     style="width: {{ $levelProgressPercentage }}%"></div>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-1 text-center text-xs">
                <div class="p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Streak</span>
                    <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $user->current_streak ?? 0 }} Days</span>
                </div>
                <div class="p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Rank</span>
                    <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">Level {{ $currentLevel }}</span>
                </div>
                <div class="p-2 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Flashcards</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400 mt-0.5 block">{{ $spacedReviewsCount }} Due</span>
                </div>
            </div>
        </div>

        <!-- 4. TODAY'S DAILY FORMATION ("LEARN IN 5 MINUTES") -->
        @if($microLesson)
            <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border-2 border-purple-500/40 dark:border-purple-600/40 space-y-3 shadow-sm">
                <div class="flex items-center justify-between text-xs">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 font-bold uppercase text-[10px] border border-purple-200 dark:border-purple-800">
                        Today's Formation
                    </span>
                    <span class="text-slate-400 text-[11px] font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Learn in 5 Minutes
                    </span>
                </div>

                <div>
                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white leading-snug">{{ $microLesson->title }}</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 mt-1">{{ $microLesson->summary ?? $microLesson->subheading }}</p>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] text-purple-600 dark:text-purple-400 font-bold">+25 Formation XP</span>
                    <a href="/lesson/{{ $microLesson->id }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-sm">
                        {{ $microLessonCompleted ? 'Review Micro-Lesson' : 'Start Micro-Lesson →' }}
                    </a>
                </div>
            </div>
        @endif

        <!-- 5. HORIZONTAL CONTENT RAIL: CATHOLIC SAINTS & AFRICAN HERITAGE -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Saints &amp; African Heritage</h3>
                    <p class="text-[11px] text-slate-500">Patrons and witnesses of the Universal Church</p>
                </div>
                <a href="/study" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline">Explore All &rarr;</a>
            </div>

            <!-- Horizontal Scroll Rail -->
            <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-1 -mx-4 px-4">
                <!-- Saint Card 1: St. Theresa -->
                <div class="flex-shrink-0 w-60 p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            Diocesan Cathedral Patroness
                        </span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-1">St. Theresa of Lisieux</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2">Doctor of the Church &bull; The "Little Way" of spiritual childhood and trust.</p>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px]">
                        <span class="text-slate-400">Feast: Oct 1</span>
                        <a href="/study" class="text-purple-600 font-bold hover:underline">Learn &rarr;</a>
                    </div>
                </div>

                <!-- Saint Card 2: Uganda Martyrs -->
                <div class="flex-shrink-0 w-60 p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-red-50 dark:bg-red-950/40 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800">
                            African Youth Patrons
                        </span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-1">St. Charles Lwanga &amp; Companions</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2">Martyrs of Uganda &bull; Courageous witness to Christian faith and purity.</p>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px]">
                        <span class="text-slate-400">Feast: Jun 3</span>
                        <a href="/study" class="text-purple-600 font-bold hover:underline">Learn &rarr;</a>
                    </div>
                </div>

                <!-- Saint Card 3: St. Bakhita -->
                <div class="flex-shrink-0 w-60 p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2 flex flex-col justify-between">
                    <div class="space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            African Witness of Hope
                        </span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-1">St. Josephine Bakhita</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2">Sudanese virgin &bull; Transformative journey from slavery to divine freedom.</p>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px]">
                        <span class="text-slate-400">Feast: Feb 8</span>
                        <a href="/study" class="text-purple-600 font-bold hover:underline">Learn &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. RALLY PREPARATION (LIVINGSTONE DIOCESAN YOUTH RALLY 2026) -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-950/30 dark:to-indigo-950/30 border border-purple-200 dark:border-purple-900/60 space-y-3">
            <div class="flex items-center justify-between">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-bold uppercase text-[10px] tracking-wider">
                    Diocesan Rally 2026
                </span>
                <span class="text-[11px] text-purple-700 dark:text-purple-300 font-semibold">Livingstone Diocese</span>
            </div>

            <div>
                <h4 class="text-base font-bold font-serif text-slate-900 dark:text-white">Prepare for the Rally</h4>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                    Represent your parish in the upcoming Diocesan Bible &amp; Catechetical Quiz Tournament.
                </p>
            </div>

            <div class="pt-1 flex gap-2">
                <a href="/quiz?tab=compete" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs text-center transition-colors touch-press shadow-sm">
                    Enter Rally Lobby &rarr;
                </a>
            </div>
        </div>

        <!-- 7. CONTINUE LEARNING & RECOMMENDED FORMATION -->
        @if($continueLesson)
            <div class="p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Continue Your Formation</span>
                    <span class="text-[11px] text-purple-600 font-semibold">{{ $continueLesson->category?->name }}</span>
                </div>

                <div>
                    <h4 class="font-bold font-serif text-slate-900 dark:text-white text-sm">{{ $continueLesson->title }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ $continueLesson->subheading }}</p>
                </div>

                <a href="/lesson/{{ $continueLesson->id }}" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl text-xs block text-center transition-colors touch-press">
                    Resume Lesson &rarr;
                </a>
            </div>
        @endif

        <!-- 8. PARISH COMMUNITY FORMATION SPRINT -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2.5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Parish Formation Challenge
                </span>
                <span class="text-[10px] font-bold text-slate-400">{{ $user->parish?->name ?? 'Parish Challenge' }}</span>
            </div>

            <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $activeParishChallenge?->title ?? '5,000 XP Parish Collective Sprint' }}</h4>
            <p class="text-xs text-slate-500">{{ $activeParishChallenge?->description ?? 'Complete quizzes and lessons to help our parish climb the diocesan leaderboard.' }}</p>
        </div>
    @endif

</div>
