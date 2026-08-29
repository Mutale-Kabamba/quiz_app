<div class="space-y-5 pb-6">

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN DIOCESAN MULTI-TIER STANDINGS                         -->
    <!-- ========================================================================= -->
    @if($currentUser->isSuperAdmin())
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Diocesan Standings</h2>
                <p class="text-xs text-slate-500">Livingstone Diocese &bull; Territory &amp; Youth Formation Ranks</p>
            </div>

            <!-- SCOPE SWITCHER (Deaneries / Parishes / Youth) -->
            <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                <button 
                    type="button" 
                    wire:click="setScope('deanery')" 
                    class="py-2 rounded-lg transition-colors {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Deaneries
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('parish')" 
                    class="py-2 rounded-lg transition-colors {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Parishes
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('youth')" 
                    class="py-2 rounded-lg transition-colors {{ $scope === 'youth' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Top Youth
                </button>
            </div>

            <!-- TAB 1: DEANERIES STANDINGS -->
            @if($scope === 'deanery')
                <div class="space-y-2">
                    @foreach($deaneryStandings as $index => $d)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $d['name'] }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $d['parishes_count'] }} Parishes &bull; {{ $d['youth_count'] }} Youth Members</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-purple-600 dark:text-purple-400 block">{{ number_format($d['total_xp']) }} XP</span>
                                <span class="text-[10px] text-emerald-600 font-semibold">{{ $d['avg_accuracy'] }}% Accuracy</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- TAB 2: PARISHES STANDINGS -->
            @if($scope === 'parish')
                <div class="space-y-2">
                    @foreach($parishStandings as $index => $p)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $p['name'] }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $p['deanery_name'] }} &bull; {{ $p['youth_count'] }} Youth</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-purple-600 dark:text-purple-400 block">{{ number_format($p['total_xp']) }} XP</span>
                                <span class="text-[10px] text-emerald-600 font-semibold">{{ $p['avg_accuracy'] }}% Avg Score</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- TAB 3: DIOCESAN TOP YOUTH -->
            @if($scope === 'youth' && $youthStandings)
                <div class="space-y-2">
                    @foreach($youthStandings['top3']->concat($youthStandings['remaining']) as $index => $y)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $y->user_name }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $y->parish_name }} &bull; Level {{ $y->user_level }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($y->total_points) }} XP</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) PARISH & DEANERY STANDINGS             -->
    <!-- ========================================================================= -->
    @elseif($currentUser->isChairperson())
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Parish Formation Standings</h2>
                <p class="text-xs text-slate-500">{{ $parish->name }} &bull; {{ $parish->deanery?->name }}</p>
            </div>

            <!-- SCOPE SWITCHER (My Parish Youths vs Deanery Parishes) -->
            <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                <button 
                    type="button" 
                    wire:click="setScope('parish')" 
                    class="py-2 rounded-lg transition-colors {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    My Parish Youths
                </button>
                <button 
                    type="button" 
                    wire:click="setScope('deanery')" 
                    class="py-2 rounded-lg transition-colors {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Deanery Parishes
                </button>
            </div>

            <!-- PARISH YOUTHS -->
            @if($scope === 'parish' && $parishYouthRankings)
                <div class="space-y-2">
                    @forelse($parishYouthRankings['top3']->concat($parishYouthRankings['remaining']) as $index => $y)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $y->user_name }}</h4>
                                    <span class="text-[11px] text-slate-500">Level {{ $y->user_level }} &bull; {{ $y->attempts_count }} Quizzes</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-purple-600 dark:text-purple-400 block">{{ number_format($y->total_points) }} XP</span>
                                <span class="text-[10px] text-emerald-600 font-semibold">{{ (int)$y->avg_accuracy }}% Accuracy</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-6 text-center">No ranked youth quiz activity recorded in your parish yet.</p>
                    @endforelse
                </div>
            @endif

            <!-- DEANERY PARISHES STANDINGS -->
            @if($scope === 'deanery')
                <div class="space-y-2">
                    @foreach($deaneryParishStandings as $index => $p)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs {{ $p['id'] === $parish->id ? 'ring-2 ring-purple-600' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $p['name'] }} @if($p['id'] === $parish->id)<span class="text-purple-600 text-xs font-semibold">(Your Parish)</span>@endif</h4>
                                    <span class="text-[11px] text-slate-500">{{ $p['youth_count'] }} Youth Members</span>
                                </div>
                            </div>
                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($p['total_xp']) }} XP</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER LEADERBOARD                                         -->
    <!-- ========================================================================= -->
    @else
        <!-- HEADER -->
        <div class="pt-1">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Formation Leaderboard</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Livingstone Diocese Catholic Youth Standings</p>
        </div>

        <!-- HIERARCHICAL SCOPE SELECTOR -->
        <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
            <button type="button" wire:click="setScope('parish')" class="py-2 rounded-lg transition-colors {{ $scope === 'parish' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold' : 'text-slate-500 hover:text-slate-900' }}">Parish</button>
            <button type="button" wire:click="setScope('deanery')" class="py-2 rounded-lg transition-colors {{ $scope === 'deanery' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold' : 'text-slate-500 hover:text-slate-900' }}">Deanery</button>
            <button type="button" wire:click="setScope('diocese')" class="py-2 rounded-lg transition-colors {{ $scope === 'diocese' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold' : 'text-slate-500 hover:text-slate-900' }}">Diocese</button>
        </div>

        <!-- PROXIMITY CARD -->
        @if($userRank)
            <div class="bg-purple-600 text-white rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center font-bold text-sm">#{{ $userRank }}</span>
                    <div>
                        <h4 class="font-bold text-xs">Your Current Rank</h4>
                        <p class="text-[11px] text-purple-100">{{ number_format($userPoints) }} XP Earned</p>
                    </div>
                </div>
                @if($pointsBehind && $aheadPlayerName)
                    <div class="text-right text-[11px]">
                        <span class="text-purple-200">Behind {{ explode(' ', $aheadPlayerName)[0] }}:</span>
                        <span class="font-bold block">+{{ number_format($pointsBehind) }} XP</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- TOP 3 PODIUM / LIST -->
        <div class="space-y-2">
            @foreach($top3->concat($remaining) as $index => $entry)
                <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs {{ $entry->user_id === $currentUser->id ? 'ring-2 ring-purple-600' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white">{{ $entry->user_name }}</h4>
                            <span class="text-[11px] text-slate-500">{{ $entry->parish_name }}</span>
                        </div>
                    </div>
                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($entry->total_points) }} XP</span>
                </div>
            @endforeach
        </div>
    @endif

</div>
