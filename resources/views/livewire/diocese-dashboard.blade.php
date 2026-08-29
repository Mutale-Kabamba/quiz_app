<div class="space-y-5 pb-20">

    <!-- TOAST FEEDBACK NOTIFICATIONS -->
    @if($successMessage)
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700 text-base font-bold">&times;</button>
        </div>
    @endif

    @if($errorMessage)
        <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl text-xs text-red-800 dark:text-red-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', null)" class="text-red-500 hover:text-red-700 text-base font-bold">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. DIOCESAN COMMAND CENTER HEADER                                         -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/profile" class="relative w-12 h-12 rounded-full overflow-hidden border-2 border-purple-300 dark:border-purple-700 bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center flex-shrink-0 shadow-sm aspect-square" title="Super Admin Profile">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full aspect-square">
                    @else
                        <span class="font-bold text-base text-purple-700 dark:text-purple-300">{{ $user->initials }}</span>
                    @endif
                </a>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded border border-purple-200 dark:border-purple-800">
                        Diocesan Command Center
                    </span>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-0.5">Livingstone Diocese</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Super Admin: {{ $user->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    wire:click="generateExecutiveReport"
                    class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-xs font-semibold flex items-center gap-1 transition-colors border border-purple-200 dark:border-purple-900/50 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Report</span>
                </button>
            </div>
        </div>

        <!-- QUICK MANAGEMENT ACTIONS BAR (6 BUTTONS WITH SYMBOL ICONS & CLEAN Q&A) -->
        <div class="grid grid-cols-6 gap-1 pt-2.5 border-t border-slate-100 dark:border-slate-800 text-center">
            <button 
                type="button" 
                wire:click="openCreateDeaneryModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-indigo-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Add Deanery">
                <svg class="w-4 h-4 mx-auto mb-1 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Deanery</span>
            </button>

            <button 
                type="button" 
                wire:click="openCreateParishModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-purple-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Add Parish">
                <svg class="w-4 h-4 mx-auto mb-1 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Parish</span>
            </button>

            <button 
                type="button" 
                wire:click="openCreateTrackModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-cyan-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Add Track">
                <svg class="w-4 h-4 mx-auto mb-1 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Track</span>
            </button>

            <button 
                type="button" 
                wire:click="openCreateLessonModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-emerald-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Add Study Lesson">
                <svg class="w-4 h-4 mx-auto mb-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Lesson</span>
            </button>

            <button 
                type="button" 
                wire:click="openCreateQuestionModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-amber-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Add Q&A">
                <svg class="w-4 h-4 mx-auto mb-1 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Q&A</span>
            </button>

            <button 
                type="button" 
                wire:click="openCreateCompetitionModal"
                class="py-2 px-1 rounded-lg bg-slate-50 hover:bg-rose-50/70 dark:bg-slate-900 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200 transition-colors border border-slate-200 dark:border-slate-800 shadow-xs touch-press"
                title="Schedule Rally">
                <svg class="w-4 h-4 mx-auto mb-1 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span class="text-[9px] sm:text-[10px] font-semibold block leading-tight truncate">+ Rally</span>
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
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'overview' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Overview
        </button>
        <button 
            type="button" 
            wire:click="setTab('deaneries')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'deaneries' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Deaneries ({{ $deaneries->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('parishes')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'parishes' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Parishes ({{ $parishes->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('tracks')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'tracks' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Tracks ({{ $tracks->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('lessons')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'lessons' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Lessons ({{ $lessons->total() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('questions')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'questions' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Q&amp;A Bank ({{ $questions->total() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('competitions')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'competitions' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Rallies ({{ $competitions->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('admins')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'admins' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Admins ({{ $admins->count() }})
        </button>
        <button 
            type="button" 
            wire:click="setTab('youth')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'youth' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Youth &amp; Transfers @if($pendingTransfers->isNotEmpty())<span class="ml-1 px-1.5 py-0.2 bg-amber-500 text-white rounded-full text-[9px]">{{ $pendingTransfers->count() }}</span>@endif
        </button>
        <button 
            type="button" 
            wire:click="setTab('reports')"
            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ $activeTab === 'reports' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Audit Trail
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. TAB CONTENT VIEWS                                                      -->
    <!-- ========================================================================= -->

    <!-- TAB 1: OVERVIEW -->
    @if($activeTab === 'overview')
        <div class="space-y-4 animate-fade-in">
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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Parishes &amp; Deaneries</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $kpis['total_parishes'] }}</span>
                        <span class="text-xs text-slate-500 font-medium">parishes in {{ $deaneries->count() }} deaneries</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1">
                        {{ $kpis['total_chairpersons'] }} active admins
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Quizzes Answered</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($kpis['quizzes_completed']) }}</span>
                        <span class="text-xs text-slate-500 font-medium">attempts</span>
                    </div>
                    <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold block mt-1">
                        {{ $kpis['ranked_sessions'] ?? 0 }} ranked sessions
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: DEANERIES CRUD -->
    @if($activeTab === 'deaneries')
        <div class="space-y-3 animate-fade-in">
            <div class="flex items-center justify-between">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search deaneries..."
                    class="w-2/3 px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600">

                <button 
                    type="button" 
                    wire:click="openCreateDeaneryModal"
                    class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm">
                    <span>+ Add Deanery</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse($deaneries as $deanery)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-mono text-[10px] font-bold">
                                    {{ $deanery->code }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $deanery->name }}</h3>
                                @if($deanery->headquarters)
                                    <p class="text-xs text-slate-500">HQ: {{ $deanery->headquarters }}</p>
                                @endif
                            </div>

                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-700 dark:text-slate-300">
                                {{ $deanery->parishes_count }} Parishes
                            </span>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="editDeanery({{ $deanery->id }})"
                                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg">
                                Edit
                            </button>
                            <button 
                                type="button" 
                                wire:confirm="Are you sure you want to delete '{{ $deanery->name }}'?"
                                wire:click="deleteDeanery({{ $deanery->id }})"
                                class="px-2.5 py-1 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-lg">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs col-span-2">No deaneries found.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: PARISHES CRUD -->
    @if($activeTab === 'parishes')
        <div class="space-y-3 animate-fade-in">
            <!-- Search & Actions Toolbar -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Search parishes..."
                            class="w-full pl-9 pr-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-xs">
                    </div>

                    <button 
                        type="button" 
                        wire:click="openCreateParishModal"
                        class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap flex-shrink-0 touch-press">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Add Parish</span>
                    </button>
                </div>

                <div>
                    <select 
                        wire:model.live="selectedDeaneryFilter"
                        class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 font-medium shadow-xs focus:outline-none focus:border-purple-600">
                        <option value="">All Deaneries</option>
                        @foreach($deaneries as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($parishes as $parish)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-mono text-[10px] font-bold">
                                    {{ $parish->code }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $parish->name }}</h3>
                            </div>
                            <p class="text-xs text-slate-500">
                                Deanery: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $parish->deanery?->name ?? 'Unassigned' }}</span> &bull; {{ $parish->youth_count }} Youth members
                            </p>
                        </div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button 
                                type="button" 
                                wire:click="editParish({{ $parish->id }})"
                                class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                                Edit
                            </button>
                            <button 
                                type="button" 
                                wire:confirm="Are you sure you want to delete '{{ $parish->name }}'?"
                                wire:click="deleteParish({{ $parish->id }})"
                                class="px-3 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">No parishes match your filter.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 4: TRACKS & TAXONOMY CRUD -->
    @if($activeTab === 'tracks')
        <div class="space-y-3 animate-fade-in">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" 
                        placeholder="Search formation tracks..."
                        class="w-full pl-9 pr-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-xs">
                </div>

                <button 
                    type="button" 
                    wire:click="openCreateTrackModal"
                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap flex-shrink-0 touch-press">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Add Track</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse($tracks as $track)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-xs">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded-lg bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 font-mono text-[10px] font-bold">
                                    Order: {{ $track->display_order }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $track->name }}</h3>
                                @if($track->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 mt-0.5">{{ $track->description }}</p>
                                @endif
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-700 dark:text-slate-300 flex-shrink-0">
                                {{ $track->questions_count }} Qs
                            </span>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="editTrack({{ $track->id }})"
                                class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                                Edit
                            </button>
                            <button 
                                type="button" 
                                wire:confirm="Are you sure you want to delete track '{{ $track->name }}'?"
                                wire:click="deleteTrack({{ $track->id }})"
                                class="px-3 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl col-span-2">No tracks found.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 5: STUDY LESSONS CRUD -->
    @if($activeTab === 'lessons')
        <div class="space-y-3 animate-fade-in">
            <!-- Search & Filter Controls -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Search lessons by title or content..."
                            class="w-full pl-9 pr-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-xs">
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="openLessonImportModal"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap touch-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Import</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openCreateLessonModal"
                            class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-xs whitespace-nowrap flex-shrink-0 touch-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Lesson</span>
                        </button>
                    </div>
                </div>

                <div>
                    <select 
                        wire:model.live="selectedCategoryFilter"
                        class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 shadow-xs font-medium focus:outline-none focus:border-purple-600">
                        <option value="">All Categories &amp; Formation Tracks</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Lessons Stream -->
            <div class="space-y-3">
                @forelse($lessons as $lesson)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 shadow-xs">
                        <!-- Top Metadata Row -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="px-2.5 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[11px] font-bold truncate max-w-[200px]">
                                    {{ $lesson->category?->name ?? 'Formation Track' }}
                                </span>
                                <span class="text-[11px] text-slate-400 whitespace-nowrap flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                    {{ $lesson->estimated_read_minutes ?? 5 }} min
                                </span>
                            </div>

                            <button 
                                type="button" 
                                wire:click="toggleLessonStatus('{{ $lesson->id }}')"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all whitespace-nowrap flex-shrink-0 {{ $lesson->status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100' }}"
                                title="Click to toggle Published / Draft status">
                                {{ $lesson->status ?? 'published' }}
                            </button>
                        </div>

                        <!-- Main Title & Summary -->
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                                {{ $lesson->title }}
                            </h3>
                            @if($lesson->subheading)
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $lesson->subheading }}
                                </p>
                            @endif
                        </div>

                        <!-- Citations -->
                        @if($lesson->scripture_citations || $lesson->catechism_citations)
                            <div class="py-2 px-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl text-[11px] text-slate-500 space-y-0.5 border border-slate-100 dark:border-slate-800/80">
                                @if($lesson->scripture_citations)
                                    <div><span class="font-semibold text-slate-700 dark:text-slate-300">Scripture:</span> {{ $lesson->scripture_citations }}</div>
                                @endif
                                @if($lesson->catechism_citations)
                                    <div><span class="font-semibold text-slate-700 dark:text-slate-300">Catechism:</span> {{ $lesson->catechism_citations }}</div>
                                @endif
                            </div>
                        @endif

                        <!-- Card Action Footer -->
                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                            <a 
                                href="{{ route('lesson.show', $lesson->id) }}" 
                                target="_blank"
                                class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-xs font-semibold rounded-xl flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Preview</span>
                            </a>
                            <button 
                                type="button" 
                                wire:click="editLesson('{{ $lesson->id }}')"
                                class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                                Edit
                            </button>
                            <button 
                                type="button" 
                                wire:confirm="Delete lesson '{{ $lesson->title }}'?"
                                wire:click="deleteLesson('{{ $lesson->id }}')"
                                class="px-3 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                        No study lessons found. Click <strong>+ Add Lesson</strong> above to create your first diocesan micro-lesson!
                    </div>
                @endforelse

                <div class="pt-2">
                    {{ $lessons->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 6: QUESTIONS BANK CRUD & IMPORT (Q&A) -->
    @if($activeTab === 'questions')
        <div class="space-y-3 animate-fade-in">
            <!-- Search & Actions Toolbar -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Search question text or scripture..."
                            class="w-full pl-9 pr-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-xs">
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="openImportModal"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap touch-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Import</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="openCreateQuestionModal"
                            class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap touch-press">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Q&amp;A</span>
                        </button>
                    </div>
                </div>

                <div>
                    <select 
                        wire:model.live="selectedCategoryFilter"
                        class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 font-medium shadow-xs focus:outline-none focus:border-purple-600">
                        <option value="">All Categories &amp; Formation Tracks</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($questions as $q)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold truncate max-w-[200px]">
                                        {{ $q->category?->name ?? 'Universal' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold whitespace-nowrap">
                                        Diff: {{ $q->difficulty ?? 1 }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-slate-900 dark:text-white leading-snug">{{ $q->question_text }}</h3>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button 
                                    type="button" 
                                    wire:click="editQuestion({{ $q->id }})"
                                    class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg">
                                    Edit
                                </button>
                                <button 
                                    type="button" 
                                    wire:confirm="Delete this question?"
                                    wire:click="deleteQuestion({{ $q->id }})"
                                    class="px-2.5 py-1 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-lg">
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Options Preview Grid (Neutral - No Answer Highlight) -->
                        <div class="grid grid-cols-2 gap-1.5 text-xs">
                            @if(is_array($q->options))
                                @foreach($q->options as $optKey => $optText)
                                    <div class="p-2 rounded-lg border bg-slate-50 dark:bg-slate-900/60 border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-normal">
                                        <span class="font-mono font-bold text-[10px] text-slate-500 mr-0.5">{{ $optKey }}:</span> {{ $optText }}
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        @if($q->reference_citation)
                            <p class="text-[11px] text-slate-400 italic">Ref: {{ $q->reference_citation }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">No questions found. Use the Import button to upload your CSV, Excel, or JSON questions!</div>
                @endforelse

                <div class="pt-2">
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 6: COMPETITIONS & RALLIES CRUD -->
    @if($activeTab === 'competitions')
        <div class="space-y-3 animate-fade-in">
            <!-- Search & Schedule Toolbar -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" 
                        placeholder="Search rallies..."
                        class="w-full pl-9 pr-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 shadow-xs">
                </div>

                <button 
                    type="button" 
                    wire:click="openCreateCompetitionModal"
                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap flex-shrink-0 touch-press">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Schedule Rally</span>
                </button>
            </div>

            <div class="space-y-3">
                @forelse($competitions as $comp)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $comp->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                        {{ $comp->status }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-mono text-[10px] font-bold">
                                        PIN: {{ $comp->rally_pin }}
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $comp->title }}</h3>
                                @if($comp->description)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $comp->description }}</p>
                                @endif
                            </div>

                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400 whitespace-nowrap flex-shrink-0">
                                {{ $comp->question_count }} Qs &bull; {{ $comp->time_limit_seconds }}s
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                            <span class="text-slate-400 text-[11px]">
                                {{ $comp->start_time?->format('M d, Y') }} &rarr; {{ $comp->end_time?->format('M d, Y') }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                <button 
                                    type="button" 
                                    wire:click="toggleCompetitionStatus('{{ $comp->id }}')"
                                    class="px-2.5 py-1 rounded-xl text-xs font-semibold {{ $comp->status === 'active' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                    {{ $comp->status === 'active' ? 'Conclude' : 'Activate' }}
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="editCompetition('{{ $comp->id }}')"
                                    class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl">
                                    Edit
                                </button>
                                <button 
                                    type="button" 
                                    wire:confirm="Delete this competition?"
                                    wire:click="deleteCompetition('{{ $comp->id }}')"
                                    class="px-2.5 py-1 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">No active or scheduled rallies found.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 7: PARISH ADMINS -->
    @if($activeTab === 'admins')
        <div class="space-y-3 animate-fade-in">
            <div class="flex items-center justify-between">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search chairperson by name or email..."
                    class="w-2/3 px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600">

                <button 
                    type="button" 
                    wire:click="$set('showAdminModal', true)"
                    class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm">
                    <span>+ Add Chairperson</span>
                </button>
            </div>

            <div class="space-y-2">
                @forelse($admins as $admin)
                    <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="space-y-0.5">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white">{{ $admin->name }}</h3>
                            <p class="text-[11px] text-slate-500">
                                Parish: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $admin->parish?->name ?? 'Unassigned' }}</span> &bull; {{ $admin->phone }} &bull; {{ $admin->email }}
                            </p>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-[10px] font-bold uppercase">
                            {{ $admin->role }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">No administrators found.</div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 8: YOUTH & TRANSFERS -->
    @if($activeTab === 'youth')
        <div class="space-y-4 animate-fade-in">
            <!-- Pending Transfers Section -->
            @if($pendingTransfers->isNotEmpty())
                <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-xl space-y-3 shadow-sm">
                    <h3 class="text-xs font-bold text-amber-900 dark:text-amber-200 uppercase tracking-wider">
                        Pending Parish Transfers ({{ $pendingTransfers->count() }})
                    </h3>
                    <div class="space-y-2">
                        @foreach($pendingTransfers as $pt)
                            <div class="p-3 bg-white dark:bg-[#121826] rounded-lg border border-amber-200 dark:border-amber-900/50 flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $pt->user?->name }}</h4>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $pt->fromParish?->name }} &rarr; <span class="font-bold text-purple-600 dark:text-purple-400">{{ $pt->toParish?->name }}</span>
                                    </p>
                                    @if($pt->reason)
                                        <p class="text-[10px] text-slate-400 italic">"{{ $pt->reason }}"</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button wire:click="approveTransfer('{{ $pt->id }}')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm">Approve</button>
                                    <button wire:click="rejectTransfer('{{ $pt->id }}')" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold rounded-lg">Reject</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Youth Directory -->
            <div class="space-y-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search youth by name or phone..."
                    class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600">

                <div class="space-y-1.5">
                    @foreach($youths as $y)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between shadow-sm">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $y->name }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $y->parish?->name }} &bull; {{ $y->phone }} &bull; Level {{ $y->level }} ({{ number_format($y->xp) }} XP)</p>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold rounded">
                                Active
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="pt-2">{{ $youths->links() }}</div>
            </div>
        </div>
    @endif

    <!-- TAB 9: AUDIT TRAIL -->
    @if($activeTab === 'reports')
        <div class="space-y-2 animate-fade-in">
            @forelse($auditLogs as $log)
                <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs shadow-sm">
                    <div>
                        <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $log->action }}</span>
                        <p class="text-[11px] text-slate-500">By: {{ $log->user?->name ?? 'System' }} &bull; {{ $log->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-xs">No audit logs recorded yet.</div>
            @endforelse
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. MODALS (DEANERY, PARISH, TRACK, QUESTION, RALLY, IMPORT)               -->
    <!-- ========================================================================= -->

    <!-- A. DEANERY MODAL -->
    @if($showDeaneryModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $editDeaneryId ? 'Edit Deanery' : 'Create Deanery' }}
                    </h3>
                    <button wire:click="$set('showDeaneryModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Deanery Code</label>
                        <input type="text" wire:model="deaneryCode" placeholder="e.g. LIV-URB" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        @error('deaneryCode') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Deanery Name</label>
                        <input type="text" wire:model="deaneryName" placeholder="e.g. Livingstone Urban Deanery" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        @error('deaneryName') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Headquarters (Optional)</label>
                        <input type="text" wire:model="deaneryHeadquarters" placeholder="e.g. St. Theresa Cathedral" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showDeaneryModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveDeanery" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Deanery</button>
                </div>
            </div>
        </div>
    @endif

    <!-- B. PARISH MODAL -->
    @if($showParishModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $editParishId ? 'Edit Parish' : 'Register New Parish' }}
                    </h3>
                    <button wire:click="$set('showParishModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Assigned Deanery</label>
                        <select wire:model="newParishDeaneryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            <option value="">Select Deanery</option>
                            @foreach($deaneries as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                        @error('newParishDeaneryId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Parish Code</label>
                            <input type="text" wire:model="newParishCode" placeholder="e.g. ST-THERESA" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('newParishCode') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Parish Name</label>
                            <input type="text" wire:model="newParishName" placeholder="e.g. St. Theresa Cathedral" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('newParishName') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Location / Town</label>
                        <input type="text" wire:model="newParishLocation" placeholder="e.g. Livingstone Central" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Contact Email</label>
                            <input type="email" wire:model="newParishEmail" placeholder="parish@diocese.org" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Contact Phone</label>
                            <input type="text" wire:model="newParishPhone" placeholder="+260..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showParishModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveParish" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Parish</button>
                </div>
            </div>
        </div>
    @endif

    <!-- C. TRACK MODAL -->
    @if($showTrackModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $editTrackId ? 'Edit Track' : 'Create Formation Track' }}
                    </h3>
                    <button wire:click="$set('showTrackModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Track Name</label>
                        <input type="text" wire:model="trackName" placeholder="e.g. Sacraments & Grace" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        @error('trackName') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Description</label>
                        <textarea wire:model="trackDescription" rows="2" placeholder="Brief overview of the track..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Display Order</label>
                            <input type="number" wire:model="trackDisplayOrder" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Icon Name</label>
                            <input type="text" wire:model="trackIcon" placeholder="e.g. book-open" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showTrackModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveTrack" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Track</button>
                </div>
            </div>
        </div>
    @endif

    <!-- D. QUESTION MODAL -->
    @if($showQuestionModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $editQuestionId ? 'Edit Question' : 'Add Question to Universal Bank' }}
                    </h3>
                    <button wire:click="$set('showQuestionModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Category / Track</label>
                            <select wire:model="newQuestionCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('newQuestionCategoryId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Difficulty Level</label>
                            <select wire:model="newQuestionLevel" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="1">Level 1 - Easy</option>
                                <option value="2">Level 2 - Medium</option>
                                <option value="3">Level 3 - Hard</option>
                                <option value="4">Level 4 - Expert</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Question Prompt</label>
                        <textarea wire:model="newQuestionText" rows="2" placeholder="e.g. Which Gospel contains the Beatitudes?" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                        @error('newQuestionText') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- 4 Multiple Choice Options -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option A</label>
                            <input type="text" wire:model="optionA" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('optionA') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option B</label>
                            <input type="text" wire:model="optionB" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('optionB') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option C</label>
                            <input type="text" wire:model="optionC" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('optionC') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option D</label>
                            <input type="text" wire:model="optionD" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('optionD') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Correct Answer</label>
                            <select wire:model="correctOption" class="w-full px-3 py-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-700 rounded-xl font-bold text-emerald-800 dark:text-emerald-200">
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Scripture / CCC Citation</label>
                            <input type="text" wire:model="newQuestionCitation" placeholder="e.g. CCC 1213, Matthew 28:19" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Doctrinal Explanation</label>
                        <textarea wire:model="newQuestionExplanation" rows="2" placeholder="Why is this answer correct according to Catholic doctrine?" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                        @error('newQuestionExplanation') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showQuestionModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveQuestion" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Q&amp;A</button>
                </div>
            </div>
        </div>
    @endif

    <!-- E. STUDY LESSON CREATE & EDIT MODAL -->
    @if($showLessonModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $editLessonId ? 'Edit Study Lesson' : 'Create Study Lesson' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showLessonModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs max-h-[70vh] overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Formation Category / Track *</label>
                            <select wire:model="lessonCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('lessonCategoryId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Status</label>
                            <select wire:model="lessonStatus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Lesson Title *</label>
                        <input type="text" wire:model="lessonTitle" placeholder="e.g. The Mystery of the Holy Trinity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        @error('lessonTitle') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Subtitle / Hook Summary</label>
                        <input type="text" wire:model="lessonSubheading" placeholder="e.g. Understanding God as Father, Son, and Holy Spirit" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Lesson Content Body *</label>
                        <textarea wire:model="lessonContent" rows="6" placeholder="Write the main lesson teachings, reflections, and insights..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl font-sans"></textarea>
                        @error('lessonContent') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Key Takeaways (one bullet point per line)</label>
                        <textarea wire:model="lessonTakeaways" rows="3" placeholder="God is one in essence and three in persons&#10;The Trinity is a mystery of communion&#10;We are invited into the divine life" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl font-mono text-[11px]"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Scripture Citation</label>
                            <input type="text" wire:model="lessonScripture" placeholder="e.g. Matthew 28:19, 2 Cor 13:14" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Catechism (CCC) Citation</label>
                            <input type="text" wire:model="lessonCatechism" placeholder="e.g. CCC 232-260" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Estimated Reading Time (Minutes)</label>
                            <input type="number" wire:model="lessonReadMinutes" min="1" max="60" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Difficulty Level</label>
                            <select wire:model="lessonDifficulty" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="1">Level 1 - Beginner</option>
                                <option value="2">Level 2 - Intermediate</option>
                                <option value="3">Level 3 - Advanced</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showLessonModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveLesson" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Lesson</button>
                </div>
            </div>
        </div>
    @endif

    <!-- F. COMPETITION / RALLY MODAL -->
    @if($showCompetitionModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-4 shadow-xl my-8">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $editCompetitionId ? 'Edit Rally / Competition' : 'Schedule Diocesan Rally' }}
                    </h3>
                    <button wire:click="$set('showCompetitionModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally Title</label>
                        <input type="text" wire:model="newCompTitle" placeholder="e.g. Diocesan Youth Bible Rally 2026" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        @error('newCompTitle') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Description</label>
                        <textarea wire:model="newCompDescription" rows="2" placeholder="Overview and formation theme..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                        @error('newCompDescription') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally Type</label>
                            <select wire:model="newCompType" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="diocesan">Diocesan Championship</option>
                                <option value="deanery">Deanery Championship</option>
                                <option value="parish">Parish Competition</option>
                                <option value="youth_rally">Youth Rally Special</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Category</label>
                            <select wire:model="newCompCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">All Categories (Mixed)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Start Date &amp; Time</label>
                            <input type="datetime-local" wire:model="newCompStartTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">End Date &amp; Time</label>
                            <input type="datetime-local" wire:model="newCompEndTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Time Limit (Seconds)</label>
                            <input type="number" wire:model="newCompTimeLimit" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Question Count</label>
                            <input type="number" wire:model="newCompQuestionCount" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showCompetitionModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="saveCompetition" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Schedule Rally</button>
                </div>
            </div>
        </div>
    @endif

    <!-- F. DYNAMIC FILE IMPORT MODAL (CSV, XLSX, JSON) -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Import Questions &amp; Content</h3>
                            <p class="text-[11px] text-slate-500">Upload your custom CSV, Excel (.xlsx), or JSON file</p>
                        </div>
                    </div>
                    <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <!-- Template Download Section -->
                <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-xl flex items-center justify-between text-xs">
                    <span class="text-purple-900 dark:text-purple-200 font-medium">Need sample formatting?</span>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="downloadSampleTemplate('csv')"
                            class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-sm">
                            Download CSV
                        </button>
                        <button 
                            type="button" 
                            wire:click="downloadSampleTemplate('json')"
                            class="px-2.5 py-1 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg font-bold text-[11px] flex items-center gap-1">
                            Download JSON
                        </button>
                    </div>
                </div>

                <!-- File Upload Input Area -->
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Select File (CSV, XLSX, JSON)</label>
                        <input 
                            type="file" 
                            wire:model="importFile"
                            accept=".csv, .xlsx, .xls, .json, text/csv, application/json, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                        @error('importFile') <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Fallback Category / Track</label>
                            <select wire:model="importTrackId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">Auto-Detect from File</option>
                                @foreach($tracks as $tr)
                                    <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Duplicate Handling</label>
                            <select wire:model="importDuplicateStrategy" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="skip">Skip duplicates (Safe)</option>
                                <option value="overwrite">Overwrite existing questions</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Import Results Display -->
                @if($importResults)
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <h4 class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Import Summary</span>
                        </h4>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="p-2 bg-white dark:bg-[#121826] rounded-lg border">
                                <span class="text-[10px] text-slate-400 block">Total</span>
                                <span class="font-bold text-sm">{{ $importResults['total_processed'] }}</span>
                            </div>
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg border border-emerald-200 text-emerald-800 dark:text-emerald-200">
                                <span class="text-[10px] block">Imported</span>
                                <span class="font-bold text-sm">{{ $importResults['successful'] }}</span>
                            </div>
                            <div class="p-2 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 text-amber-800 dark:text-amber-200">
                                <span class="text-[10px] block">Duplicates</span>
                                <span class="font-bold text-sm">{{ $importResults['duplicates_skipped'] }}</span>
                            </div>
                            <div class="p-2 bg-red-50 dark:bg-red-950/40 rounded-lg border border-red-200 text-red-800 dark:text-red-200">
                                <span class="text-[10px] block">Failed</span>
                                <span class="font-bold text-sm">{{ $importResults['failed'] }}</span>
                            </div>
                        </div>

                        @if(!empty($importResults['errors']))
                            <div class="p-2 bg-red-50 dark:bg-red-950/20 rounded-lg border border-red-200 text-red-700 dark:text-red-300 text-[10px] space-y-0.5">
                                <span class="font-bold block">Validation Errors:</span>
                                @foreach($importResults['errors'] as $err)
                                    <div>&bull; {{ $err }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showImportModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Close</button>
                    <button 
                        type="button" 
                        wire:click="processDynamicImport" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                        <span wire:loading.remove>Process Import</span>
                        <span wire:loading class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Importing...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- F2. LESSON IMPORT MODAL -->
    @if($showLessonImportModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Import Lessons &amp; Micro Lessons</h3>
                            <p class="text-[11px] text-slate-500">Upload CSV, Excel (.xlsx), or JSON lesson files</p>
                        </div>
                    </div>
                    <button wire:click="$set('showLessonImportModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <!-- Template Download Card -->
                <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-xl flex items-center justify-between text-xs">
                    <span class="text-purple-900 dark:text-purple-200 font-medium">Download sample template:</span>
                    <div class="flex items-center gap-1.5">
                        <button 
                            type="button" 
                            wire:click="downloadSampleLessonTemplate('csv')"
                            class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-sm">
                            CSV
                        </button>
                        <button 
                            type="button" 
                            wire:click="downloadSampleLessonTemplate('json')"
                            class="px-2.5 py-1 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg font-bold text-[11px] flex items-center gap-1">
                            JSON
                        </button>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Select File (CSV, XLSX, JSON)</label>
                        <input 
                            type="file" 
                            wire:model="lessonImportFile"
                            accept=".csv, .xlsx, .xls, .json, text/csv, application/json, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                        @error('lessonImportFile') <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Fallback Category / Track</label>
                            <select wire:model="lessonImportCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">Auto-Detect from File</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Duplicate Strategy</label>
                            <select wire:model="lessonImportDuplicateStrategy" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="skip">Skip duplicates</option>
                                <option value="overwrite">Overwrite existing</option>
                            </select>
                        </div>
                    </div>
                </div>

                @if($lessonImportResults)
                    <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg border border-emerald-200 text-emerald-800 dark:text-emerald-200">
                                <span class="text-[10px] block">Imported</span>
                                <span class="font-bold text-sm">{{ $lessonImportResults['successful'] }}</span>
                            </div>
                            <div class="p-2 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 text-amber-800 dark:text-amber-200">
                                <span class="text-[10px] block">Duplicates</span>
                                <span class="font-bold text-sm">{{ $lessonImportResults['duplicates_skipped'] }}</span>
                            </div>
                            <div class="p-2 bg-red-50 dark:bg-red-950/40 rounded-lg border border-red-200 text-red-800 dark:text-red-200">
                                <span class="text-[10px] block">Failed</span>
                                <span class="font-bold text-sm">{{ $lessonImportResults['failed'] }}</span>
                            </div>
                        </div>

                        @if(!empty($lessonImportResults['errors']))
                            <div class="p-2 bg-red-50 dark:bg-red-950/20 rounded-lg border border-red-200 text-red-700 dark:text-red-300 text-[10px] space-y-0.5">
                                <span class="font-bold block">Validation Errors:</span>
                                @foreach($lessonImportResults['errors'] as $err)
                                    <div>&bull; {{ $err }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showLessonImportModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Close</button>
                    <button 
                        type="button" 
                        wire:click="processLessonImport" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                        <span wire:loading.remove>Process Lesson Import</span>
                        <span wire:loading class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Importing...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- G. PARISH ADMIN MODAL -->
    @if($showAdminModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Add Parish Chairperson</h3>
                    <button wire:click="$set('showAdminModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Parish</label>
                        <select wire:model="newAdminParishId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            <option value="">Select Parish</option>
                            @foreach($parishes as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Full Name</label>
                        <input type="text" wire:model="newAdminName" placeholder="e.g. John Banda" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Phone Number</label>
                        <input type="text" wire:model="newAdminPhone" placeholder="+260..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Email</label>
                        <input type="email" wire:model="newAdminEmail" placeholder="chairperson@parish.org" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Default Password</label>
                        <input type="text" wire:model="newAdminPassword" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t">
                    <button wire:click="$set('showAdminModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                    <button wire:click="createParishAdmin" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Create Chairperson</button>
                </div>
            </div>
        </div>
    @endif

    <!-- H. EXECUTIVE REPORT MODAL -->
    @if($showReportModal && $reportSummary)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Diocesan Executive Report</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl">
                        <span class="font-bold block text-purple-900 dark:text-purple-200">Diocese Overview:</span>
                        <p class="text-slate-600 dark:text-slate-300 mt-1">
                            Total Youth: {{ $reportSummary['kpis']['total_youth'] }} &bull; 
                            Average Mastery: {{ $reportSummary['kpis']['average_mastery'] }}% &bull; 
                            Total XP: {{ number_format($reportSummary['kpis']['total_xp']) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-end pt-2 border-t">
                    <button wire:click="$set('showReportModal', false)" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Done</button>
                </div>
            </div>
        </div>
    @endif

</div>
