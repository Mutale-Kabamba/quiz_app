<x-filament-panels::page>
    <div class="space-y-6">

        @if($analytics)
            <!-- 1. PASTORAL RECOMMENDATION BOX -->
            @if($analytics['recommendation'])
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3 shadow-md">
                    <span class="text-2xl flex-shrink-0">💡</span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-400">Pastoral Formation Insight</h4>
                        <p class="text-xs text-slate-200 mt-1 leading-relaxed">{{ $analytics['recommendation'] }}</p>
                    </div>
                </div>
            @endif

            <!-- 2. TRACK PERFORMANCE CARDS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($analytics['tracks'] as $track)
                    <x-filament::section>
                        <x-slot name="heading">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold">{{ $track['name'] }}</span>
                                <span class="text-xs font-black text-amber-400">{{ $track['avg_accuracy'] }}% Accuracy</span>
                            </div>
                        </x-slot>

                        <div class="space-y-3">
                            <!-- Progress Bar Indicator -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span>Average Quiz Accuracy</span>
                                    <span class="font-bold text-white">{{ $track['avg_accuracy'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-950 h-2 rounded-full overflow-hidden border border-slate-800">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 rounded-full"
                                         style="width: {{ $track['avg_accuracy'] }}%"></div>
                                </div>
                            </div>

                            <!-- Metrics Grid -->
                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-800/80 text-center">
                                <div class="p-2 rounded-xl bg-slate-950">
                                    <span class="text-[10px] text-slate-400 block font-semibold">Participating</span>
                                    <span class="text-xs font-bold text-white">{{ $track['participating_youth'] }} Youth</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-950">
                                    <span class="text-[10px] text-slate-400 block font-semibold">Completed</span>
                                    <span class="text-xs font-bold text-emerald-400">{{ $track['completed_lessons'] }} Lessons</span>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-950">
                                    <span class="text-[10px] text-slate-400 block font-semibold">Avg Score</span>
                                    <span class="text-xs font-bold text-amber-400">{{ $track['avg_score'] }} pts</span>
                                </div>
                            </div>
                        </div>
                    </x-filament::section>
                @endforeach
            </div>
        @else
            <x-filament::section>
                <p class="text-sm text-slate-400">No active parish assigned to this account.</p>
            </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
