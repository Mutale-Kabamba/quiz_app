<x-filament-panels::page>
    <div class="space-y-6">

        <!-- TIMEFRAME FILTER -->
        <div class="flex items-center gap-2 p-1 bg-slate-900 rounded-xl border border-slate-800 w-fit">
            <button 
                type="button" 
                wire:click="setTimeframe('today')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $timeframe === 'today' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                Today
            </button>
            <button 
                type="button" 
                wire:click="setTimeframe('this_week')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $timeframe === 'this_week' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                This Week
            </button>
            <button 
                type="button" 
                wire:click="setTimeframe('this_month')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $timeframe === 'this_month' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                This Month
            </button>
            <button 
                type="button" 
                wire:click="setTimeframe('all_time')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $timeframe === 'all_time' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                All-Time
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- 1. PARISH YOUTH LEADERBOARD (2/3 Grid) -->
            <div class="lg:col-span-2 space-y-4">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold">{{ $myParish?->name ?? 'Parish' }} &bull; Top Youth</span>
                            <span class="text-xs text-amber-400 font-bold uppercase">{{ str_replace('_', ' ', $timeframe) }}</span>
                        </div>
                    </x-slot>

                    <div class="space-y-3">
                        @forelse($top3->concat($remaining) as $index => $rankItem)
                            <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-500 text-slate-950' : ($index === 1 ? 'bg-slate-700 text-slate-200' : ($index === 2 ? 'bg-amber-800 text-amber-200' : 'bg-slate-900 text-slate-400')) }}">
                                        #{{ $index + 1 }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-bold text-white leading-tight">{{ $rankItem->user_name }}</h4>
                                        <span class="text-xs text-slate-400">{{ $rankItem->attempts_count }} Quizzes Completed &bull; {{ (int) round($rankItem->avg_accuracy) }}% Accuracy</span>
                                    </div>
                                </div>
                                <span class="text-sm font-black text-amber-400 font-mono">+{{ number_format($rankItem->total_points) }} XP</span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-sm">
                                No ranked quiz attempts recorded for your parish youth in this timeframe.
                            </div>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>

            <!-- 2. PUBLIC PARISH COMPETITIVE STANDING (1/3 Grid) -->
            <div class="space-y-4">
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <span>👑</span>
                            <span class="text-base font-bold">Diocesan Parish Standings</span>
                        </div>
                    </x-slot>

                    <div class="space-y-2.5">
                        @forelse($parishStandings as $pIndex => $pStanding)
                            @php
                                $isMyParish = $pStanding->parish_id === $myParish?->id;
                            @endphp
                            <div class="p-3 rounded-xl border {{ $isMyParish ? 'bg-amber-500/10 border-amber-500/40 shadow-sm' : 'bg-slate-950 border-slate-800/80' }} flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-400 w-5">#{{ $pIndex + 1 }}</span>
                                    <span class="font-bold {{ $isMyParish ? 'text-amber-400' : 'text-white' }}">
                                        {{ $pStanding->parish_name }} {{ $isMyParish ? '(Your Parish)' : '' }}
                                    </span>
                                </div>
                                <span class="font-black text-slate-300">{{ number_format($pStanding->total_parish_points) }} XP</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No parish rankings recorded yet.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>

        </div>
    </div>
</x-filament-panels::page>
