<div class="space-y-5 pb-20">

    <!-- TOAST FEEDBACK NOTIFICATIONS -->
    @if($successMessage)
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => { show = false; $wire.set('successMessage', null); }, 2200)"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
             class="p-3 bg-emerald-500/10 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/80 rounded-2xl text-xs text-emerald-900 dark:text-emerald-100 font-semibold flex items-center justify-between shadow-lg shadow-emerald-500/5 backdrop-blur-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', null)" @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300 text-lg font-bold p-1 rounded-lg transition-colors leading-none">&times;</button>
        </div>
    @endif

    @if($errorMessage)
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => { show = false; $wire.set('errorMessage', null); }, 3000)"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
             class="p-3 bg-rose-500/10 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/80 rounded-2xl text-xs text-rose-900 dark:text-rose-100 font-semibold flex items-center justify-between shadow-lg shadow-rose-500/5 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', null)" @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 text-lg font-bold p-1 rounded-lg transition-colors">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. PARISH COMMAND CENTER HEADER                                           -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/profile" class="relative w-12 h-12 rounded-full overflow-hidden border-2 border-purple-300 dark:border-purple-700 bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center flex-shrink-0 shadow-sm aspect-square" title="Parish Chairperson Profile">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full aspect-square">
                    @else
                        <span class="font-bold text-base text-purple-700 dark:text-purple-300">{{ $user->initials }}</span>
                    @endif
                </a>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                        Parish Youth Ministry
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-0.5">{{ $parish->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Chairperson: {{ $user->name }} &bull; {{ $parish->deanery?->name }}</p>
                </div>
            </div>

            <button 
                type="button" 
                wire:click="generateReport"
                class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-xs font-semibold flex items-center gap-1 transition-colors border border-purple-200 dark:border-purple-900/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Report</span>
            </button>
        </div>

        <!-- QUICK MANAGEMENT ACTIONS BAR -->
        <div class="grid grid-cols-4 gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button 
                type="button" 
                wire:click="$set('showAddYouthModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Youth</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showChallengeModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Challenge</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showAnnouncementModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Post Note</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showEventModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Event</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. SCROLLABLE SEGMENTED NAVIGATION TABS                                   -->
    <!-- ========================================================================= -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 hide-scrollbar text-xs font-semibold">
        <button 
            type="button" 
            wire:click="setTab('overview')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'overview' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Overview
        </button>
        <button 
            type="button" 
            wire:click="setTab('youth')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'youth' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Youth Roster ({{ $kpis['total_youth'] }}) @if($pendingYouths->isNotEmpty())<span class="ml-1 px-1.5 py-0.2 bg-amber-500 text-white rounded-full text-[9px]">{{ $pendingYouths->count() }}</span>@endif
        </button>
        <button 
            type="button" 
            wire:click="setTab('leaderboard')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'leaderboard' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Leaderboard &amp; Mastery
        </button>
        <button 
            type="button" 
            wire:click="setTab('challenges')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'challenges' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Challenges ({{ $activeChallenges->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('communication')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'communication' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Announcements ({{ $announcements->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('activities')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'activities' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Events &amp; Quizzes
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. TAB CONTENT VIEWS                                                      -->
    <!-- ========================================================================= -->

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB A: OVERVIEW & PARISH KPIS                                             -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'overview')
        <div class="space-y-4">
            <!-- 6-GRID MAIN METRICS -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Parish Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $kpis['total_youth'] }}</span>
                        <span class="text-xs text-slate-500 font-medium">Members</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-1">
                        {{ $kpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Parish Engagement</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ $kpis['engagement_level'] }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1">
                        {{ $kpis['engagement_pct'] }}% of youth active
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lessons Completed</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $kpis['lessons_completed'] }}</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 font-medium block mt-1">
                        Catechetical formation tracks
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Quizzes Completed</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $kpis['quizzes_completed'] }}</span>
                    </div>
                    <span class="text-[11px] text-purple-600 font-medium block mt-1">
                        Avg Accuracy: {{ $kpis['avg_quiz_score'] }}%
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Parish XP</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($kpis['total_parish_xp']) }} XP</span>
                    </div>
                    <span class="text-[11px] text-amber-600 font-medium block mt-1">
                        Formation points
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Average Quiz Score</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ $kpis['avg_quiz_score'] }} pts</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 font-semibold block mt-1">
                        Accuracy: {{ $kpis['avg_quiz_score'] }}%
                    </span>
                </div>
            </div>

            <!-- PARISH ACTION ITEMS / ATTENTION REQUIRED -->
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Parish Action Items &bull; Attention Required
                    </h3>
                </div>

                <div class="space-y-2.5 text-xs">
                    <!-- Inactive Youth Item -->
                    <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Inactive Youth</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $kpis['inactive_youth_count'] }} youth inactive (14+ days) &bull; Needs motivation</span>
                        </div>
                        <button type="button" wire:click="setTab('youth')" class="text-purple-600 dark:text-purple-400 font-bold text-[11px] hover:underline">
                            View &rarr;
                        </button>
                    </div>

                    <!-- Pending Registrations Item -->
                    <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Registrations</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $kpis['pending_approvals_count'] }} pending verification &bull; Waiting for sign-off</span>
                        </div>
                        <button type="button" wire:click="setTab('youth')" class="text-purple-600 dark:text-purple-400 font-bold text-[11px] hover:underline">
                            Review &rarr;
                        </button>
                    </div>

                    <!-- Daily Challenge Item -->
                    <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Daily Formation Challenge</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $kpis['challenge_completed_today'] ?? 0 }} completed today &bull; +50 XP Streak</span>
                        </div>
                        <button type="button" wire:click="setTab('challenges')" class="text-purple-600 dark:text-purple-400 font-bold text-[11px] hover:underline">
                            Remind &rarr;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB B: YOUTH ROSTER & PENDING APPROVALS                                    -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'youth')
        <div class="space-y-4">
            <!-- PENDING REGISTRATIONS QUEUE -->
            @if($pendingYouths->isNotEmpty())
                <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-xl space-y-3">
                    <h3 class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                        Pending Youth Approvals ({{ $pendingYouths->count() }})
                    </h3>

                    <div class="space-y-2">
                        @foreach($pendingYouths as $pending)
                            <div class="p-3 bg-white dark:bg-[#121826] border border-amber-200 dark:border-amber-900/50 rounded-lg text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $pending->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $pending->phone }}</span>
                                </div>
                                <div class="flex gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                                    <button 
                                        type="button" 
                                        wire:click="approveYouth('{{ $pending->id }}')" 
                                        class="w-1/2 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded text-[11px]">
                                        Approve
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="rejectYouth('{{ $pending->id }}')" 
                                        class="w-1/2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded text-[11px]">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- PARISH YOUTH ROSTER DIRECTORY -->
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" 
                        placeholder="Search parish youth..." 
                        class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400">
                    <button 
                        type="button" 
                        wire:click="$set('showAddYouthModal', true)" 
                        class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold whitespace-nowrap">
                        + Add Youth
                    </button>
                </div>

                <div class="space-y-2">
                    @foreach($youths as $y)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $y->name }}</h4>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $y->phone }} &bull; {{ $y->email ?? 'No email' }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $y->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300' }}">
                                    {{ ucfirst($y->status) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800 text-[11px]">
                                <span class="text-purple-600 dark:text-purple-400 font-semibold">Level {{ $y->level }} &bull; {{ number_format($y->xp) }} XP</span>
                                
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        wire:click="toggleYouthStatus('{{ $y->id }}')" 
                                        class="text-slate-500 hover:text-slate-800 dark:hover:text-white font-medium text-[11px]">
                                        {{ $y->status === 'suspended' ? 'Reactivate' : 'Suspend' }}
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="$set('transferUserId', '{{ $y->id }}'); $set('showTransferModal', true);" 
                                        class="text-purple-600 dark:text-purple-400 font-semibold text-[11px] hover:underline">
                                        Transfer &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB C: LEADERBOARD & STUDY TRACK MASTERY                                   -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'leaderboard')
        <div class="space-y-4">
            <!-- LEADERBOARD ROSTER -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Parish Youth Leaderboard
                </h3>

                <div class="space-y-2">
                    @forelse($leaderboard as $index => $leader)
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $leader->name }}</span>
                                    <span class="text-[10px] text-slate-400">Level {{ $leader->level }} &bull; {{ $leader->streak_count }} day streak</span>
                                </div>
                            </div>
                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($leader->xp) }} XP</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2 text-center">No active youth rankings yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- CATECHETICAL TOPIC MASTERY -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Study Track &amp; Doctrinal Performance
                </h3>

                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $cat->name }}</span>
                                <span class="text-purple-600 dark:text-purple-400 font-semibold">{{ $cat->questions_count }} Questions</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-purple-600 h-full rounded-full" style="width: 75%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB D: FORMATION CHALLENGES                                               -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'challenges')
        <div class="space-y-3">
            <div class="flex justify-end">
                <button 
                    type="button" 
                    wire:click="$set('showChallengeModal', true)" 
                    class="px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold">
                    + Launch New Challenge
                </button>
            </div>

            <div class="space-y-2">
                @forelse($activeChallenges as $ch)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $ch->title }}</h4>
                            <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 rounded font-bold text-[10px]">
                                +{{ $ch->xp_reward }} XP Reward
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $ch->description }}</p>
                        <div class="space-y-1 pt-1">
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Progress: {{ $ch->current_value }} / {{ $ch->target_value }}</span>
                                <span>Ends: {{ $ch->ends_at?->format('M d, Y') }}</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, ($ch->current_value / max(1, $ch->target_value)) * 100) }}%;"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No active challenges. Launch one for your youth!</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB E: PARISH ANNOUNCEMENTS                                               -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'communication')
        <div class="space-y-3">
            <div class="flex justify-end">
                <button 
                    type="button" 
                    wire:click="$set('showAnnouncementModal', true)" 
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold">
                    + Post Announcement
                </button>
            </div>

            <div class="space-y-2">
                @forelse($announcements as $ann)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $ann->title }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ann->priority === 'urgent' ? 'bg-red-50 text-red-700 dark:bg-red-950/40' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/40' }}">
                                {{ $ann->priority }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300">{{ $ann->content }}</p>
                        <span class="text-[10px] text-slate-400 block pt-1 border-t border-slate-100 dark:border-slate-800">
                            Posted {{ $ann->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No announcements posted yet.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB F: EVENTS & PARISH QUIZZES                                            -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'activities')
        <div class="space-y-4">
            <!-- SCHEDULED EVENTS -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Parish Events</h3>
                    <button type="button" wire:click="$set('showEventModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">
                        + Add Event
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($events as $ev)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $ev->title }}</h4>
                                <span class="text-[10px] text-purple-600 dark:text-purple-400 font-bold">{{ $ev->event_date?->format('M d, Y') }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $ev->description }}</p>
                            <span class="text-[10px] text-slate-400 block">Location: {{ $ev->location }} &bull; Organizer: {{ $ev->organizer }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2 text-center">No upcoming events scheduled.</p>
                    @endforelse
                </div>
            </div>

            <!-- PARISH QUIZZES & RALLIES -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Parish Competitions &amp; Quizzes</h3>
                    <button type="button" wire:click="$set('showQuizModal', true)" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                        + Host Quiz
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($parishCompetitions as $pq)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $pq->title }}</h4>
                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px]">
                                    PIN: {{ $pq->rally_pin }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $pq->description }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2 text-center">No active parish competitions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: ADD YOUTH MODAL                                                  -->
    <!-- ========================================================================= -->
    @if($showAddYouthModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Register Youth to Parish</h3>
                    <button wire:click="$set('showAddYouthModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="addYouth" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" wire:model="newYouthName" placeholder="e.g. Mary Banda" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newYouthName') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (+260...)</label>
                        <input type="text" wire:model="newYouthPhone" placeholder="+260970000000" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newYouthPhone') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" wire:model="newYouthEmail" placeholder="youth@email.com" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showAddYouthModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Register Youth
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: TRANSFER YOUTH MODAL                                             -->
    <!-- ========================================================================= -->
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Request Youth Transfer</h3>
                    <button wire:click="$set('showTransferModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="requestTransfer" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Destination Parish</label>
                        <select wire:model="transferToParishId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="">Select Target Parish</option>
                            @foreach($allParishes as $ap)
                                <option value="{{ $ap->id }}">{{ $ap->name }} ({{ $ap->deanery?->name }})</option>
                            @endforeach
                        </select>
                        @error('transferToParishId') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason for Transfer</label>
                        <textarea wire:model="transferReason" rows="2" placeholder="e.g. Relocating with family..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        @error('transferReason') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showTransferModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
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
    <!-- MODAL 3: CREATE CHALLENGE MODAL                                           -->
    <!-- ========================================================================= -->
    @if($showChallengeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Create Parish Challenge</h3>
                    <button wire:click="$set('showChallengeModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createChallenge" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Challenge Title</label>
                        <input type="text" wire:model="challengeTitle" placeholder="e.g. Lent 5,000 XP Formation Race" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('challengeTitle') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <textarea wire:model="challengeDescription" rows="2" placeholder="Let us achieve this collective goal together!" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Target XP</label>
                            <input type="number" wire:model="challengeTarget" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Reward XP</label>
                            <input type="number" wire:model="challengeXpReward" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showChallengeModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold">
                            Launch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: POST ANNOUNCEMENT MODAL                                          -->
    <!-- ========================================================================= -->
    @if($showAnnouncementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Post Parish Announcement</h3>
                    <button wire:click="$set('showAnnouncementModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="postAnnouncement" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Title</label>
                        <input type="text" wire:model="announcementTitle" placeholder="e.g. Youth Mass &amp; Choir Practice" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Content</label>
                        <textarea wire:model="announcementContent" rows="3" placeholder="Announcement details..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                        <select wire:model="announcementPriority" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showAnnouncementModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold">
                            Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 5: SCHEDULE EVENT MODAL                                             -->
    <!-- ========================================================================= -->
    @if($showEventModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Schedule Parish Event</h3>
                    <button wire:click="$set('showEventModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="scheduleEvent" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Event Title</label>
                        <input type="text" wire:model="eventTitle" placeholder="e.g. Saturday Youth Catechism Circle" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Location</label>
                        <input type="text" wire:model="eventLocation" placeholder="e.g. St. Theresa Parish Hall" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Event Date</label>
                        <input type="date" wire:model="eventDate" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <textarea wire:model="eventDescription" rows="2" placeholder="Event details..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showEventModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                            Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 6: HOST PARISH QUIZ MODAL                                           -->
    <!-- ========================================================================= -->
    @if($showQuizModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Host Parish Formation Quiz</h3>
                    <button wire:click="$set('showQuizModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createParishQuiz" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Quiz Title</label>
                        <input type="text" wire:model="quizTitle" placeholder="e.g. Parish Youth Sacraments Challenge" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Track Category</label>
                        <select wire:model="quizCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showQuizModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                            Launch Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 7: MONTHLY REPORT SUMMARY MODAL                                     -->
    <!-- ========================================================================= -->
    @if($showReportModal && $monthlyReportData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Monthly Parish Formation Report</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/30 rounded-lg space-y-1 text-purple-950 dark:text-purple-200">
                        <div class="flex justify-between">
                            <span>Total Youth:</span>
                            <strong>{{ $monthlyReportData['total_youth'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Active This Month:</span>
                            <strong>{{ $monthlyReportData['active_youth'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Quizzes Attempted:</span>
                            <strong>{{ $monthlyReportData['quizzes_completed'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Average Accuracy:</span>
                            <strong>{{ $monthlyReportData['avg_accuracy'] }}%</strong>
                        </div>
                    </div>
                </div>

                <button type="button" wire:click="$set('showReportModal', false)" class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                    Close Report
                </button>
            </div>
        </div>
    @endif

</div>
