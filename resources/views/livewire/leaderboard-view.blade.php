<div class="space-y-6 pb-6">

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN DIOCESAN MULTI-TIER STANDINGS                         -->
    <!-- ========================================================================= -->
    @if($currentUser->isSuperAdmin())
        <div class="space-y-5">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Diocesan Territory Standings
                </span>
                <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">Diocesan Leaderboard</h2>
                <p class="text-xs text-slate-500">Livingstone Diocese &bull; Deanery, Parish &amp; Youth Formation Standings</p>
            </div>

            <!-- SCOPE SWITCHER (Deaneries / Parishes / Youth) -->
            <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
                <button 
                    type="button" 
                    wire:click="setScope('deanery')" 
                    class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Deaneries
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('parish')" 
                    class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Parishes
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('youth')" 
                    class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'youth' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Top Youth
                </button>
            </div>

            <!-- TAB 1: DEANERIES STANDINGS -->
            @if($scope === 'deanery')
                <div class="space-y-2.5">
                    @foreach($deaneryStandings as $index => $d)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $d['name'] }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $d['parishes_count'] }} Parishes &bull; {{ $d['youth_count'] }} Youth</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-purple-600 dark:text-purple-400 text-sm block">{{ number_format($d['total_xp']) }} XP</span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ $d['avg_accuracy'] }}% Accuracy</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- TAB 2: PARISHES STANDINGS -->
            @if($scope === 'parish')
                <div class="space-y-2.5">
                    @foreach($parishStandings as $index => $p)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $p['name'] }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $p['deanery_name'] }} &bull; {{ $p['youth_count'] }} Youth</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-purple-600 dark:text-purple-400 text-sm block">{{ number_format($p['total_xp']) }} XP</span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ $p['avg_accuracy'] }}% Score</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- TAB 3: DIOCESAN TOP YOUTH -->
            @if($scope === 'youth' && $youthStandings)
                <div class="space-y-2.5">
                    @foreach($youthStandings['top3']->concat($youthStandings['remaining']) as $index => $y)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $y->user_name }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $y->parish_name }} &bull; Level {{ $y->user_level }}</span>
                                </div>
                            </div>
                            <span class="font-black text-purple-600 dark:text-purple-400 text-sm">{{ number_format($y->total_points) }} XP</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) PARISH & DEANERY STANDINGS             -->
    <!-- ========================================================================= -->
    @elseif($currentUser->isChairperson())
        <div class="space-y-5">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Parish Standings
                </span>
                <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">{{ $parish->name }}</h2>
                <p class="text-xs text-slate-500">Youth Roster Rankings &bull; {{ $parish->deanery?->name }}</p>
            </div>

            <!-- SCOPE SWITCHER -->
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
                <button 
                    type="button" 
                    wire:click="setScope('parish')" 
                    class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    My Parish Youth
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('deanery')" 
                    class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Deanery Parishes
                </button>
            </div>

            <!-- PARISH YOUTHS LIST -->
            @if($scope === 'parish' && $parishYouthRankings)
                <div class="space-y-2.5">
                    @forelse($parishYouthRankings['top3']->concat($parishYouthRankings['remaining']) as $index => $y)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $y->user_name }}</h4>
                                    <span class="text-[11px] text-slate-500">Level {{ $y->user_level }} &bull; {{ $y->attempts_count }} Quizzes</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-purple-600 dark:text-purple-400 text-sm block">{{ number_format($y->total_points) }} XP</span>
                                <span class="text-[10px] text-emerald-600 font-bold">{{ (int)$y->avg_accuracy }}% Accuracy</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-8 text-center bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                            No ranked youth quiz activity recorded in your parish yet.
                        </p>
                    @endforelse
                </div>
            @endif

            <!-- DEANERY PARISHES STANDINGS -->
            @if($scope === 'deanery')
                <div class="space-y-2.5">
                    @foreach($deaneryParishStandings as $index => $p)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm {{ $p['id'] === $parish->id ? 'ring-2 ring-purple-600' : '' }}">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ $p['name'] }}
                                        @if($p['id'] === $parish->id)<span class="text-purple-600 text-xs font-bold">(Your Parish)</span>@endif
                                    </h4>
                                    <span class="text-[11px] text-slate-500">{{ $p['youth_count'] }} Youth Members</span>
                                </div>
                            </div>
                            <span class="font-black text-purple-600 dark:text-purple-400 text-sm">{{ number_format($p['total_xp']) }} XP</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER LEADERBOARD (RICH MINIMALISM)                       -->
    <!-- ========================================================================= -->
    @else
        <!-- 1. LEADERBOARD HEADER -->
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Diocesan Ranks
                </span>
            </div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">
                Formation Standings
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Livingstone Diocese Catholic Youth League
            </p>
        </div>

        <!-- 2. HIERARCHICAL SCOPE SELECTOR -->
        <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
            <button type="button" wire:click="setScope('parish')" class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">Parish</button>
            <button type="button" wire:click="setScope('deanery')" class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">Deanery</button>
            <button type="button" wire:click="setScope('diocese')" class="py-2.5 rounded-xl transition-all touch-press {{ $scope === 'diocese' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">Diocese</button>
        </div>

        <!-- 3. USER PROXIMITY CARD -->
        @if($userRank)
            <div class="bg-gradient-to-r from-purple-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-4 flex items-center justify-between border border-purple-800/40 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center font-black text-base border border-white/20">#{{ $userRank }}</span>
                    <div>
                        <h4 class="font-bold text-xs">Your Current Rank</h4>
                        <p class="text-[11px] text-purple-200">{{ number_format($userPoints) }} XP Earned</p>
                    </div>
                </div>
                @if($pointsBehind && $aheadPlayerName)
                    <div class="text-right text-[11px]">
                        <span class="text-purple-300 block">Behind {{ explode(' ', $aheadPlayerName)[0] }}:</span>
                        <span class="font-black text-amber-300 block">+{{ number_format($pointsBehind) }} XP</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- 4. TOP 3 PODIUM & FULL LEADERBOARD LIST -->
        <div class="space-y-2.5">
            @foreach($top3->concat($remaining) as $index => $entry)
                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs shadow-sm {{ $entry->user_id === $currentUser->id ? 'ring-2 ring-purple-600 bg-purple-50/20' : '' }}">
                    <div class="flex items-center gap-3.5">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950 shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-900' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300')) }}">
                            #{{ $index + 1 }}
                        </span>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $entry->user_name }}</h4>
                                @if($entry->user_id === $currentUser->id)
                                    <span class="px-1.5 py-0.2 bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 rounded text-[9px] font-bold">You</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-500">{{ $entry->parish_name }}</span>
                        </div>
                    </div>
                    <span class="font-black text-purple-600 dark:text-purple-400 text-sm">{{ number_format($entry->total_points) }} XP</span>
                </div>
            @endforeach
        </div>
    @endif

</div>
