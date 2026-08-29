<div class="space-y-5 pb-20">

    <!-- TOAST FEEDBACK NOTIFICATIONS -->
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
    <!-- 1. DIOCESAN COMMAND CENTER HEADER                                         -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-lg border border-purple-200 dark:border-purple-800 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                        Diocesan Command Center
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-0.5">Livingstone Diocese</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Super Admin: {{ $user->name }}</p>
                </div>
            </div>

            <button 
                type="button" 
                wire:click="generateExecutiveReport"
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
                wire:click="$set('showParishModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Parish</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showAdminModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Admin</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showQuestionModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Question</span>
            </button>

            <button 
                type="button" 
                wire:click="$set('showCompetitionModal', true)"
                class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 text-center transition-colors border border-slate-200 dark:border-slate-800">
                <svg class="w-4 h-4 mx-auto mb-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="text-[10px] font-bold block leading-tight">+ Rally</span>
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
            wire:click="setTab('parishes')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'parishes' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Parishes ({{ $parishes->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('admins')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'admins' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Admins ({{ $admins->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('youth')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'youth' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Youth &amp; Transfers @if($pendingTransfers->isNotEmpty())<span class="ml-1 px-1.5 py-0.2 bg-amber-500 text-white rounded-full text-[9px]">{{ $pendingTransfers->count() }}</span>@endif
        </button>
        <button 
            type="button" 
            wire:click="setTab('questions')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'questions' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Questions Bank
        </button>
        <button 
            type="button" 
            wire:click="setTab('competitions')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'competitions' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Competitions
        </button>
        <button 
            type="button" 
            wire:click="setTab('reports')"
            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'reports' ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Audit Trail
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. TAB CONTENT VIEWS                                                      -->
    <!-- ========================================================================= -->

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB A: OVERVIEW & DIOCESAN KPIS                                           -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'overview')
        <div class="space-y-4">
            <!-- 4-GRID MAIN METRICS -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Diocesan Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($kpis['total_youth']) }}</span>
                        <span class="text-xs text-slate-500 font-medium">members</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-1">
                        {{ $kpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formation Mastery</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ $kpis['average_mastery'] }}%</span>
                        <span class="text-xs text-slate-500 font-medium">accuracy</span>
                    </div>
                    <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold block mt-1">
                        {{ number_format($kpis['total_xp']) }} total XP
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Parishes &amp; Admins</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $kpis['total_parishes'] }}</span>
                        <span class="text-xs text-slate-500 font-medium">parishes</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1">
                        {{ $kpis['total_chairpersons'] }} active admins
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Quizzes Answered</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($kpis['quizzes_completed']) }}</span>
                        <span class="text-xs text-slate-500 font-medium">sessions</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1">
                        {{ number_format($kpis['lessons_completed']) }} lessons finished
                    </span>
                </div>
            </div>

            <!-- DEANERIES OVERVIEW -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Deaneries &amp; Territory Structure
                </h3>

                <div class="space-y-2">
                    @foreach($deaneries as $deanery)
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $deanery->name }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $deanery->parishes_count }} Parishes assigned</span>
                            </div>
                            <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[11px]">
                                Code: {{ $deanery->code }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB B: PARISHES DIRECTORY                                                 -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'parishes')
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search parishes..." 
                    class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400">
                <button 
                    type="button" 
                    wire:click="$set('showParishModal', true)" 
                    class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold whitespace-nowrap">
                    + Add Parish
                </button>
            </div>

            <div class="space-y-2">
                @foreach($parishes as $parish)
                    <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $parish->name }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Active
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-1 text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Deanery: {{ $parish->deanery?->name }}</span>
                            <span>Youth Members: {{ $parish->youth_count }}</span>
                            <span>Code: {{ $parish->code }}</span>
                            <span>Location: {{ $parish->location ?? 'Livingstone' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB C: PARISH ADMINISTRATORS DIRECTORY                                    -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'admins')
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search administrators..." 
                    class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400">
                <button 
                    type="button" 
                    wire:click="$set('showAdminModal', true)" 
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold whitespace-nowrap">
                    + Add Admin
                </button>
            </div>

            <div class="space-y-2">
                @foreach($admins as $admin)
                    <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $admin->name }}</span>
                            <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold block">{{ $admin->parish?->name ?? 'Unassigned' }}</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">{{ $admin->phone }} &bull; {{ $admin->email }}</span>
                        </div>
                        <span class="px-2 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px] uppercase">
                            {{ $admin->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB D: YOUTH & TRANSFERS QUEUE                                            -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'youth')
        <div class="space-y-4">
            <!-- PENDING INTER-PARISH TRANSFERS -->
            @if($pendingTransfers->isNotEmpty())
                <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-xl space-y-3">
                    <h3 class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                        Pending Parish Transfers ({{ $pendingTransfers->count() }})
                    </h3>

                    <div class="space-y-2">
                        @foreach($pendingTransfers as $trans)
                            <div class="p-3 bg-white dark:bg-[#121826] border border-amber-200 dark:border-amber-900/50 rounded-lg text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $trans->user?->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $trans->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-[11px] text-slate-600 dark:text-slate-300">
                                    Transfer: <strong class="text-slate-900 dark:text-white">{{ $trans->fromParish?->name }}</strong> &rarr; <strong class="text-purple-600 dark:text-purple-400">{{ $trans->toParish?->name }}</strong>
                                    <p class="text-[10px] text-slate-400 italic mt-0.5">Reason: {{ $trans->reason }}</p>
                                </div>
                                <div class="flex gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                                    <button 
                                        type="button" 
                                        wire:click="approveTransfer('{{ $trans->id }}')" 
                                        class="w-1/2 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded text-[11px]">
                                        Approve Transfer
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="rejectTransfer('{{ $trans->id }}')" 
                                        class="w-1/2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded text-[11px]">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- DIOCESAN YOUTH LIST -->
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-2">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" 
                        placeholder="Search youth by name or phone..." 
                        class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="space-y-2">
                    @foreach($youths as $yUser)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $yUser->name }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $yUser->parish?->name }} &bull; {{ $yUser->phone }}</span>
                                <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold block mt-0.5">Level {{ $yUser->level }} &bull; {{ number_format($yUser->xp) }} XP</span>
                            </div>
                            <span class="px-2 py-1 rounded text-[10px] font-bold {{ $yUser->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                {{ ucfirst($yUser->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB E: UNIVERSAL QUESTION BANK & CURRICULUM                               -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'questions')
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search question bank..." 
                    class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                <button 
                    type="button" 
                    wire:click="$set('showQuestionModal', true)" 
                    class="px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold whitespace-nowrap">
                    + Question
                </button>
            </div>

            <!-- CURRICULUM TRACKS BADGES -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 hide-scrollbar">
                @foreach($categories as $cat)
                    <div class="px-3 py-1.5 rounded-lg bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-[11px] whitespace-nowrap flex-shrink-0 font-medium text-slate-700 dark:text-slate-300">
                        <span>{{ $cat->name }}</span>
                        <span class="ml-1 text-purple-600 dark:text-purple-400 font-bold">({{ $cat->questions_count }} Qs)</span>
                    </div>
                @endforeach
            </div>

            <!-- QUESTION BANK REPOSITORY -->
            <div class="space-y-2">
                @foreach($questions as $qItem)
                    <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase text-purple-600 dark:text-purple-400 tracking-wider">
                                {{ $qItem->category?->name ?? 'General Catholic Doctrine' }}
                            </span>
                            <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">
                                Level {{ $qItem->level }}
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white">{{ $qItem->question_text }}</h4>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg text-[11px] text-slate-600 dark:text-slate-300 space-y-1">
                            <div><strong>Correct Answer:</strong> Option {{ $qItem->correct_option_key }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400"><em>{{ $qItem->explanation }}</em></div>
                            @if($qItem->reference_citation)
                                <div class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Citation: {{ $qItem->reference_citation }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB F: COMPETITIONS & RALLIES                                             -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'competitions')
        <div class="space-y-3">
            <div class="flex justify-end">
                <button 
                    type="button" 
                    wire:click="$set('showCompetitionModal', true)" 
                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">
                    + Schedule New Competition
                </button>
            </div>

            <div class="space-y-2">
                @foreach($competitions as $comp)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $comp->title }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300">
                                {{ $comp->competition_type }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $comp->description }}</p>
                        <div class="grid grid-cols-2 gap-1 text-[11px] text-slate-600 dark:text-slate-300 pt-1 border-t border-slate-100 dark:border-slate-800">
                            <span>Rally PIN: <strong class="text-purple-600 dark:text-purple-400 font-bold">{{ $comp->rally_pin }}</strong></span>
                            <span>Time Limit: {{ $comp->time_limit_seconds }}s</span>
                            <span>Start: {{ $comp->start_time?->format('M d, Y') }}</span>
                            <span>Status: <strong class="text-emerald-600">{{ ucfirst($comp->status) }}</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------------------- -->
    <!-- TAB G: AUDIT LOG TRAIL                                                    -->
    <!-- ------------------------------------------------------------------------- -->
    @if($activeTab === 'reports')
        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Diocesan System Audit Trail
            </h3>

            <div class="space-y-2 text-xs">
                @foreach($auditLogs as $log)
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">{{ str_replace('_', ' ', ucwords($log->action)) }}</span>
                            <span class="text-[10px] text-slate-400">By: {{ $log->user?->name ?? 'System' }} &bull; {{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="text-[10px] font-mono text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: ADD PARISH MODAL                                                 -->
    <!-- ========================================================================= -->
    @if($showParishModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Register New Parish</h3>
                    <button wire:click="$set('showParishModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createParish" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Parish Name</label>
                        <input type="text" wire:model="newParishName" placeholder="e.g. St. Jude Parish" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newParishName') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Code</label>
                            <input type="text" wire:model="newParishCode" placeholder="e.g. STJ" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            @error('newParishCode') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Deanery</label>
                            <select wire:model="newParishDeaneryId" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                                <option value="">Select</option>
                                @foreach($deaneries as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('newParishDeaneryId') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showParishModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                            Save Parish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: ADD PARISH ADMIN MODAL                                           -->
    <!-- ========================================================================= -->
    @if($showAdminModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Assign Parish Chairperson / Admin</h3>
                    <button wire:click="$set('showAdminModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createParishAdmin" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" wire:model="newAdminName" placeholder="e.g. John Mukuka" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newAdminName') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (+260...)</label>
                        <input type="text" wire:model="newAdminPhone" placeholder="+260970000000" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newAdminPhone') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" wire:model="newAdminEmail" placeholder="admin@parish.org" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Assigned Parish</label>
                        <select wire:model="newAdminParishId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="">Select Parish</option>
                            @foreach($parishes as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->deanery?->name }})</option>
                            @endforeach
                        </select>
                        @error('newAdminParishId') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showAdminModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold">
                            Create Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: ADD QUESTION MODAL                                               -->
    <!-- ========================================================================= -->
    @if($showQuestionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Add Formation Question</h3>
                    <button wire:click="$set('showQuestionModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createQuestion" class="space-y-2.5">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Curriculum Track</label>
                        <select wire:model="newQuestionCategoryId" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="">Select Track</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('newQuestionCategoryId') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Question Prompt</label>
                        <textarea wire:model="newQuestionText" rows="2" placeholder="e.g. Which Catholic Sacrament is the source and summit of Christian life?" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        @error('newQuestionText') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">Options</label>
                        <input type="text" wire:model="optionA" placeholder="Option A (e.g. Baptism)" class="w-full px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md text-xs">
                        <input type="text" wire:model="optionB" placeholder="Option B (e.g. The Holy Eucharist)" class="w-full px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md text-xs">
                        <input type="text" wire:model="optionC" placeholder="Option C (e.g. Confirmation)" class="w-full px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md text-xs">
                        <input type="text" wire:model="optionD" placeholder="Option D (e.g. Holy Orders)" class="w-full px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Correct Option</label>
                            <select wire:model="correctOption" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Difficulty</label>
                            <select wire:model="newQuestionLevel" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs">
                                <option value="1">1 (Beginner)</option>
                                <option value="2">2 (Intermediate)</option>
                                <option value="3">3 (Advanced)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Doctrinal Explanation</label>
                        <textarea wire:model="newQuestionExplanation" rows="2" placeholder="Explain the theological rationale..." class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showQuestionModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold">
                            Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: SCHEDULE COMPETITION MODAL                                       -->
    <!-- ========================================================================= -->
    @if($showCompetitionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Schedule Diocesan Rally / Competition</h3>
                    <button wire:click="$set('showCompetitionModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form wire:submit.prevent="createCompetition" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Rally Title</label>
                        <input type="text" wire:model="newCompTitle" placeholder="e.g. 2026 Livingstone Youth Bible Rally" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        @error('newCompTitle') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <textarea wire:model="newCompDescription" rows="2" placeholder="Competition guidelines..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Type</label>
                            <select wire:model="newCompType" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                                <option value="diocesan">Diocesan Rally</option>
                                <option value="deanery">Deanery Stage</option>
                                <option value="parish">Parish Level</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Time Limit (Sec)</label>
                            <input type="number" wire:model="newCompTimeLimit" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showCompetitionModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                            Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 5: EXECUTIVE REPORT MODAL                                           -->
    <!-- ========================================================================= -->
    @if($showReportModal && $reportSummary)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Diocesan Formation Executive Report</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/30 rounded-lg space-y-1 text-purple-950 dark:text-purple-200">
                        <div class="flex justify-between">
                            <span>Total Youth:</span>
                            <strong>{{ number_format($reportSummary['kpis']['total_youth']) }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Active This Week:</span>
                            <strong>{{ number_format($reportSummary['kpis']['active_this_week']) }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Average Mastery:</span>
                            <strong>{{ $reportSummary['kpis']['average_mastery'] }}%</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Quizzes Completed:</span>
                            <strong>{{ number_format($reportSummary['kpis']['quizzes_completed']) }}</strong>
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
