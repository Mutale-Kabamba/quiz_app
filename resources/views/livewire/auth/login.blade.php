<div class="relative py-8 max-w-sm mx-auto flex flex-col justify-center min-h-[85vh] overflow-hidden">
    
    <!-- SUBTLE BACKGROUND GEOMETRY (FAINT DIOCESAN WATERMARK) -->
    <div class="absolute inset-0 pointer-events-none flex items-center justify-center -z-10 opacity-[0.03] dark:opacity-[0.04]">
        <svg class="w-96 h-96 text-purple-950 dark:text-purple-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
        </svg>
    </div>

    <!-- DIOCESAN BRANDING HEADER -->
    <div class="text-center mb-6 space-y-2">
        <!-- Brand Mark Container -->
        <div class="relative w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200/80 dark:border-purple-800/80 text-purple-700 dark:text-purple-300 mx-auto flex items-center justify-center shadow-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
        </div>

        <div class="space-y-1">
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300">
                Livingstone Diocese &bull; Zambia
            </span>
            <h1 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catholic Youth Ministry</h1>
            <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">Welcome Back</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Continue your faith formation journey in Scripture &amp; Catechism.</p>
        </div>
    </div>

    <!-- LOGIN FORM CARD (RICH MINIMALISM) -->
    <form wire:submit.prevent="login" 
          @submit="try { sessionStorage.setItem('show_login_preloader', 'true'); } catch(e) {}"
          x-data="{ showPassword: false }"
          class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm">
        
        <!-- Error Alert -->
        @if($errors->has('identifier'))
            <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 rounded-xl text-xs text-red-700 dark:text-red-300 leading-snug flex items-start gap-2 animate-fade-in">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first('identifier') }}</span>
            </div>
        @endif

        <!-- Identifier Input (Phone or Email) -->
        <div class="space-y-1">
            <label for="login-identifier" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                Phone Number or Email
            </label>
            <div class="relative">
                <input 
                    id="login-identifier"
                    type="text" 
                    wire:model="identifier" 
                    autocomplete="username"
                    required
                    placeholder="e.g. +260970000003 or youth@parish.com"
                    class="w-full pl-3.5 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
            </div>
        </div>

        <!-- Password Input with Visibility Toggle -->
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <label for="login-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                    Password
                </label>
                <a href="javascript:void(0)" 
                   onclick="alert('Please contact your Parish Chairperson or Youth Executive to reset your password.')" 
                   class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                    Forgot password?
                </a>
            </div>
            <div class="relative">
                <input 
                    id="login-password"
                    :type="showPassword ? 'text' : 'password'" 
                    wire:model="password" 
                    autocomplete="current-password"
                    required
                    placeholder="••••••••"
                    class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
                
                <!-- Password Visibility Toggle Button -->
                <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    tabindex="-1"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                    title="Toggle password visibility">
                    <!-- Eye open -->
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <!-- Eye closed -->
                    <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center">
            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="remember"
                    class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-900">
                <span class="font-medium">Remember this device</span>
            </label>
        </div>

        <!-- Primary Sign In Action Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full py-3 bg-purple-600 hover:bg-purple-700 disabled:opacity-75 text-white font-bold rounded-xl transition-colors text-xs touch-press flex items-center justify-center gap-2 shadow-sm">
            <span wire:loading.remove>Sign In</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Signing In...</span>
            </span>
        </button>

        <!-- Switch to Register -->
        <div class="pt-3 text-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                New youth member?
                <a href="/register" class="text-purple-600 dark:text-purple-400 font-bold hover:underline block mt-1">
                    Register under your Parish &rarr;
                </a>
            </p>
        </div>
    </form>
</div>
