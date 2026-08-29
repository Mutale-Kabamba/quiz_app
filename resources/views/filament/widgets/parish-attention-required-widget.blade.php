<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚡</span>
                <span class="text-base font-bold">Parish Action Items &bull; Attention Required</span>
            </div>
        </x-slot>

        @if($kpis)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. INACTIVE YOUTH ALERT -->
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Inactive Youth</span>
                        <h4 class="text-sm font-bold text-white">
                            {{ $kpis['inactive_youth_count'] }} youth inactive (14+ days)
                        </h4>
                        <p class="text-xs text-slate-400">Needs motivation &amp; engagement</p>
                    </div>
                    <a href="/admin/parish-youths?tableFilters[inactive_filter][value]=1" class="px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold hover:bg-amber-400 transition-colors shadow-sm">
                        View &rarr;
                    </a>
                </div>

                <!-- 2. PENDING REGISTRATIONS -->
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Registrations</span>
                        <h4 class="text-sm font-bold text-white">
                            {{ $kpis['pending_approvals_count'] }} pending verification
                        </h4>
                        <p class="text-xs text-slate-400">Waiting for chairperson sign-off</p>
                    </div>
                    <a href="/admin/youth-approvals" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors">
                        Review &rarr;
                    </a>
                </div>

                <!-- 3. TODAY'S DAILY CHALLENGE -->
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">Daily Challenge</span>
                        <h4 class="text-sm font-bold text-white">
                            {{ $kpis['challenge_completed_today'] }} completed today
                        </h4>
                        <p class="text-xs text-slate-400">+50 XP Streak Challenge</p>
                    </div>
                    <a href="/admin/parish-announcements/create" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold transition-colors">
                        Remind &rarr;
                    </a>
                </div>
            </div>
        @else
            <p class="text-sm text-slate-400">No active parish context assigned to this account.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
