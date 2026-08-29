<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛡️</span>
                <span class="text-base font-bold">Diocesan Command Center &bull; Action Required</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. REPORTED QUESTIONS / DISPUTES -->
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Content Quality Control</span>
                    <h4 class="text-sm font-bold text-white">
                        {{ $pending_reports }} Question Disputes Reported
                    </h4>
                    <p class="text-xs text-slate-400">Typos, bad citations, or answer disputes</p>
                </div>
                <a href="/admin/question-reports" class="px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold hover:bg-amber-400 transition-colors shadow-sm">
                    Resolve &rarr;
                </a>
            </div>

            <!-- 2. PENDING PARISH TRANSFERS -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">Youth Transfers</span>
                    <h4 class="text-sm font-bold text-white">
                        {{ $pending_transfers }} Pending Transfer Requests
                    </h4>
                    <p class="text-xs text-slate-400">Inter-parish relocations awaiting approval</p>
                </div>
                <a href="/admin/parish-transfers" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                    Review &rarr;
                </a>
            </div>

            <!-- 3. INTEGRITY & ANTI-CHEATING MONITOR -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-wider block">Platform Integrity</span>
                    <h4 class="text-sm font-bold text-white">
                        {{ $cheating_flags_count }} Anomaly Flags Detected
                    </h4>
                    <p class="text-xs text-slate-400">Rapid response timing or score spikes</p>
                </div>
                <a href="/admin/audit-logs" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-300 text-xs font-bold transition-colors">
                    Inspect &rarr;
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
