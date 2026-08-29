<x-filament-panels::page>
    <div class="space-y-6">

        <!-- DEANERY COMPARATIVE PERFORMANCE TABLE -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold">Deanery Comparative Standings</span>
                    <span class="text-xs text-amber-400 font-bold uppercase">Diocese of Livingstone</span>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-2.5 px-3">Deanery</th>
                            <th class="py-2.5 px-3">Parishes</th>
                            <th class="py-2.5 px-3">Total Youth</th>
                            <th class="py-2.5 px-3">Active (7d)</th>
                            <th class="py-2.5 px-3">Quizzes</th>
                            <th class="py-2.5 px-3">Total Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($deaneries as $index => $deanery)
                            <tr class="hover:bg-slate-900/50 transition-colors">
                                <td class="py-3 px-3 font-bold text-white flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-md bg-slate-800 flex items-center justify-center text-[10px] text-amber-400 font-black">#{{ $index + 1 }}</span>
                                    {{ $deanery['name'] }} ({{ $deanery['code'] }})
                                </td>
                                <td class="py-3 px-3 text-slate-300">{{ $deanery['parishes_count'] }} Parishes</td>
                                <td class="py-3 px-3 font-bold text-white">{{ number_format($deanery['total_youth']) }}</td>
                                <td class="py-3 px-3 text-emerald-400 font-bold">{{ number_format($deanery['active_youth']) }} ({{ $deanery['active_rate'] }}%)</td>
                                <td class="py-3 px-3 text-slate-300">{{ number_format($deanery['quizzes_count']) }}</td>
                                <td class="py-3 px-3 font-mono font-black text-amber-400">+{{ number_format($deanery['total_xp']) }} XP</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No deaneries recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- TOP 5 PARISHES GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-sm font-bold">Top Performing Parishes (XP &bull; Competitions)</span>
                </x-slot>

                <div class="space-y-3">
                    @forelse($topParishes as $pRank => $parish)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-black text-xs">
                                    #{{ $pRank + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-white">{{ $parish->name }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $parish->deanery?->name ?? 'Deanery' }} &bull; {{ $parish->users_count }} Members</span>
                                </div>
                            </div>
                            <span class="font-mono text-xs font-black text-amber-400">{{ number_format($parish->total_xp) }} XP</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No parish activity recorded yet.</p>
                    @endforelse
                </div>
            </x-filament::section>

            <!-- STUDY TRACKS DISTRIBUTION -->
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-sm font-bold">Catholic Study Tracks Distribution</span>
                </x-slot>

                <div class="space-y-3">
                    @foreach($tracks as $track)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $track->icon }}</span>
                                <span class="text-xs font-bold text-white">{{ $track->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold">{{ $track->lessons_count }} Lessons</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold">{{ $track->questions_count }} Questions</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>
