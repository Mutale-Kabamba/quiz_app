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

        <!-- 2. DYNAMIC TODAY'S READINGS & CATHOLIC LITURGICAL CALENDAR -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 text-white p-5 space-y-4 border border-purple-800/40 shadow-sm">
            <!-- Subtle Catholic Watermark Cross Art SVG -->
            <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none text-white">
                <svg class="w-48 h-48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
                </svg>
            </div>

            <div class="relative z-10 space-y-3.5">
                <!-- Header Row: Category Badge, Liturgical Color & Date Navigator -->
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 font-bold uppercase tracking-wider text-[10px] border border-white/15 whitespace-nowrap">
                            {{ empty($readingsDate) || ($liturgicalContext['is_today'] ?? true) ? "Today's Mass Readings" : "Mass Readings" }}
                        </span>
                        @if(!empty($readingsDate) && !($liturgicalContext['is_today'] ?? false))
                            <button type="button" wire:click="todayReadings" class="px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 hover:bg-amber-400/30 text-[10px] font-bold border border-amber-400/30 transition-all touch-press cursor-pointer flex items-center gap-1">
                                <span>Reset Today</span>
                                <span class="text-[9px]">&times;</span>
                            </button>
                        @endif
                    </div>

                    <!-- Date Navigator (Prev, Date Picker, Next) -->
                    <div class="flex items-center gap-0.5 bg-white/10 backdrop-blur-md rounded-xl p-0.5 border border-white/15">
                        <button type="button"
                                wire:click="previousDayReadings"
                                title="Previous Day"
                                class="p-1 rounded-lg hover:bg-white/15 active:scale-95 text-purple-200 hover:text-white transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        </button>

                        <label class="relative flex items-center px-2 py-0.5 text-[11px] font-semibold text-purple-100 hover:text-white cursor-pointer select-none">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                <span>{{ $liturgicalContext['date_short'] ?? '' }}</span>
                            </span>
                            <input type="date"
                                   value="{{ $liturgicalContext['date_raw'] ?? date('Y-m-d') }}"
                                   wire:change="setReadingsDate($event.target.value)"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        </label>

                        <button type="button"
                                wire:click="nextDayReadings"
                                title="Next Day"
                                class="p-1 rounded-lg hover:bg-white/15 active:scale-95 text-purple-200 hover:text-white transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Liturgical Season & Day Title -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs text-purple-300">
                        <span class="text-purple-300 text-[11px] font-medium flex items-center gap-1.5 flex-shrink-0">
                            <span class="w-2 h-2 rounded-full {{ strtolower($liturgicalContext['liturgical_color'] ?? '') === 'red' ? 'bg-rose-500' : (in_array(strtolower($liturgicalContext['liturgical_color'] ?? ''), ['white', 'gold']) ? 'bg-amber-400' : (strtolower($liturgicalContext['liturgical_color'] ?? '') === 'purple' ? 'bg-purple-400' : 'bg-emerald-400')) }}"></span>
                            <span>{{ $liturgicalContext['liturgical_color'] ?? 'Green' }} &bull; {{ $liturgicalContext['season'] ?? 'Ordinary Time' }}</span>
                        </span>
                        <span class="text-[11px] text-purple-200/70 font-medium">{{ $liturgicalContext['date_formatted'] ?? '' }}</span>
                    </div>

                    <h3 class="font-serif font-bold text-base sm:text-lg text-white leading-snug">
                        {{ $liturgicalContext['liturgical_day'] ?? "Today's Liturgy" }}
                    </h3>
                    @if(!empty($liturgicalContext['feast_name']) && $liturgicalContext['feast_name'] !== ($liturgicalContext['liturgical_day'] ?? ''))
                        <p class="text-xs text-purple-200/90 font-medium">
                            Feast: {{ $liturgicalContext['feast_name'] }} ({{ $liturgicalContext['feast_type'] ?? 'Feast' }})
                        </p>
                    @elseif(!empty($liturgicalContext['saint_of_day']))
                        <p class="text-xs text-purple-200/80 font-medium">
                            Commemoration: {{ $liturgicalContext['saint_of_day'] }}
                        </p>
                    @endif
                </div>

                <!-- DYNAMIC DAY'S READINGS ROTATING CAROUSEL -->
                <div x-data="{
                        currentSlide: 0,
                        totalSlides: {{ count($liturgicalContext['slides'] ?? []) }},
                        autoplayTimer: null,
                        startAutoplay() {
                            this.stopAutoplay();
                            if (this.totalSlides > 1) {
                                this.autoplayTimer = setInterval(() => {
                                    this.nextSlide();
                                }, 5000);
                            }
                        },
                        stopAutoplay() {
                            if (this.autoplayTimer) {
                                clearInterval(this.autoplayTimer);
                                this.autoplayTimer = null;
                            }
                        },
                        nextSlide() {
                            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                        },
                        prevSlide() {
                            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                        },
                        goToSlide(index) {
                            this.currentSlide = index;
                            this.startAutoplay();
                        }
                     }"
                     x-init="startAutoplay()"
                     @mouseenter="stopAutoplay()"
                     @mouseleave="startAutoplay()"
                     class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2 relative transition-all">

                    @foreach($liturgicalContext['slides'] ?? [] as $index => $slide)
                        <div x-show="currentSlide === {{ $index }}"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-1 min-h-[64px] flex flex-col justify-between">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-purple-300 font-bold flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                    <span>{{ $slide['type'] }}</span>
                                </span>
                                <span class="text-purple-200/90 font-medium">{{ $slide['citation'] }}</span>
                            </div>
                            <blockquote class="font-serif italic text-xs sm:text-sm text-purple-50 leading-relaxed">
                                &ldquo;{{ $slide['highlight'] }}&rdquo;
                            </blockquote>
                        </div>
                    @endforeach

                    <!-- Carousel Controls & Dots -->
                    @if(count($liturgicalContext['slides'] ?? []) > 1)
                        <div class="flex items-center justify-between pt-1.5 border-t border-white/10 text-[10px] text-purple-300/80">
                            <!-- Indicator Dots -->
                            <div class="flex items-center gap-1.5">
                                @foreach($liturgicalContext['slides'] ?? [] as $index => $slide)
                                    <button type="button"
                                            @click="goToSlide({{ $index }})"
                                            class="h-1.5 rounded-full transition-all cursor-pointer"
                                            :class="currentSlide === {{ $index }} ? 'w-4 bg-purple-400' : 'w-1.5 bg-white/25 hover:bg-white/40'"
                                            title="{{ $slide['type'] }}">
                                    </button>
                                @endforeach
                            </div>

                            <!-- Next / Prev Controls -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="prevSlide(); stopAutoplay()" title="Previous Reading" class="p-1 rounded hover:bg-white/10 text-purple-300 hover:text-white transition-colors cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                </button>
                                <span class="text-[10px] text-purple-300/70 select-none font-medium" x-text="`${currentSlide + 1} of ${totalSlides}`"></span>
                                <button type="button" @click="nextSlide(); stopAutoplay()" title="Next Reading" class="p-1 rounded hover:bg-white/10 text-purple-300 hover:text-white transition-colors cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Subtle & Minimal Book Citations with Action Below -->
                <div class="pt-1.5 space-y-2.5">
                    <!-- Minimalist Subtle Scripture Citations (No heavy clunky boxes) -->
                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[11px] text-purple-200/70 font-normal">
                        <span class="hover:text-white transition-colors">{{ $liturgicalContext['readings']['reading_1']['citation'] ?? '1st Reading' }}</span>
                        <span class="text-purple-400/30 text-[10px]">&bull;</span>
                        <span class="hover:text-white transition-colors">{{ $liturgicalContext['readings']['psalm']['citation'] ?? 'Psalm' }}</span>
                        @if(!empty($liturgicalContext['readings']['reading_2']))
                            <span class="text-purple-400/30 text-[10px]">&bull;</span>
                            <span class="hover:text-white transition-colors">{{ $liturgicalContext['readings']['reading_2']['citation'] }}</span>
                        @endif
                        <span class="text-purple-400/30 text-[10px]">&bull;</span>
                        <span class="font-semibold text-purple-100 hover:text-white transition-colors">{{ $liturgicalContext['readings']['gospel']['citation'] ?? 'Gospel' }}</span>
                    </div>

                    <!-- Full Readings Button placed neatly below -->
                    <button type="button"
                            wire:click="openReadingsModal"
                            class="w-full py-2.5 bg-white/15 hover:bg-white/25 active:scale-[0.99] text-white text-xs font-bold rounded-xl transition-all touch-press flex items-center justify-center gap-1.5 cursor-pointer border border-white/20 shadow-xs">
                        <span>{{ empty($readingsDate) || ($liturgicalContext['is_today'] ?? true) ? "View Full Today's Mass Readings" : "View Full Mass Readings (" . ($liturgicalContext['date_short'] ?? '') . ")" }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- LIVEWIRE MANAGED MODAL: FULL CATHOLIC MASS READINGS -->
            @if($showReadingsModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                     wire:click.self="closeReadingsModal">
                    <div class="bg-white dark:bg-[#121826] text-slate-900 dark:text-white rounded-3xl max-w-lg w-full max-h-[85vh] overflow-hidden flex flex-col shadow-2xl border border-purple-500/30">
                        <!-- Modal Header with Date Navigation -->
                        <div class="p-4 sm:p-5 bg-gradient-to-r from-purple-900 to-indigo-900 text-white flex items-start justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider">
                                        Mass Readings
                                    </span>
                                    <span class="text-xs text-purple-200">{{ $liturgicalContext['liturgical_color'] ?? 'Green' }} &bull; {{ $liturgicalContext['season'] ?? 'Ordinary Time' }}</span>
                                </div>
                                <h3 class="font-serif font-bold text-base sm:text-lg leading-snug">{{ $liturgicalContext['liturgical_day'] ?? "Today's Liturgy" }}</h3>
                                <p class="text-[11px] text-purple-200/80">{{ $liturgicalContext['date_formatted'] ?? '' }}</p>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <!-- Modal Date Controls -->
                                <button type="button" wire:click="previousDayReadings" title="Previous Day" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 active:scale-95 text-white transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                </button>
                                <button type="button" wire:click="nextDayReadings" title="Next Day" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 active:scale-95 text-white transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </button>
                                <button type="button" wire:click="closeReadingsModal" class="text-white/80 hover:text-white p-1.5 rounded-full text-lg leading-none cursor-pointer">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <!-- Readings Navigation Tabs -->
                        <div class="flex border-b border-slate-200 dark:border-slate-800 text-xs font-bold overflow-x-auto no-scrollbar bg-slate-50 dark:bg-[#0d121f]">
                            <button type="button" wire:click="setReadingsTab('reading1')" class="px-3.5 py-2.5 whitespace-nowrap transition-colors cursor-pointer {{ $readingsTab === 'reading1' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 bg-white dark:bg-[#121826]' : 'text-slate-500 hover:text-slate-800' }}">
                                1st Reading
                            </button>
                            <button type="button" wire:click="setReadingsTab('psalm')" class="px-3.5 py-2.5 whitespace-nowrap transition-colors cursor-pointer {{ $readingsTab === 'psalm' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 bg-white dark:bg-[#121826]' : 'text-slate-500 hover:text-slate-800' }}">
                                Psalm
                            </button>
                            @if(!empty($liturgicalContext['readings']['reading_2']))
                                <button type="button" wire:click="setReadingsTab('reading2')" class="px-3.5 py-2.5 whitespace-nowrap transition-colors cursor-pointer {{ $readingsTab === 'reading2' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 bg-white dark:bg-[#121826]' : 'text-slate-500 hover:text-slate-800' }}">
                                    2nd Reading
                                </button>
                            @endif
                            <button type="button" wire:click="setReadingsTab('gospel')" class="px-3.5 py-2.5 whitespace-nowrap transition-colors cursor-pointer {{ $readingsTab === 'gospel' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 bg-white dark:bg-[#121826]' : 'text-slate-500 hover:text-slate-800' }}">
                                Holy Gospel
                            </button>
                        </div>

                        <!-- Readings Scrollable Content -->
                        <div class="p-5 overflow-y-auto space-y-4 flex-1 text-sm leading-relaxed">
                            @if($readingsTab === 'reading1')
                                <!-- 1st Reading -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs text-purple-600 dark:text-purple-400 font-bold">
                                        <span>First Reading</span>
                                        <span>{{ $liturgicalContext['readings']['reading_1']['citation'] ?? '' }}</span>
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-200 font-serif leading-relaxed text-sm">
                                        {{ $liturgicalContext['readings']['reading_1']['text'] ?? '' }}
                                    </p>
                                </div>
                            @elseif($readingsTab === 'psalm')
                                <!-- Psalm -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs text-purple-600 dark:text-purple-400 font-bold">
                                        <span>Responsorial Psalm</span>
                                        <span>{{ $liturgicalContext['readings']['psalm']['citation'] ?? '' }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-xs font-semibold text-purple-900 dark:text-purple-200">
                                        <span class="font-bold uppercase text-[10px] block text-purple-600 dark:text-purple-400">Response:</span>
                                        &ldquo;{{ $liturgicalContext['readings']['psalm']['response'] ?? '' }}&rdquo;
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-200 font-serif leading-relaxed text-sm whitespace-pre-line">
                                        {{ $liturgicalContext['readings']['psalm']['text'] ?? '' }}
                                    </p>
                                </div>
                            @elseif($readingsTab === 'reading2' && !empty($liturgicalContext['readings']['reading_2']))
                                <!-- 2nd Reading -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs text-purple-600 dark:text-purple-400 font-bold">
                                        <span>Second Reading</span>
                                        <span>{{ $liturgicalContext['readings']['reading_2']['citation'] }}</span>
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-200 font-serif leading-relaxed text-sm">
                                        {{ $liturgicalContext['readings']['reading_2']['text'] }}
                                    </p>
                                </div>
                            @else
                                <!-- Gospel -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs text-purple-600 dark:text-purple-400 font-bold">
                                        <span>Holy Gospel</span>
                                        <span>{{ $liturgicalContext['readings']['gospel']['citation'] ?? '' }}</span>
                                    </div>
                                    @if(!empty($liturgicalContext['readings']['acclamation']))
                                        <div class="text-[11px] italic text-slate-500 dark:text-slate-400 border-l-2 border-purple-500 pl-2">
                                            Gospel Acclamation: &ldquo;{{ $liturgicalContext['readings']['acclamation']['verse'] }}&rdquo; ({{ $liturgicalContext['readings']['acclamation']['citation'] }})
                                        </div>
                                    @endif
                                    <p class="text-slate-700 dark:text-slate-200 font-serif leading-relaxed text-sm">
                                        {{ $liturgicalContext['readings']['gospel']['text'] ?? '' }}
                                    </p>
                                    @if(!empty($liturgicalContext['readings']['reflection']))
                                        <div class="mt-3 p-3 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs">
                                            <span class="font-bold text-purple-600 dark:text-purple-400 block mb-1">Spiritual Reflection:</span>
                                            <p class="text-slate-600 dark:text-slate-300">{{ $liturgicalContext['readings']['reflection'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-3 bg-slate-50 dark:bg-[#0d121f] border-t border-slate-200 dark:border-slate-800 flex justify-end">
                            <button type="button" wire:click="closeReadingsModal" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            @endif
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

        <!-- 4. TODAY'S DAILY FORMATION / CONTINUE YOUR FORMATION -->
        @php
            $displayLesson = $ongoingLesson ?? $microLesson;
            $isOngoing = !is_null($ongoingLesson);
        @endphp

        @if($displayLesson)
            <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3 shadow-xs">
                <!-- Header row: Status badge + Read duration -->
                <div class="flex items-center justify-between gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg {{ $isOngoing ? 'bg-purple-600 text-white' : 'bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800' }} font-bold uppercase text-[10px] tracking-wider whitespace-nowrap">
                        @if($isOngoing)
                            Continue Your Formation
                        @else
                            Today's Formation
                        @endif
                    </span>

                    <div class="flex items-center gap-1 text-slate-400 dark:text-slate-500 text-[11px] font-medium flex-shrink-0">
                        @if($isOngoing)
                            <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                            <span class="text-purple-600 dark:text-purple-400 font-semibold">In Progress</span>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $displayLesson->estimated_read_minutes ?? 5 }} min</span>
                        @endif
                    </div>
                </div>

                <!-- Track Name & Lesson Title -->
                <div class="space-y-1">
                    @if($displayLesson->category?->name)
                        <div class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            {{ $displayLesson->category->name }}
                        </div>
                    @endif
                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white leading-snug">
                        {{ $displayLesson->title }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                        {{ $displayLesson->summary ?? $displayLesson->subheading }}
                    </p>
                </div>

                <!-- Footer row: XP reward badge + Action button -->
                <div class="flex items-center justify-between pt-1">
                    <span class="px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-200/60 dark:border-purple-800/60 text-[11px] text-purple-700 dark:text-purple-300 font-bold">
                        {{ $isOngoing ? 'Formation in progress' : '+25 Formation XP' }}
                    </span>

                    <a href="/lesson/{{ $displayLesson->id }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-xs flex items-center gap-1.5 flex-shrink-0">
                        @if($isOngoing)
                            <span>Continue Lesson</span>
                        @elseif($microLessonCompleted)
                            <span>Review Micro-Lesson</span>
                        @else
                            <span>Start Micro-Lesson</span>
                        @endif
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
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
                <a href="{{ route('saints.index') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1">
                    <span>Explore All</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Horizontal Scroll Rail -->
            <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-1 -mx-4 px-4">
                @forelse($featuredSaints as $saint)
                    <div class="flex-shrink-0 w-64 p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2 flex flex-col justify-between shadow-xs hover:border-purple-300 dark:hover:border-purple-800/60 transition-all">
                        <div class="space-y-1">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase whitespace-nowrap
                                {{ $saint->slug === 'st-theresa-of-lisieux' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : ($saint->is_african_heritage ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800') }}">
                                {{ $saint->slug === 'st-theresa-of-lisieux' ? 'Diocesan Cathedral Patroness' : ($saint->is_african_heritage ? 'African Catholic Heritage' : 'Universal Doctor') }}
                            </span>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-1">{{ $saint->name }}</h4>
                            <p class="text-[11px] text-slate-500 line-clamp-2">{{ $saint->title_designation }} &bull; {{ $saint->biography }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px]">
                            <span class="text-slate-400">Feast: {{ $saint->feast_day_month_day ? \Carbon\Carbon::createFromFormat('m-d', $saint->feast_day_month_day)->format('M j') : 'Oct 1' }}</span>
                            <a href="{{ route('saints.show', $saint->slug) }}" class="text-purple-600 dark:text-purple-400 font-bold hover:underline">Learn &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-xs text-slate-400">No saints loaded.</div>
                @endforelse
            </div>
        </div>

        <!-- 6. RALLY PREPARATION (LIVINGSTONE DIOCESAN YOUTH RALLY 2026) -->
        @if($rallyPrep)
            <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-950/30 dark:to-indigo-950/30 border border-purple-200 dark:border-purple-900/60 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-bold uppercase text-[10px] tracking-wider">
                        {{ $rallyPrep->title }}
                    </span>
                    <span class="text-[11px] text-purple-700 dark:text-purple-300 font-semibold">Catholic Diocese of Livingstone</span>
                </div>

                <div>
                    <h4 class="text-base font-bold font-serif text-slate-900 dark:text-white">Prepare for the Rally</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                        {{ $rallyPrep->description ?? 'Represent your parish in the upcoming Diocesan Bible & Catechetical Quiz Tournament.' }}
                    </p>
                </div>

                <div class="pt-1 flex gap-2">
                    <a href="/quiz?tab=compete" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs text-center transition-colors touch-press shadow-sm">
                        Enter Rally Lobby &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- 7. DAILY SPIRITUAL BREAD -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-900 via-purple-950 to-indigo-950 text-white border border-purple-800/40 space-y-3 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10 pointer-events-none">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>

            <div class="relative z-10 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 text-[10px] font-bold uppercase tracking-wider border border-white/15">
                        Daily Spiritual Bread
                    </span>
                    <span class="text-[11px] text-purple-300 font-semibold">{{ $liturgicalContext['verse_ref'] ?? 'Holy Scripture' }}</span>
                </div>

                <div>
                    <blockquote class="font-serif italic text-xs sm:text-sm text-purple-50 leading-relaxed">
                        &ldquo;{{ $liturgicalContext['verse'] ?? 'Thy word is a lamp unto my feet, and a light unto my path.' }}&rdquo;
                    </blockquote>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-white/10 text-xs">
                    <span class="text-purple-200/80 text-[11px] truncate max-w-[200px]">
                        Patroness: {{ $liturgicalContext['diocesan_patroness'] ?? 'St. Theresa of Lisieux' }}
                    </span>
                    <a href="/study" class="text-white font-bold hover:underline flex items-center gap-1">
                        <span>Explore All Tracks</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 8. PARISH COMMUNITY FORMATION SPRINT -->
        @if($activeParishChallenge)
            <div class="p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-2.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Parish Formation Challenge
                    </span>
                    <span class="text-[10px] font-bold text-slate-400">{{ $user->parish?->name ?? 'Parish Challenge' }}</span>
                </div>

                <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $activeParishChallenge->title }}</h4>
                <p class="text-xs text-slate-500">{{ $activeParishChallenge->description }}</p>
            </div>
        @endif
    @endif

</div>
