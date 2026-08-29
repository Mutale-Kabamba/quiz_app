<div class="space-y-4 pb-12">

    <!-- LEADERBOARD TITLE & REPUTATION HEADER -->
    <div class="text-center pt-1">
        <h2 class="text-xl font-black font-display text-white">Hierarchical Leaderboard</h2>
        <p class="text-[11px] text-slate-400">Diocese of Livingstone Catholic Youth Rankings</p>
    </div>

    <!-- 3-SEGMENTED HIERARCHICAL SCOPE SWITCHER -->
    <div class="p-1 rounded-2xl bg-slate-900 border border-slate-800 flex items-center shadow-md">
        <button 
            type="button" 
            wire:click="setScope('parish')"
            class="w-1/3 py-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 {{ $scope === 'parish' ? 'bg-amber-500 text-slate-950 shadow-glow-gold' : 'text-slate-400 hover:text-white' }}">
            <span>⛪</span> Parish
        </button>

        <button 
            type="button" 
            wire:click="setScope('deanery')"
            class="w-1/3 py-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 {{ $scope === 'deanery' ? 'bg-amber-500 text-slate-950 shadow-glow-gold' : 'text-slate-400 hover:text-white' }}">
            <span>🏛️</span> Deanery
        </button>

        <button 
            type="button" 
            wire:click="setScope('diocese')"
            class="w-1/3 py-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 {{ $scope === 'diocese' ? 'bg-amber-500 text-slate-950 shadow-glow-gold' : 'text-slate-400 hover:text-white' }}">
            <span>👑</span> Diocese
        </button>
    </div>

    <!-- CATEGORY FILTER PILLS (HORIZONTAL SCROLL) -->
    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1">
        <button 
            type="button"
            wire:click="setCategory(null)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ is_null($categoryId) ? 'bg-slate-800 text-amber-400 border border-amber-500/40' : 'bg-slate-900/80 text-slate-400 border border-slate-800' }}">
            🌟 All Tracks
        </button>
        @foreach($categories as $cat)
            <button 
                type="button"
                wire:click="setCategory({{ $cat->id }})"
                class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $categoryId === $cat->id ? 'bg-slate-800 text-amber-400 border border-amber-500/40' : 'bg-slate-900/80 text-slate-400 border border-slate-800' }}">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    <!-- VISUAL TOP 3 PODIUM (If rankings exist) -->
    @if($top3->count() > 0)
        <div class="pt-4 pb-2">
            <div class="flex items-end justify-center gap-2 max-w-xs mx-auto">
                <!-- RANK 2: SILVER PODIUM (LEFT) -->
                @if($top3->has(1))
                    <div class="w-1/3 text-center flex flex-col items-center">
                        <div class="relative mb-2">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 border-2 border-slate-400 flex items-center justify-center text-sm font-black text-slate-200 shadow-md">
                                {{ substr($top3[1]->user_name, 0, 1) }}
                            </div>
                            <span class="absolute -bottom-1.5 -right-1.5 w-5 h-5 rounded-full bg-slate-400 text-slate-950 font-black text-[10px] flex items-center justify-center">2</span>
                        </div>
                        <h4 class="text-xs font-bold text-white truncate max-w-[80px]">{{ $top3[1]->user_name }}</h4>
                        <span class="text-[10px] text-amber-400 font-extrabold">{{ number_format($top3[1]->total_points) }} pts</span>
                        <div class="w-full h-16 bg-gradient-to-t from-slate-900 to-slate-800 rounded-t-2xl mt-2 border-t border-slate-700"></div>
                    </div>
                @endif

                <!-- RANK 1: GOLD PODIUM (CENTER) -->
                @if($top3->has(0))
                    <div class="w-1/3 text-center flex flex-col items-center">
                        <span class="text-xl animate-bounce mb-0.5">👑</span>
                        <div class="relative mb-2">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center text-base font-black text-slate-950 shadow-glow-gold border-2 border-amber-300">
                                {{ substr($top3[0]->user_name, 0, 1) }}
                            </div>
                            <span class="absolute -bottom-1.5 -right-1.5 w-6 h-6 rounded-full bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-md">1</span>
                        </div>
                        <h4 class="text-xs font-extrabold text-white truncate max-w-[90px]">{{ $top3[0]->user_name }}</h4>
                        <span class="text-xs text-amber-400 font-black">{{ number_format($top3[0]->total_points) }} pts</span>
                        <div class="w-full h-24 bg-gradient-to-t from-slate-900 to-amber-950/40 rounded-t-2xl mt-2 border-t-2 border-amber-500"></div>
                    </div>
                @endif

                <!-- RANK 3: BRONZE PODIUM (RIGHT) -->
                @if($top3->has(2))
                    <div class="w-1/3 text-center flex flex-col items-center">
                        <div class="relative mb-2">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 border-2 border-amber-700 flex items-center justify-center text-sm font-black text-amber-600 shadow-md">
                                {{ substr($top3[2]->user_name, 0, 1) }}
                            </div>
                            <span class="absolute -bottom-1.5 -right-1.5 w-5 h-5 rounded-full bg-amber-700 text-white font-black text-[10px] flex items-center justify-center">3</span>
                        </div>
                        <h4 class="text-xs font-bold text-white truncate max-w-[80px]">{{ $top3[2]->user_name }}</h4>
                        <span class="text-[10px] text-amber-400 font-extrabold">{{ number_format($top3[2]->total_points) }} pts</span>
                        <div class="w-full h-12 bg-gradient-to-t from-slate-900 to-slate-800 rounded-t-2xl mt-2 border-t border-slate-700"></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- REMAINING RANKS (4–50) LIST -->
        <div class="space-y-2">
            @foreach($remaining as $index => $player)
                <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800/80 flex items-center justify-between shadow-sm {{ $currentUser && $currentUser->id === $player->user_id ? 'border-amber-500/80 bg-amber-500/10' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-6 text-center font-black text-xs text-slate-500">
                            #{{ $index + 4 }}
                        </span>
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-300">
                            {{ substr($player->user_name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white leading-tight">{{ $player->user_name }}</h4>
                            <p class="text-[10px] text-slate-400 leading-tight">{{ $player->parish_name ?? 'Livingstone Diocese' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-amber-400 block">{{ number_format($player->total_points) }}</span>
                        <span class="text-[9px] text-slate-500 font-semibold">{{ $player->attempts_count }} sessions</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- EMPTY LEADERBOARD STATE -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto mb-2 text-xl font-bold">
                🏆
            </div>
            <h3 class="text-sm font-bold text-white">No Ranked Scores Yet</h3>
            <p class="text-xs text-slate-400 mt-1">Be the first approved youth from your parish to complete a ranked quiz!</p>
            <a href="/quiz?mode=ranked" class="mt-4 inline-block px-4 py-2 bg-amber-500 text-slate-950 font-black rounded-xl text-xs shadow-glow-gold">
                Start First Ranked Quiz
            </a>
        </div>
    @endif

    <!-- STICKY BOTTOM USER RANKING BADGE -->
    @if($currentUser)
        <div class="fixed bottom-16 left-0 right-0 max-w-md mx-auto px-3 pointer-events-none z-30">
            <div class="p-3 bg-gradient-to-r from-slate-900 via-slate-900 to-amber-950/80 border border-amber-500/40 rounded-2xl shadow-2xl backdrop-blur-md flex items-center justify-between pointer-events-auto">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center shadow-md">
                        {{ $userRank ? '#' . $userRank : '—' }}
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-tight block">Your Current Standing</span>
                        <h4 class="text-xs font-extrabold text-white truncate max-w-[170px]">{{ $currentUser->name }}</h4>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs font-black text-amber-400 block">{{ number_format($userPoints) }} pts</span>
                    <span class="text-[9px] text-slate-400">{{ $currentUser->parish?->name ?? 'Livingstone' }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
