<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <meta name="theme-color" content="#7C3AED">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>{{ $title ?? 'Livingstone Diocese Catholic Youth Ministry' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN configured with Liturgical Tokens & Flat Surfaces) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        liturgical: {
                            purple: '#7C3AED',
                            'purple-light': '#F5F3FF',
                            'purple-dark': '#5B21B6',
                            green: '#059669',
                            'green-light': '#ECFDF5',
                            'green-dark': '#065F46',
                            gold: '#D97706',
                            'gold-light': '#FFFBEB',
                            'gold-dark': '#92400E',
                            red: '#DC2626',
                            'red-light': '#FEF2F2',
                            'red-dark': '#991B1B',
                        },
                        surface: {
                            base: '#F8FAFC',
                            card: '#FFFFFF',
                            variant: '#F1F5F9',
                            border: '#E2E8F0',
                            darkBase: '#0B0F19',
                            darkCard: '#121826',
                            darkVariant: '#1A2333',
                            darkBorder: '#1F293D',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        .touch-press:active { transform: scale(0.98); transition: transform 0.12s ease; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @livewireStyles
</head>
<body class="h-full font-sans bg-[#F8FAFC] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 flex flex-col justify-between selection:bg-purple-500 selection:text-white">

    <!-- Mobile-First App Wrapper -->
    <div class="max-w-md w-full mx-auto min-h-screen bg-[#F8FAFC] dark:bg-[#0B0F19] flex flex-col justify-between relative border-x border-slate-200/80 dark:border-slate-800/80 {{ auth()->check() ? 'pb-20' : 'pb-6' }}">

        @auth
            <!-- CLEAN CATHOLIC DIOCESAN HEADER -->
            <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#121826]/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800 px-4 py-3 transition-all">
                <div class="flex items-center justify-between">
                    <!-- Diocesan Cross & Parish Label -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 flex items-center justify-center flex-shrink-0 border border-purple-200 dark:border-purple-800">
                            <!-- Clean Catholic Cross Outline SVG -->
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xs font-bold text-slate-900 dark:text-white tracking-tight leading-tight">Livingstone Diocese</h1>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-tight">
                                {{ auth()->user()->parish?->name ?? 'Catholic Youth Ministry' }}
                            </p>
                        </div>
                    </a>

                    <!-- Streak Counter & Profile Link -->
                    <div class="flex items-center gap-2">
                        <!-- Formation Streak Badge (Clean Flame SVG) -->
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 text-amber-700 dark:text-amber-400 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ auth()->user()->current_streak ?? 0 }}d</span>
                        </div>

                        <!-- User Initial / Avatar -->
                        <a href="/profile" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300 hover:border-purple-400 transition-colors overflow-hidden">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </a>
                    </div>
                </div>

                <!-- PARISH APPROVAL ALERT (CALM SEMANTIC BANNER) -->
                @if(auth()->user()->isYouth() && auth()->user()->status === 'pending')
                    <div class="mt-2.5 p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 flex items-start gap-2 text-xs text-amber-800 dark:text-amber-300">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="leading-tight">
                            <span class="font-semibold">Parish Verification Pending:</span>
                            <span class="text-amber-700 dark:text-amber-300/80 text-[11px] block mt-0.5">Your Parish Chairperson will verify your account. You can study and practice freely.</span>
                        </div>
                    </div>
                @elseif(auth()->user()->isYouth() && auth()->user()->status === 'rejected')
                    <div class="mt-2.5 p-2.5 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 flex items-start gap-2 text-xs text-red-800 dark:text-red-300">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="leading-tight">
                            <span class="font-semibold">Verification Rejected:</span>
                            <span class="text-red-700 dark:text-red-300/80 text-[11px] block mt-0.5">{{ auth()->user()->rejection_reason ?? 'Please contact your Parish Youth Chairperson.' }}</span>
                        </div>
                    </div>
                @endif
            </header>
        @endauth

        <!-- MAIN SCROLLABLE VIEWPORT -->
        <main class="flex-1 w-full p-4 overflow-y-auto">
            {{ $slot }}
        </main>

        @auth
            <!-- CLEAN, MINIMAL BOTTOM NAVIGATION BAR (ICONS + LABELS) -->
            <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-[#121826]/95 backdrop-blur-sm border-t border-slate-200 dark:border-slate-800 max-w-md mx-auto px-4 py-2">
                <div class="flex items-center justify-between">
                    <!-- Tab 1: Home -->
                    <a href="/" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('/') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->is('/') ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-[11px] tracking-tight">Home</span>
                    </a>

                    <!-- Tab 2: Study -->
                    <a href="/study" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('study*') || request()->is('lesson*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ (request()->is('study*') || request()->is('lesson*')) ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-[11px] tracking-tight">Study</span>
                    </a>

                    <!-- Tab 3: Quiz -->
                    <a href="/quiz" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('quiz*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->is('quiz*') ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[11px] tracking-tight">Quiz</span>
                    </a>

                    <!-- Tab 4: Ranks -->
                    <a href="/leaderboard" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('leaderboard*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->is('leaderboard*') ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-[11px] tracking-tight">Ranks</span>
                    </a>

                    <!-- Tab 5: Dynamic Role-Based Hub -->
                    @if(auth()->user()->isChairperson())
                        <a href="/parish" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('parish*') || request()->is('approvals*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                            <div class="relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ (request()->is('parish*') || request()->is('approvals*')) ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                @php
                                    $pendingCount = \App\Models\User::where('role', 'youth')->where('status', 'pending')
                                        ->where('parish_id', auth()->user()->parish_id)
                                        ->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="absolute -top-1 -right-2 px-1.5 py-0.2 bg-red-600 text-white rounded-full text-[9px] font-bold">{{ $pendingCount }}</span>
                                @endif
                            </div>
                            <span class="text-[11px] tracking-tight">Parish</span>
                        </a>
                    @elseif(auth()->user()->isSuperAdmin())
                        <a href="/diocese" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('diocese*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->is('diocese*') ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                            </svg>
                            <span class="text-[11px] tracking-tight">Diocese</span>
                        </a>
                    @else
                        <a href="/profile" class="flex flex-col items-center gap-1 py-1 px-3 rounded-lg transition-colors {{ request()->is('profile*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-normal' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="{{ request()->is('profile*') ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-[11px] tracking-tight">Profile</span>
                        </a>
                    @endif
                </div>
            </nav>
        @endauth
    </div>

    <!-- Also update layout for standalone pages -->
    @livewireScripts
</body>
</html>
