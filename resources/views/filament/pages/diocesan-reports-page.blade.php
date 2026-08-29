<x-filament-panels::page>
    <div class="space-y-6">

        <!-- REPORT SELECTOR -->
        <div class="flex items-center gap-2 p-1 bg-slate-900 rounded-xl border border-slate-800 w-fit">
            <button 
                type="button" 
                wire:click="setReport('census')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $reportType === 'census' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                Youth Population &amp; Parishes
            </button>
            <button 
                type="button" 
                wire:click="setReport('deanery')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $reportType === 'deanery' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                Deanery Formation Standings
            </button>
            <button 
                type="button" 
                wire:click="setReport('quality')"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $reportType === 'quality' ? 'bg-amber-500 text-slate-950 font-black' : 'text-slate-400 hover:text-white' }}">
                Question Quality &amp; Disputes
            </button>
        </div>

        <!-- REPORT CONTAINER -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs text-amber-400 font-bold uppercase block">Official Diocesan Ministry Report</span>
                        <h2 class="text-lg font-black text-white">
                            @if($reportType === 'census')
                                Diocesan Youth Census &amp; Parish Status Report
                            @elseif($reportType === 'deanery')
                                Deanery Comparative Formation &amp; Competition Report
                            @else
                                Question Bank Quality, Disputes &amp; Flagged Items
                            @endif
                        </h2>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">{{ $generatedDate }}</span>
                </div>
            </x-slot>

            @if($reportType === 'census')
                <div class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-400">Total Youth</span>
                            <span class="text-xl font-black text-white block mt-1">{{ number_format($kpis['total_youth']) }}</span>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-400">Active This Week</span>
                            <span class="text-xl font-black text-emerald-400 block mt-1">{{ number_format($kpis['active_this_week']) }}</span>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-400">Total Parishes</span>
                            <span class="text-xl font-black text-amber-400 block mt-1">{{ $kpis['total_parishes'] }}</span>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-xs text-slate-400">New (30 Days)</span>
                            <span class="text-xl font-black text-purple-400 block mt-1">+{{ $kpis['new_registrations_month'] }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="text-slate-400 border-b border-slate-800 bg-slate-950/50">
                                <tr>
                                    <th class="py-2.5 px-3">Parish Name</th>
                                    <th class="py-2.5 px-3">Deanery</th>
                                    <th class="py-2.5 px-3">Location</th>
                                    <th class="py-2.5 px-3">Youth Population</th>
                                    <th class="py-2.5 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($parishes as $parish)
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-white">{{ $parish->name }}</td>
                                        <td class="py-2.5 px-3 text-slate-300">{{ $parish->deanery?->name ?? '—' }}</td>
                                        <td class="py-2.5 px-3 text-slate-400">{{ $parish->location ?? '—' }}</td>
                                        <td class="py-2.5 px-3 font-bold text-amber-400">{{ $parish->users_count }} Members</td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $parish->is_active ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                                {{ $parish->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($reportType === 'deanery')
                <div class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="text-slate-400 border-b border-slate-800 bg-slate-950/50">
                                <tr>
                                    <th class="py-2.5 px-3">Deanery</th>
                                    <th class="py-2.5 px-3">Parishes</th>
                                    <th class="py-2.5 px-3">Total Registered</th>
                                    <th class="py-2.5 px-3">Active (7d)</th>
                                    <th class="py-2.5 px-3">Quizzes Completed</th>
                                    <th class="py-2.5 px-3">Total Formation XP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($deaneries as $deanery)
                                    <tr>
                                        <td class="py-3 px-3 font-bold text-white">{{ $deanery['name'] }}</td>
                                        <td class="py-3 px-3 text-slate-300">{{ $deanery['parishes_count'] }} Parishes</td>
                                        <td class="py-3 px-3 font-bold text-white">{{ number_format($deanery['total_youth']) }}</td>
                                        <td class="py-3 px-3 text-emerald-400 font-bold">{{ number_format($deanery['active_youth']) }}</td>
                                        <td class="py-3 px-3 text-slate-300">{{ number_format($deanery['quizzes_count']) }}</td>
                                        <td class="py-3 px-3 font-mono font-black text-amber-400">+{{ number_format($deanery['total_xp']) }} XP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <div class="space-y-4">
                    @forelse($reportedQuestions as $q)
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px]">{{ $q->category?->name ?? 'Category' }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-bold text-[10px]">
                                    {{ $q->reports->count() }} Reports Submitted
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-white">{{ $q->question_text }}</h4>
                            <div class="text-slate-400">
                                <strong>Assigned Correct Answer:</strong> {{ $q->options[$q->correct_option_key] ?? $q->correct_option_key }} (Option {{ $q->correct_option_key }})
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No questions currently flagged or requiring review.</p>
                    @endforelse
                </div>
            @endif

        </x-filament::section>

    </div>
</x-filament-panels::page>
