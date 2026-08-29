<div class="space-y-4 pb-8">

    <!-- HEADER / PARISH IDENTIFIER -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black font-display text-white">Youth Approvals</h2>
            <p class="text-xs text-amber-400 font-semibold">{{ $parish?->name ?? 'Livingstone Diocese' }}</p>
        </div>
        <span class="px-3 py-1 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-black text-xs">
            {{ $pendingYouths->count() }} Pending
        </span>
    </div>

    <!-- FLASH MESSAGE -->
    @if (session()->has('message'))
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-xs text-emerald-400 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- PENDING YOUTH CARDS -->
    <div class="space-y-3">
        @forelse($pendingYouths as $youth)
            <div class="p-4 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-black text-base border border-amber-500/20">
                            {{ substr($youth->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white leading-tight">{{ $youth->name }}</h4>
                            <p class="text-xs text-slate-400 leading-tight mt-0.5">{{ $youth->phone }}</p>
                            @if($youth->email)
                                <p class="text-[10px] text-slate-500">{{ $youth->email }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium">
                        {{ $youth->created_at->diffForHumans(null, true) }} ago
                    </span>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-800/80">
                    <button 
                        type="button"
                        wire:click="openRejectModal('{{ $youth->id }}')"
                        class="py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold text-xs flex items-center justify-center gap-1.5 transition-all touch-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>

                    <button 
                        type="button"
                        wire:click="approve('{{ $youth->id }}')"
                        class="py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-glow-emerald flex items-center justify-center gap-1.5 transition-all touch-press">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve Youth
                    </button>
                </div>
            </div>
        @empty
            <div class="p-8 rounded-3xl bg-slate-900 border border-slate-800 text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto text-xl">
                    ✅
                </div>
                <h3 class="text-sm font-bold text-white">All Caught Up!</h3>
                <p class="text-xs text-slate-400">There are no pending youth registrations for {{ $parish?->name ?? 'your parish' }}.</p>
                <div class="pt-2 text-xs font-bold text-slate-500">
                    Total Approved: <span class="text-amber-400">{{ $approvedYouthsCount }} youths</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- REJECTION REASON MODAL -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 max-w-xs w-full shadow-2xl space-y-3">
                <h3 class="text-sm font-black text-white font-display">Provide Reason for Rejection</h3>
                <p class="text-[11px] text-slate-400">This reason will be visible to the youth so they can correct their parish registration.</p>

                <div>
                    <textarea 
                        wire:model="rejectionReason" 
                        rows="3" 
                        placeholder="e.g., Not recognized in this parish youth roster. Please register under your local outstation."
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500"></textarea>
                    @error('rejectionReason') <span class="text-[10px] text-rose-400 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2">
                    <button 
                        type="button"
                        wire:click="$set('showRejectModal', false)"
                        class="w-1/2 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">
                        Cancel
                    </button>
                    <button 
                        type="button"
                        wire:click="confirmReject"
                        class="w-1/2 py-2 rounded-xl bg-rose-500 text-white font-bold text-xs shadow-md">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
