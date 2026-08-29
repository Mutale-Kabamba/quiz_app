<div class="space-y-4 pb-8">

    <!-- HEADER / PARISH IDENTIFIER -->
    <div class="flex items-center justify-between pt-1">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Youth Verification</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $parish?->name ?? 'Livingstone Diocese' }}</p>
        </div>
        <span class="px-2.5 py-1 rounded-md bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 font-bold text-xs">
            {{ $pendingYouths->count() }} Pending
        </span>
    </div>

    <!-- FLASH MESSAGE -->
    @if (session()->has('message'))
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- PENDING YOUTH CARDS -->
    <div class="space-y-3">
        @forelse($pendingYouths as $youth)
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-sm border border-purple-200 dark:border-purple-800">
                            {{ substr($youth->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $youth->name }}</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $youth->phone }}</p>
                            @if($youth->email)
                                <p class="text-[10px] text-slate-400">{{ $youth->email }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">
                        {{ $youth->created_at->diffForHumans(null, true) }}
                    </span>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        wire:click="openRejectModal('{{ $youth->id }}')" 
                        class="py-2 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-950/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900/50 font-semibold text-xs transition-colors">
                        Reject
                    </button>

                    <button 
                        type="button" 
                        wire:click="approve('{{ $youth->id }}')" 
                        class="py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors">
                        Approve Youth
                    </button>
                </div>
            </div>
        @empty
            <div class="p-8 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-900 dark:text-white">All Caught Up</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">There are no pending youth registrations for {{ $parish?->name ?? 'your parish' }}.</p>
                <div class="pt-2 text-xs font-medium text-slate-400">
                    Total Approved: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $approvedYouthsCount }} youths</span>
                </div>
            </div>
        @endforelse
    </div>

    <!-- REJECTION REASON MODAL -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-xs w-full space-y-3 shadow-xl">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white">Reason for Rejection</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">This will be shown to the youth so they can correct their registration.</p>

                <div>
                    <textarea 
                        wire:model="rejectionReason" 
                        rows="3" 
                        placeholder="e.g., Please register under your local parish outstation."
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-red-500"></textarea>
                    @error('rejectionReason') <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2">
                    <button 
                        type="button" 
                        wire:click="$set('showRejectModal', false)" 
                        class="w-1/2 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors">
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        wire:click="confirmReject" 
                        class="w-1/2 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold text-xs transition-colors">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
