<div class="space-y-5 pb-6">

    <!-- CHAIRPERSON / ADMIN QUICK STATS WIDGET (If applicable) -->
    @if($chairpersonStats)
        <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 border border-amber-500/30 rounded-3xl p-4 shadow-xl">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-amber-400">
                        {{ auth()->user()->isSuperAdmin() ? 'Diocesan Super Admin Monitor' : 'Parish Chairperson Portal' }}
                    </h3>
                </div>
                <a href="/approvals" class="text-[11px] font-extrabold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-xl border border-amber-500/20 hover:bg-amber-500 hover:text-slate-950 transition-colors">
                    Manage Approvals &rarr;
                </a>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-950/60 p-2.5 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 block">Pending</span>
                    <span class="text-base font-black text-amber-400">{{ $chairpersonStats['pending_approvals'] }}</span>
                </div>
                <div class="bg-slate-950/60 p-2.5 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 block">Total Youths</span>
                    <span class="text-base font-black text-white">{{ $chairpersonStats['total_parish_youth'] }}</span>
                </div>
                <div class="bg-slate-950/60 p-2.5 rounded-2xl border border-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 block">Active 7d</span>
                    <span class="text-base font-black text-emerald-400">{{ $chairpersonStats['active_this_week'] }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- DAILY LITURGICAL & DIOCESAN RALLY WIDGET -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-5 text-slate-950 shadow-glow-gold">
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-950/25 text-slate-950 text-[10px] font-black uppercase tracking-wider">
                    <span>✝️</span> Daily Liturgical Office
                </span>
                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-950/10 text-slate-950">
                    {{ now()->format('M d, Y') }}
                </span>
            </div>

            <h2 class="text-lg font-black font-display leading-tight text-slate-950">
                Saint of the Day: St. Augustine of Hippo
            </h2>
            <p class="mt-1 text-xs font-semibold text-slate-900 leading-snug italic">
                "Our heart is restless until it rests in you, O Lord." &bull; <span class="font-bold">Confessions I, 1</span>
            </p>

            <!-- Rally Countdown Bar -->
            <div class="mt-3.5 pt-3 border-t border-slate-950/15 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-900 uppercase tracking-tight block">Livingstone Youth Rally</span>
                    <span class="text-xs font-black text-slate-950">Grand Catechism Trophy 2026</span>
                </div>
                <a href="/quiz" class="px-3.5 py-1.5 bg-slate-950 hover:bg-slate-900 text-amber-400 font-extrabold text-xs rounded-xl shadow-md transition-all active:scale-95">
                    Prepare Now
                </a>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- QUIZ ARENA QUICK LAUNCH MODES -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-extrabold text-white font-display">Competition Arena</h3>
            <span class="text-xs text-amber-400 font-bold">Choose Mode</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <!-- Mode 1: Practice Mode (Always Unlocked) -->
            <a href="/quiz?mode=practice" class="p-4 rounded-3xl bg-slate-900 border border-slate-800 hover:border-amber-500/40 transition-all group flex flex-col justify-between shadow-lg touch-press">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">Practice Mode</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-2">Untimed, instant theological citations &amp; references.</p>
                </div>
                <span class="mt-3 text-[10px] font-extrabold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg inline-block w-fit">
                    UNTIMED &bull; UNLIMITED
                </span>
            </a>

            <!-- Mode 2: Ranked Mode (Approval Locked) -->
            @php
                $isRankedLocked = auth()->check() && auth()->user()->isYouth() && !auth()->user()->isApproved();
            @endphp
            @if($isRankedLocked)
                <div class="p-4 rounded-3xl bg-slate-900/60 border border-slate-800/60 opacity-70 relative flex flex-col justify-between shadow-lg">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-300">Ranked Mode</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">Earn Diocesan leaderboard points.</p>
                    </div>
                    <span class="mt-3 text-[10px] font-extrabold text-amber-400/80 bg-amber-500/10 px-2 py-0.5 rounded-lg inline-block w-fit">
                        🔒 PENDING APPROVAL
                    </span>
                </div>
            @else
                <a href="/quiz?mode=ranked" class="p-4 rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 border border-amber-500/30 hover:border-amber-400 transition-all group flex flex-col justify-between shadow-lg touch-press">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">Ranked Mode</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-2">Timed sessions with speed bonus multipliers.</p>
                    </div>
                    <span class="mt-3 text-[10px] font-extrabold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-lg inline-block w-fit">
                        🏆 LEADERBOARD ELIGIBLE
                    </span>
                </a>
            @endif
        </div>

        <!-- Mode 3: Live Rally Mode Bar -->
        <button 
            type="button"
            wire:click="$toggle('showRallyModal')"
            class="w-full mt-3 p-3.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/40 flex items-center justify-between shadow-md touch-press transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm font-black">
                    ⚡
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-white">Live Rally Arena (Laravel Reverb)</h4>
                    <p class="text-[10px] text-slate-400">Enter rally PIN code for real-time multiplayer</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-xl bg-purple-500/10 text-purple-300 font-bold text-xs">
                Enter PIN &rarr;
            </span>
        </button>
    </div>

    <!-- STUDY TRACKS / CATEGORIES -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-extrabold text-white font-display">Study Tracks (Catechetical Pillars)</h3>
            <a href="/study" class="text-xs text-amber-400 font-bold hover:underline">View All</a>
        </div>

        <div class="space-y-2.5">
            @foreach($categories as $category)
                <a href="/study/{{ $category->id }}" class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800/90 hover:border-amber-500/40 flex items-center justify-between transition-all group touch-press">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l-10-5v9l10 5 10-5V6l-10 5z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors">{{ $category->name }}</h4>
                            <p class="text-[11px] text-slate-400 line-clamp-1">{{ $category->description }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[11px] font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg">
                            {{ $category->questions_count }} Qs
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- LIVE RALLY PIN MODAL POPUP -->
    @if($showRallyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-xs w-full shadow-2xl space-y-4">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center mx-auto mb-2 text-xl font-bold">
                        ⚡
                    </div>
                    <h3 class="text-base font-black text-white font-display">Join Live Rally</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Enter the 6-digit PIN announced on the projector</p>
                </div>

                <div>
                    <input 
                        type="text" 
                        wire:model="rallyPin" 
                        maxlength="6"
                        placeholder="000000"
                        class="w-full text-center tracking-[0.5em] text-2xl font-black px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-amber-400 focus:outline-none focus:border-amber-500">
                    @error('rallyPin') <span class="text-[11px] text-rose-400 block text-center mt-1 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2">
                    <button 
                        type="button"
                        wire:click="$toggle('showRallyModal')" 
                        class="w-1/2 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                        Cancel
                    </button>
                    <button 
                        type="button"
                        wire:click="joinLiveRally" 
                        class="w-1/2 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20">
                        Join Game
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
