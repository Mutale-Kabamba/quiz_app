<div class="py-6 max-w-sm mx-auto flex flex-col justify-center min-h-[85vh]">
    <!-- Diocesan Mobile Branding Header -->
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/40 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 mx-auto flex items-center justify-center mb-3">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
        </div>
        <span class="inline-block px-2.5 py-0.5 rounded bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 mb-1">
            Livingstone Diocese &bull; Zambia
        </span>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Catholic Youth Ministry</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Faith Formation, Catechism &amp; Quiz Platform</p>
    </div>

    <!-- Login Form Card (Flat M3) -->
    <form wire:submit.prevent="login" class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 space-y-4">
        <div class="border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white">Sign In to Your Account</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Enter your registered mobile number or email</p>
        </div>

        @if($errors->has('identifier'))
            <div class="p-2.5 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 rounded-lg text-xs text-red-700 dark:text-red-300 leading-snug">
                {{ $errors->first('identifier') }}
            </div>
        @endif

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number or Email</label>
            <input 
                type="text" 
                wire:model="identifier" 
                placeholder="e.g. +260970000003 or user@domain.com"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
        </div>

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <input 
                type="password" 
                wire:model="password" 
                placeholder="••••••••"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
        </div>

        <button 
            type="submit" 
            class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors text-xs touch-press flex items-center justify-center gap-2">
            <span wire:loading.remove>Sign In</span>
            <span wire:loading class="flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Verifying Credentials...
            </span>
        </button>

        <!-- Switch to Register -->
        <div class="pt-2 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                New youth member?
                <a href="/register" class="text-purple-600 dark:text-purple-400 font-semibold hover:underline block mt-0.5">Register under your Parish &rarr;</a>
            </p>
        </div>
    </form>
</div>
