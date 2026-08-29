<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <meta name="theme-color" content="#0A0F1D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $title ?? 'Livingstone Diocese Catholic Youth Ministry' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN with Custom Palette) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            navy: '#0A0F1D',
                            surface: '#0F172A',
                            card: '#1E293B',
                            gold: '#F59E0B',
                            goldDark: '#D97706',
                            crimson: '#991B1B',
                            emerald: '#10B981',
                        }
                    },
                    boxShadow: {
                        'glow-gold': '0 0 25px -5px rgba(245, 158, 11, 0.3)',
                        'glow-emerald': '0 0 25px -5px rgba(16, 185, 129, 0.3)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        .touch-press:active { transform: scale(0.97); transition: transform 0.1s ease; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @livewireStyles
</head>
<body class="h-full font-sans bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-amber-500 selection:text-slate-950">

    <!-- Mobile Device Outer Wrapper -->
    <div class="max-w-md w-full mx-auto min-h-screen bg-slate-950 flex flex-col justify-between relative shadow-2xl border-x border-slate-900/60 {{ auth()->check() ? 'pb-20' : 'pb-6' }}">

        @auth
            <!-- AUTHENTICATED APP STICKY TOP HEADER -->
            <header class="sticky top-0 z-40 backdrop-blur-xl bg-slate-950/85 border-b border-slate-800/80 px-4 py-2.5 transition-all">
                <div class="flex items-center justify-between">
                    <!-- Diocesan Crest & User Parish -->
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 shadow-glow-gold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H4v2h7v9h2v-9h7v-2h-7V2z"/></svg>
                        </div>
                        <div>
                            <h1 class="text-sm font-extrabold font-display text-white tracking-tight leading-none">Livingstone Diocese</h1>
                            <p class="text-[11px] font-semibold text-amber-400/90 leading-tight mt-0.5">
                                {{ auth()->user()->parish?->name ?? 'Catholic Youth Ministry' }}
                            </p>
                        </div>
                    </a>

                    <!-- Streak Counter & Profile Avatar -->
                    <div class="flex items-center gap-2">
                        <!-- Active Study Streak Badge -->
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold shadow-sm">
                            <span class="animate-bounce">🔥</span>
                            <span>{{ auth()->user()->current_streak ?? 0 }} <span class="text-[10px] text-amber-500/80 font-semibold">d</span></span>
                        </div>

                        <!-- Profile Avatar -->
                        <a href="/profile" class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-black text-amber-400">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </a>
                    </div>
                </div>

                <!-- PARISH APPROVAL STATE ALERT BANNER -->
                @if(auth()->user()->isYouth() && auth()->user()->status === 'pending')
                    <div class="mt-2.5 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-2.5 text-xs text-amber-300">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <div class="leading-tight">
                            <span class="font-bold text-amber-300">Awaiting Parish Approval:</span>
                            <span class="text-amber-200/90 text-[11px] block mt-0.5">Your Parish Chairperson will verify your profile. You can study and play Practice Mode freely!</span>
                        </div>
                    </div>
                @elseif(auth()->user()->isYouth() && auth()->user()->status === 'rejected')
                    <div class="mt-2.5 p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-start gap-2.5 text-xs text-rose-300">
                        <svg class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <div class="leading-tight">
                            <span class="font-bold text-rose-300">Verification Rejected:</span>
                            <span class="text-rose-200/90 text-[11px] block mt-0.5">{{ auth()->user()->rejection_reason ?? 'Please contact your Parish Youth Chairperson.' }}</span>
                        </div>
                    </div>
                @endif
            </header>
        @endauth

        <!-- MAIN SCROLLABLE CONTENT VIEW -->
        <main class="flex-1 w-full p-4 overflow-y-auto">
            {{ $slot }}
        </main>

        @auth
            <!-- NATIVE FIXED BOTTOM NAVIGATION BAR (ONLY FOR LOGGED-IN USERS) -->
            <nav class="fixed bottom-0 left-0 right-0 z-50 bg-slate-950/90 backdrop-blur-xl border-t border-slate-800/80 max-w-md mx-auto px-3 py-2">
                <div class="flex items-center justify-around">
                    <!-- Tab 1: Home Dashboard -->
                    <a href="/" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('/') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-[10px] tracking-tight">Home</span>
                    </a>

                    <!-- Tab 2: Study Hub -->
                    <a href="/study" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('study*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="text-[10px] tracking-tight">Study</span>
                    </a>

                    <!-- Tab 3: Quiz Arena -->
                    <a href="/quiz" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('quiz*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                        <div class="relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-[10px] tracking-tight">Arena</span>
                    </a>

                    <!-- Tab 4: Leaderboards -->
                    <a href="/leaderboard" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('leaderboard*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="text-[10px] tracking-tight">Ranks</span>
                    </a>

                    <!-- Tab 5: Dynamic Role Tab (Parish Approvals for Chairperson, or Profile for Youth) -->
                    @if(auth()->user()->isChairperson() || auth()->user()->isSuperAdmin())
                        <a href="/approvals" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('approvals*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                            <div class="relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @php
                                    $pendingCount = \App\Models\User::where('role', 'youth')->where('status', 'pending')
                                        ->when(auth()->user()->isChairperson(), fn($q) => $q->where('parish_id', auth()->user()->parish_id))
                                        ->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="absolute -top-1 -right-2 px-1.5 py-0.2 bg-rose-500 text-white rounded-full text-[9px] font-black animate-pulse">{{ $pendingCount }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] tracking-tight">Approvals</span>
                        </a>
                    @else
                        <a href="/profile" class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->is('profile*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-[10px] tracking-tight">Profile</span>
                        </a>
                    @endif
                </div>
            </nav>
        @endauth
    </div>

    @livewireScripts
</body>
</html>
