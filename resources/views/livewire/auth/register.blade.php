<div class="relative py-6 max-w-sm mx-auto flex flex-col justify-center min-h-[85vh] overflow-hidden">
    
    <!-- SUBTLE BACKGROUND GEOMETRY (FAINT DIOCESAN WATERMARK) -->
    <div class="absolute inset-0 pointer-events-none flex items-center justify-center -z-10 opacity-[0.03] dark:opacity-[0.04]">
        <svg class="w-96 h-96 text-purple-950 dark:text-purple-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
        </svg>
    </div>

    <!-- DIOCESAN BRANDING HEADER -->
    <div class="text-center mb-5 space-y-2">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200/80 dark:border-purple-800/80 text-purple-700 dark:text-purple-300 mx-auto flex items-center justify-center shadow-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
        </div>
        <div class="space-y-1">
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300">
                Livingstone Diocese &bull; Zambia
            </span>
            <h1 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">Parish Youth Registration</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Join your parish youth community for diocesan formation.</p>
        </div>
    </div>

    <!-- REGISTRATION FORM CARD (RICH MINIMALISM) -->
    <form wire:submit.prevent="register" class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-3.5 shadow-sm">
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Full Name</label>
            <input 
                type="text" 
                wire:model="name" 
                autocomplete="name"
                required
                placeholder="e.g. Mutale Mwamba"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
            @error('name') <span class="text-[11px] text-red-500 font-bold mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Phone Number</label>
            <input 
                type="text" 
                wire:model="phone" 
                autocomplete="tel"
                required
                placeholder="+260 97X XXXXXX"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
            @error('phone') <span class="text-[11px] text-red-500 font-bold mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Deanery Selector -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Deanery</label>
            <select 
                wire:model.live="deanery_id" 
                required
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
                <option value="">-- Select Deanery --</option>
                @foreach($deaneries as $deanery)
                    <option value="{{ $deanery->id }}">{{ $deanery->name }}</option>
                @endforeach
            </select>
            @error('deanery_id') <span class="text-[11px] text-red-500 font-bold mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Parish Selector -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Parish</label>
            <select 
                wire:model="parish_id" 
                required
                @disabled(!$deanery_id)
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 disabled:opacity-50 transition-colors">
                <option value="">{{ $deanery_id ? '-- Select Parish --' : 'Select a Deanery first' }}</option>
                @foreach($parishes as $parish)
                    <option value="{{ $parish->id }}">{{ $parish->name }}</option>
                @endforeach
            </select>
            @error('parish_id') <span class="text-[11px] text-red-500 font-bold mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Password</label>
            <input 
                type="password" 
                wire:model="password" 
                autocomplete="new-password"
                required
                placeholder="At least 6 characters"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
            @error('password') <span class="text-[11px] text-red-500 font-bold mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Confirm Password</label>
            <input 
                type="password" 
                wire:model="password_confirmation" 
                autocomplete="new-password"
                required
                placeholder="Repeat password"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
        </div>

        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full py-3 bg-purple-600 hover:bg-purple-700 disabled:opacity-75 text-white font-bold rounded-xl transition-colors text-xs touch-press flex items-center justify-center gap-2 mt-2 shadow-sm">
            <span wire:loading.remove>Create Account</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Registering...</span>
            </span>
        </button>

        <div class="pt-3 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Already registered? 
                <a href="/login" class="text-purple-600 dark:text-purple-400 font-bold hover:underline">Sign In &rarr;</a>
            </p>
        </div>
    </form>
</div>
