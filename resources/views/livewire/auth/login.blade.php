<div class="py-6 max-w-sm mx-auto flex flex-col justify-center min-h-[85vh]">
    <!-- Diocesan Mobile Branding Splash Header -->
    <div class="text-center mb-6">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 mx-auto flex items-center justify-center text-slate-950 shadow-glow-gold mb-3.5 border-2 border-amber-300">
            <svg class="w-11 h-11" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H4v2h7v9h2v-9h7v-2h-7V2z"/></svg>
        </div>
        <span class="inline-block px-3 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] font-black uppercase tracking-wider text-amber-400 mb-1">
            Livingstone Diocese &bull; Zambia
        </span>
        <h2 class="text-2xl font-black font-display text-white tracking-tight">Catholic Youth Ministry</h2>
        <p class="text-xs text-slate-400 mt-1">Faith Formation, Catechism &amp; Rally Quiz</p>
    </div>

    <!-- Login Form Card -->
    <form wire:submit.prevent="login" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4 backdrop-blur-md">
        <div class="border-b border-slate-800 pb-2.5">
            <h3 class="text-sm font-extrabold text-white font-display">Sign In to Your Account</h3>
            <p class="text-[11px] text-slate-400">Enter your registered mobile number or email</p>
        </div>

        @if($errors->has('identifier'))
            <div class="p-3 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs text-rose-400 leading-snug">
                {{ $errors->first('identifier') }}
            </div>
        @endif

        <div>
            <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Phone Number / Email</label>
            <input 
                type="text" 
                wire:model="identifier" 
                placeholder="e.g. +260970000003 or email@domain.com"
                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
            <input 
                type="password" 
                wire:model="password" 
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors">
        </div>

        <button 
            type="submit" 
            class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-2xl shadow-glow-gold active:scale-[0.98] transition-all text-xs uppercase tracking-wider mt-2 flex items-center justify-center gap-2">
            <span wire:loading.remove>Sign In to App</span>
            <span wire:loading class="flex items-center gap-1.5">
                <svg class="animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Verifying Credentials...
            </span>
        </button>

        <!-- Quick Switch to Register -->
        <div class="pt-3 text-center border-t border-slate-800/80">
            <p class="text-xs text-slate-400">
                New youth member in the Diocese?
                <a href="/register" class="text-amber-400 font-bold hover:underline block mt-1">Register under your Parish &rarr;</a>
            </p>
        </div>
    </form>
</div>
