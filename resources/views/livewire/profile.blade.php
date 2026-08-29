<div class="space-y-4 pb-10">

    <!-- PROFILE CARD -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl text-center relative overflow-hidden">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 mx-auto flex items-center justify-center text-slate-950 font-black text-2xl shadow-glow-gold mb-3 border-2 border-amber-300">
            {{ substr($user->name, 0, 1) }}
        </div>

        <h2 class="text-lg font-black font-display text-white">{{ $user->name }}</h2>
        <p class="text-xs text-amber-400 font-semibold">{{ $user->parish?->name ?? 'Livingstone Diocese' }}</p>
        <span class="text-[10px] text-slate-500 block mt-0.5">{{ $user->parish?->deanery?->name ?? 'Livingstone Deanery' }}</span>

        <!-- STATUS BADGE -->
        <div class="mt-3">
            @if($user->status === 'approved')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-full text-xs font-black">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Verified Parish Youth
                </span>
            @elseif($user->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-full text-xs font-black">
                    ⏳ Verification Pending
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-full text-xs font-black">
                    ⚠️ Verification Rejected
                </span>
            @endif
        </div>
    </div>

    <!-- STATS COUNTERS -->
    <div class="grid grid-cols-3 gap-2.5">
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-2xl text-center shadow-lg">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Points</span>
            <span class="text-base font-black text-amber-400 font-display">{{ number_format($totalScore) }}</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-2xl text-center shadow-lg">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Active Streak</span>
            <span class="text-base font-black text-emerald-400 font-display">🔥 {{ $user->current_streak }}d</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-2xl text-center shadow-lg">
            <span class="text-[10px] text-slate-400 font-bold uppercase block">Sessions</span>
            <span class="text-base font-black text-slate-200 font-display">{{ $totalSessions }}</span>
        </div>
    </div>

    <!-- CATECHETICAL MILESTONES & BADGES -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-3">
        <h3 class="text-sm font-extrabold text-white font-display">Earned Milestones &amp; Badges</h3>

        <div class="grid grid-cols-3 gap-2 text-center">
            <!-- Badge 1 -->
            <div class="p-2.5 bg-slate-950 rounded-2xl border border-slate-800 flex flex-col items-center">
                <span class="text-2xl mb-1">📜</span>
                <h4 class="text-[10px] font-bold text-white leading-tight">Scripture Pillar</h4>
                <span class="text-[9px] text-amber-400 font-semibold mt-0.5">Unlocked</span>
            </div>

            <!-- Badge 2 -->
            <div class="p-2.5 bg-slate-950 rounded-2xl border border-slate-800 flex flex-col items-center">
                <span class="text-2xl mb-1">🕊️</span>
                <h4 class="text-[10px] font-bold text-white leading-tight">YOUCAT Scholar</h4>
                <span class="text-[9px] text-amber-400 font-semibold mt-0.5">Unlocked</span>
            </div>

            <!-- Badge 3 (Locked) -->
            <div class="p-2.5 bg-slate-950/40 rounded-2xl border border-slate-800/40 flex flex-col items-center opacity-40">
                <span class="text-2xl mb-1">⚡</span>
                <h4 class="text-[10px] font-bold text-slate-400 leading-tight">Rally Champion</h4>
                <span class="text-[9px] text-slate-500 font-semibold mt-0.5">Locked</span>
            </div>
        </div>
    </div>

    <!-- LOGOUT BUTTON -->
    <button 
        type="button"
        wire:click="logout" 
        class="w-full py-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold rounded-2xl text-xs transition-all touch-press">
        Sign Out Account
    </button>
</div>
