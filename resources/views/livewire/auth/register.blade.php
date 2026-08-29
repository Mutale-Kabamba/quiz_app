<div class="py-4 max-w-sm mx-auto">
    <!-- Header -->
    <div class="text-center mb-5">
        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 mx-auto flex items-center justify-center mb-2.5">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Parish Youth Registration</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Livingstone Diocese Catholic Youth Ministry</p>
    </div>

    <!-- Registration Form (Flat M3) -->
    <form wire:submit.prevent="register" class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 space-y-3.5">
        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
            <input 
                type="text" 
                wire:model="name" 
                placeholder="e.g. Mutale Mwamba"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
            @error('name') <span class="text-[11px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
            <input 
                type="text" 
                wire:model="phone" 
                placeholder="+260 97X XXXXXX"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
            @error('phone') <span class="text-[11px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Deanery Selector -->
        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Deanery</label>
            <select 
                wire:model.live="deanery_id" 
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-500 transition-colors">
                <option value="">-- Select Deanery --</option>
                @foreach($deaneries as $deanery)
                    <option value="{{ $deanery->id }}">{{ $deanery->name }}</option>
                @endforeach
            </select>
            @error('deanery_id') <span class="text-[11px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Cascading Parish Selector -->
        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Parish</label>
            <select 
                wire:model="parish_id" 
                @disabled(!$deanery_id)
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-500 disabled:opacity-50 transition-colors">
                <option value="">{{ $deanery_id ? '-- Select Parish --' : 'Select a Deanery first' }}</option>
                @foreach($parishes as $parish)
                    <option value="{{ $parish->id }}">{{ $parish->name }}</option>
                @endforeach
            </select>
            @error('parish_id') <span class="text-[11px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <input 
                type="password" 
                wire:model="password" 
                placeholder="At least 6 characters"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
            @error('password') <span class="text-[11px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm Password</label>
            <input 
                type="password" 
                wire:model="password_confirmation" 
                placeholder="Repeat password"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
        </div>

        <button 
            type="submit" 
            class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors text-xs touch-press flex items-center justify-center gap-2 mt-1">
            <span wire:loading.remove>Create Account</span>
            <span wire:loading class="flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Registering...
            </span>
        </button>

        <div class="pt-2 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Already registered? 
                <a href="/login" class="text-purple-600 dark:text-purple-400 font-semibold hover:underline">Sign In</a>
            </p>
        </div>
    </form>
</div>
