<div class="space-y-5 pb-20">

    <!-- LEADERBOARD HEADER -->
    <div class="pt-1">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Formation Leaderboard</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Livingstone Diocesan Youth Ranks</p>
    </div>

    <!-- 1. TIMEFRAME SELECTOR -->
    <div class="grid grid-cols-4 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
        <button 
            type="button"
            wire:click="setTimeframe('today')"
            class="py-1.5 rounded-lg transition-colors {{ $timeframe === 'today' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            Today
        </button>
        <button 
            type="button"
            wire:click="setTimeframe('this_week')"
            class="py-1.5 rounded-lg transition-colors {{ $timeframe === 'this_week' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            Week
        </button>
        <button 
            type="button"
            wire:click="setTimeframe('this_month')"
            class="py-1.5 rounded-lg transition-colors {{ $timeframe === 'this_month' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            Month
        </button>
        <button 
            type="button"
            wire:click="setTimeframe('all_time')"
            class="py-1.5 rounded-lg transition-colors {{ $timeframe === 'all_time' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            All-Time
        </button>
    </div>

    <!-- 2. HIERARCHICAL SCOPE SELECTOR -->
    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
        <button 
            type="button"
            wire:click="setScope('parish')"
            class="py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Parish</span>
        </button>
        <button 
            type="button"
            wire:click="setScope('deanery')"
            class="py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
            </svg>
            <span>Deanery</span>
        </button>
        <button 
            type="button"
            wire:click="setScope('diocese')"
            class="py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $scope === 'diocese' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
            <span>Diocese</span>
        </button>
    </div>

    <!-- 3. TOP 3 PODIUM (FLAT M3 DESIGN) -->
    @if($top3->isNotEmpty())
        <div class="pt-2 pb-1">
            <div class="grid grid-cols-3 gap-2 items-end">
                <!-- 2ND PLACE (SILVER) -->
                @if(isset($top3[1]))
                    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-center flex flex-col items-center justify-end h-36">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-xs mb-1 border border-slate-200 dark:border-slate-700">
                            2
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-full leading-tight">{{ explode(' ', $top3[1]->user_name)[0] }}</h4>
                        <span class="text-[10px] text-slate-400 truncate max-w-full block">{{ $top3[1]->parish_name ?? 'Parish' }}</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">{{ number_format($top3[1]->total_points) }} pts</span>
                    </div>
                @else
                    <div class="h-36"></div>
                @endif

                <!-- 1ST PLACE (GOLD) -->
                @if(isset($top3[0]))
                    <div class="bg-white dark:bg-[#121826] border-2 border-amber-500/50 rounded-xl p-3 text-center flex flex-col items-center justify-end h-44">
                        <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-bold flex items-center justify-center text-sm mb-1 border border-amber-300 dark:border-amber-700">
                            1
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-full leading-tight">{{ explode(' ', $top3[0]->user_name)[0] }}</h4>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 truncate max-w-full block">{{ $top3[0]->parish_name ?? 'Parish' }}</span>
                        <span class="text-xs font-bold text-purple-700 dark:text-purple-400 mt-1">{{ number_format($top3[0]->total_points) }} pts</span>
                    </div>
                @endif

                <!-- 3RD PLACE (BRONZE) -->
                @if(isset($top3[2]))
                    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-center flex flex-col items-center justify-end h-32">
                        <div class="w-7 h-7 rounded-full bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-400 font-bold flex items-center justify-center text-xs mb-1 border border-amber-200 dark:border-amber-800">
                            3
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-full leading-tight">{{ explode(' ', $top3[2]->user_name)[0] }}</h4>
                        <span class="text-[10px] text-slate-400 truncate max-w-full block">{{ $top3[2]->parish_name ?? 'Parish' }}</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">{{ number_format($top3[2]->total_points) }} pts</span>
                    </div>
                @else
                    <div class="h-32"></div>
                @endif
            </div>
        </div>
    @endif

    <!-- 4. REMAINING RANKS (4–50) -->
    <div class="space-y-1.5">
        @forelse($remaining as $index => $item)
            @php $rank = $index + 4; @endphp
            <div class="p-3 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-6 text-center text-xs font-bold text-slate-400">#{{ $rank }}</span>
                    <div>
                        <h4 class="text-xs font-semibold text-slate-900 dark:text-white leading-tight">{{ $item->user_name }}</h4>
                        <span class="text-[11px] text-slate-400 block">{{ $item->parish_name ?? 'Livingstone Diocese' }}</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 block">{{ number_format($item->total_points) }} pts</span>
                    <span class="text-[10px] text-slate-400">{{ $item->attempts_count }} quizzes</span>
                </div>
            </div>
        @empty
            @if($top3->isEmpty())
                <div class="p-8 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-2">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">No Ranked Quiz Records Yet</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Complete a ranked quiz to be the first on the Diocesan leaderboard.</p>
                </div>
            @endif
        @endforelse
    </div>

    <!-- 5. STICKY USER STANDING -->
    @if($currentUser)
        <div class="fixed bottom-16 inset-x-0 max-w-md mx-auto px-4 z-40">
            <div class="p-3 bg-white/95 dark:bg-[#121826]/95 backdrop-blur-sm border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-xs border border-purple-200 dark:border-purple-800">
                        #{{ $userRank ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-tight block">Your Standing</span>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">
                            @if($pointsBehind && $aheadPlayerName)
                                <span class="text-purple-600 dark:text-purple-400">{{ $pointsBehind }} pts</span> behind {{ explode(' ', $aheadPlayerName)[0] }}
                            @elseif($userRank === 1)
                                Ranked #1 Champion
                            @else
                                {{ number_format($userPoints) }} Total Points
                            @endif
                        </h4>
                    </div>
                </div>

                <a href="/quiz?tab=compete" class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs transition-colors touch-press">
                    Compete
                </a>
            </div>
        </div>
    @endif
</div>
