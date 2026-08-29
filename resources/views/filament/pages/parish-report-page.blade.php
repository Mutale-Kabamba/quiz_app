<x-filament-panels::page>
    <div class="space-y-6">

        @if($report)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs text-amber-400 font-bold uppercase block">{{ $report['deanery_name'] }}</span>
                            <h2 class="text-lg font-black text-white">{{ $report['parish_name'] }} &bull; {{ $report['month_label'] }} Report</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Livingstone Diocese Youth Ministry</span>
                    </div>
                </x-slot>

                <div class="space-y-6">
                    <!-- SUMMARY METRICS TABLE -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                            <span class="text-xs text-slate-400 block font-semibold">Total Youth</span>
                            <span class="text-xl font-black text-white block mt-1">{{ number_format($report['total_youth']) }}</span>
                            <span class="text-[10px] text-amber-400 font-bold block mt-0.5">+{{ $report['new_registrations'] }} this month</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                            <span class="text-xs text-slate-400 block font-semibold">Active Youth</span>
                            <span class="text-xl font-black text-emerald-400 block mt-1">{{ number_format($report['active_youth']) }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Participating in app</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                            <span class="text-xs text-slate-400 block font-semibold">Lessons Mastered</span>
                            <span class="text-xl font-black text-amber-400 block mt-1">{{ number_format($report['lessons_completed']) }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Study tracks</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-center">
                            <span class="text-xs text-slate-400 block font-semibold">Quizzes Taken</span>
                            <span class="text-xl font-black text-purple-400 block mt-1">{{ number_format($report['total_quizzes']) }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $report['avg_accuracy'] }}% Avg Accuracy</span>
                        </div>
                    </div>

                    <!-- HIGHLIGHTS & KEY INSIGHTS -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-slate-800 pt-4 text-xs">
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Top Youth Performer</span>
                            <span class="text-sm font-bold text-amber-400 block mt-1">{{ $report['top_youth_name'] }}</span>
                            <span class="text-slate-400 font-semibold">{{ number_format($report['top_youth_xp']) }} XP</span>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Strongest Catechetical Track</span>
                            <span class="text-sm font-bold text-emerald-400 block mt-1">{{ $report['strongest_track'] }}</span>
                            <span class="text-slate-400 font-semibold">Highest accuracy</span>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Needs Improvement Track</span>
                            <span class="text-sm font-bold text-rose-400 block mt-1">{{ $report['weakest_track'] }}</span>
                            <span class="text-slate-400 font-semibold">Recommended focus</span>
                        </div>
                    </div>

                    <!-- RECOMMENDATION -->
                    @if($report['recommendation'])
                        <div class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-xs text-slate-200">
                            <strong class="text-amber-400">Pastoral Recommendation:</strong> {{ $report['recommendation'] }}
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <p class="text-sm text-slate-400">No active parish assigned to this administrator account.</p>
            </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
