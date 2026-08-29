<div class="py-2 max-w-sm mx-auto">
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 mx-auto flex items-center justify-center text-slate-950 shadow-glow-gold mb-2.5">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H4v2h7v9h2v-9h7v-2h-7V2z"/></svg>
        </div>
        <h2 class="text-xl font-black font-display text-white">Parish Youth Registration</h2>
        <p class="text-xs text-slate-400 mt-0.5">Livingstone Diocese Catholic Youth Ministry</p>
    </div>

    <!-- Registration Card -->
    <form wire:submit.prevent="register" class="bg-slate-900 border border-slate-800/80 rounded-3xl p-5 shadow-xl space-y-3.5">
        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Full Name</label>
            <input 
                type="text" 
                wire:model="name" 
                placeholder="e.g., Mutale Mwamba"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
            @error('name') <span class="text-[11px] text-rose-400 font-semibold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Phone Number</label>
            <input 
                type="text" 
                wire:model="phone" 
                placeholder="+260 97X XXXXXX"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
            @error('phone') <span class="text-[11px] text-rose-400 font-semibold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Deanery Selector -->
        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Select Deanery</label>
            <select 
                wire:model.live="deanery_id" 
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500">
                <option value="">-- Choose Deanery --</option>
                @foreach($deaneries as $deanery)
                    <option value="{{ $deanery->id }}">{{ $deanery->name }}</option>
                @endforeach
            </select>
            @error('deanery_id') <span class="text-[11px] text-rose-400 font-semibold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Parish Selector -->
        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Select Your Parish</label>
            <select 
                wire:model="parish_id" 
                @disabled(!$deanery_id)
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500 disabled:opacity-50">
                <option value="">{{ $deanery_id ? '-- Choose Parish --' : 'Select a Deanery first' }}</option>
                @foreach($parishes as $parish)
                    <option value="{{ $parish->id }}">{{ $parish->name }}</option>
                @endforeach
            </select>
            @error('parish_id') <span class="text-[11px] text-rose-400 font-semibold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Password</label>
            <input 
                type="password" 
                wire:model="password" 
                placeholder="At least 6 characters"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
            @error('password') <span class="text-[11px] text-rose-400 font-semibold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Confirm Password</label>
            <input 
                type="password" 
                wire:model="password_confirmation" 
                placeholder="Repeat password"
                class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
        </div>

        <button 
            type="submit" 
            class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-2xl shadow-lg shadow-amber-500/20 active:scale-[0.98] transition-all text-sm mt-2 flex items-center justify-center gap-2">
            <span wire:loading.remove>Create Youth Account</span>
            <span wire:loading class="flex items-center gap-1.5">
                <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Registering...
            </span>
        </button>

        <div class="pt-2 text-center border-t border-slate-800/80">
            <p class="text-xs text-slate-400">
                Already registered? 
                <a href="/login" class="text-amber-400 font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </form>
</div>
