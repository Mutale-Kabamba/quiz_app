<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#5B21B6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Catholic Youth">
    <meta name="application-name" content="Catholic Youth">
    
    <!-- PWA Web App Manifest & Icons -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <title>{{ $title ?? 'Livingstone Diocese Catholic Youth Ministry' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans (Modern UI) & Newsreader (Editorial Catholic Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;0,6..72,700;1,6..72,400;1,6..72,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with Liturgical Tokens & Flat Rich Surfaces -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        serif: ['"Newsreader"', 'Georgia', 'serif'],
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
                            rose: '#E11D48',
                            'rose-light': '#FFF1F2',
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
        .touch-press:active { transform: scale(0.98); transition: transform 0.12s cubic-bezier(0.4, 0, 0.2, 1); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Mobile Safe Area Insets for iOS & Android Status Bars / Dynamic Islands / Home Indicators */
        :root {
            --sat: env(safe-area-inset-top, 0px);
            --sab: env(safe-area-inset-bottom, 0px);
            --sal: env(safe-area-inset-left, 0px);
            --sar: env(safe-area-inset-right, 0px);
        }

        .safe-top-header {
            padding-top: max(0.875rem, env(safe-area-inset-top, 0px));
        }

        .safe-bottom-nav {
            padding-bottom: max(0.625rem, env(safe-area-inset-bottom, 0px));
        }

        .safe-container {
            padding-bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px));
        }

        /* Minimal Progress Animation for Preloader */
        @keyframes progressIndeterminate {
            0% { transform: translateX(-100%) scaleX(0.2); }
            50% { transform: translateX(0%) scaleX(0.7); }
            100% { transform: translateX(100%) scaleX(0.2); }
        }
        .animate-progress {
            animation: progressIndeterminate 1.1s infinite ease-in-out;
            transform-origin: left;
        }

        /* Subtle entrance transitions */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(3px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            #app-preloader {
                display: none !important;
            }
        }
    </style>
    <!-- Initialize Dark Mode Early to prevent flash -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Early Preloader Visibility Check: Prevent flash on internal in-app navigation -->
    <script>
        try {
            if (sessionStorage.getItem('app_session_started') && sessionStorage.getItem('show_login_preloader') !== 'true') {
                document.write('<style>#app-preloader { display: none !important; }</style>');
            }
        } catch(e) {}
    </script>
    @livewireStyles
</head>
<body class="h-full font-sans bg-[#F8FAFC] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 flex flex-col justify-between selection:bg-purple-600 selection:text-white"
      x-data="{ 
          profileMenuOpen: false, 
          currentTheme: localStorage.getItem('theme') || 'system',
          setTheme(mode) {
              this.currentTheme = mode;
              if (mode === 'dark') {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else if (mode === 'light') {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              } else {
                  localStorage.removeItem('theme');
                  if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                      document.documentElement.classList.add('dark');
                  } else {
                      document.documentElement.classList.remove('dark');
                  }
              }
          }
      }">

    <!-- GLOBAL BRANDED PRELOADER (ONLY ON INITIAL APP OPEN & LOGIN) -->
    <div id="app-preloader" 
         class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-[#F8FAFC] dark:bg-[#0B0F19] transition-opacity duration-300 pointer-events-none"
         aria-hidden="true">
        <div class="flex flex-col items-center space-y-4 text-center px-4">
            <!-- Catholic Diocesan Brand Mark with subtle pulsing ambient ring -->
            <div class="relative w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200/80 dark:border-purple-800/80 text-purple-700 dark:text-purple-300 flex items-center justify-center shadow-sm">
                <!-- Diocesan Cross Icon -->
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                </svg>
            </div>

            <!-- Application Title & Subtitle -->
            <div class="space-y-0.5">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Livingstone Diocese</h2>
                <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Catholic Youth Ministry</p>
            </div>

            <!-- Minimal Progress Indicator -->
            <div class="w-24 h-0.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden mt-1">
                <div class="h-full bg-purple-600 dark:bg-purple-400 rounded-full w-full animate-progress"></div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var preloader = document.getElementById('app-preloader');
            if (!preloader) return;
            
            var hide = function(instant) {
                if (instant) {
                    preloader.style.display = 'none';
                    return;
                }
                preloader.style.opacity = '0';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 400);
            };

            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                hide(true);
                return;
            }

            try {
                var hasSession = sessionStorage.getItem('app_session_started');
                var showOnLogin = sessionStorage.getItem('show_login_preloader');

                // Only show 4-second loading on initial app open or on login
                if (!hasSession || showOnLogin === 'true') {
                    sessionStorage.setItem('app_session_started', 'true');
                    sessionStorage.removeItem('show_login_preloader');
                    setTimeout(function() {
                        hide(false);
                    }, 4000);
                } else {
                    // Fast in-app navigation: immediately hide
                    hide(true);
                }
            } catch(e) {
                hide(true);
            }
        })();
    </script>

    <!-- Mobile-First Container (Max Width MD for App Feel, clean border for desktop view) -->
    <div class="max-w-md w-full mx-auto min-h-screen bg-[#F8FAFC] dark:bg-[#0B0F19] flex flex-col justify-between relative border-x border-slate-200/80 dark:border-slate-800/80 {{ auth()->check() ? 'safe-container' : 'pb-6' }}">

        @auth
            <!-- CATHOLIC DIOCESAN HEADER (RICH MINIMALISM) -->
            <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#121826]/95 backdrop-blur-md border-b border-slate-200/90 dark:border-slate-800/90 px-4 pb-2.5 safe-top-header transition-all">
                <div class="flex items-center justify-between">
                    <!-- Diocesan Seal & Parish Identity -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 flex items-center justify-center flex-shrink-0 border border-purple-200/80 dark:border-purple-800/80 group-hover:border-purple-400 transition-colors shadow-sm">
                            <!-- Catholic Cross Outline SVG -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h1 class="text-xs font-bold text-slate-900 dark:text-white tracking-tight leading-tight">Livingstone Diocese</h1>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-tight truncate max-w-[180px]">
                                {{ auth()->user()->parish?->name ?? 'Youth Formation' }}
                            </p>
                        </div>
                    </a>

                    <!-- Streak Badge & Profile Avatar Trigger -->
                    <div class="flex items-center gap-2">
                        <!-- Formation Streak Badge (Catholic Flame) -->
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200/90 dark:border-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-bold" title="Formation Streak">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.123 1.956-1.517 3.085-.395 1.13-.578 2.21-.578 2.867 0 .54.1 1.053.284 1.518-1.077-.92-1.762-2.31-1.762-3.868 0-.447-.07-1.002-.27-1.436a1 1 0 00-1.79.232c-.52 1.488-.89 3.25-.89 4.904 0 4.418 3.582 8 8 8s8-3.582 8-8c0-3.64-2.022-6.84-5.065-8.796z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ auth()->user()->current_streak ?? 0 }}d</span>
                        </div>

                        <!-- User Level & Avatar (Click to open quick profile options) -->
                        <div class="relative">
                            <button 
                                type="button"
                                @click="profileMenuOpen = !profileMenuOpen" 
                                class="relative group block focus:outline-none touch-press" 
                                title="Profile Options &amp; Theme">
                                <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/40 border-2 border-purple-300 dark:border-purple-700 flex items-center justify-center text-xs font-bold text-purple-700 dark:text-purple-300 group-hover:border-purple-500 transition-colors overflow-hidden aspect-square shadow-sm flex-shrink-0">
                                    @if(auth()->user()->avatar_url)
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full rounded-full object-cover aspect-square">
                                    @else
                                        <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <span class="absolute -bottom-1 -right-1 px-1 py-0.2 bg-purple-600 text-white rounded text-[8px] font-black leading-none">
                                    L{{ auth()->user()->level ?? 1 }}
                                </span>
                            </button>

                            <!-- PROFILE OPTIONS DROPDOWN / POPUP -->
                            <div 
                                x-show="profileMenuOpen" 
                                x-cloak
                                @click.away="profileMenuOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute right-0 mt-2 w-72 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xl z-50 space-y-3.5">
                                
                                <!-- User Identity Summary -->
                                <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 flex items-center justify-center text-purple-700 dark:text-purple-300 font-black text-sm flex-shrink-0">
                                        @if(auth()->user()->avatar_url)
                                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full rounded-full object-cover">
                                        @else
                                            <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->parish?->name ?? 'Livingstone Diocese' }}</p>
                                        <span class="inline-block mt-0.5 text-[9px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                                            Level {{ auth()->user()->level ?? 1 }} &bull; {{ number_format(auth()->user()->xp ?? 0) }} XP
                                        </span>
                                    </div>
                                </div>

                                <!-- 1. THEME SWITCHER -->
                                <div class="space-y-1.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Theme Mode</span>
                                    <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-900/80 rounded-xl text-xs font-bold">
                                        <button 
                                            type="button" 
                                            @click="setTheme('light')"
                                            :class="currentTheme === 'light' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                            class="py-1.5 rounded-lg flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                                            <span>☀️</span>
                                            <span>Light</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="setTheme('dark')"
                                            :class="currentTheme === 'dark' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                            class="py-1.5 rounded-lg flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                                            <span>🌙</span>
                                            <span>Dark</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="setTheme('system')"
                                            :class="currentTheme === 'system' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                            class="py-1.5 rounded-lg flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                                            <span>💻</span>
                                            <span>Auto</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- 2. PROFILE MANAGEMENT SHORTCUTS -->
                                <div class="space-y-1 pt-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Profile Management</span>
                                    
                                    <a href="/profile" @click="profileMenuOpen = false" class="w-full px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center justify-between transition-colors">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Profile &amp; Formation</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400">&rarr;</span>
                                    </a>

                                    <a href="/profile#account-security" @click="profileMenuOpen = false; if (window.location.pathname === '/profile') { document.getElementById('account-security')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); }" class="w-full px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center justify-between transition-colors">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Account Security</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400">&rarr;</span>
                                    </a>
                                </div>

                                <!-- 3. LOGOUT BUTTON -->
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <form method="POST" action="/logout" class="w-full" onsubmit="try { sessionStorage.removeItem('app_session_started'); sessionStorage.removeItem('show_login_preloader'); } catch(e) {}">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-full py-2.5 px-3 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 text-red-700 dark:text-red-300 text-xs font-bold flex items-center justify-center gap-2 transition-colors touch-press">
                                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PARISH APPROVAL NOTIFICATION BANNER -->
                @if(auth()->user()->isYouth() && auth()->user()->status === 'pending')
                    <div class="mt-2.5 p-2.5 rounded-lg bg-amber-50/90 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 flex items-start gap-2 text-xs text-amber-800 dark:text-amber-300">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="leading-tight">
                            <span class="font-bold">Parish Verification Pending:</span>
                            <span class="text-amber-700 dark:text-amber-300/80 text-[11px] block mt-0.5">Your Parish Chairperson will verify your roster membership. You have full access to study and practice.</span>
                        </div>
                    </div>
                @elseif(auth()->user()->isYouth() && auth()->user()->status === 'rejected')
                    <div class="mt-2.5 p-2.5 rounded-lg bg-red-50/90 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 flex items-start gap-2 text-xs text-red-800 dark:text-red-300">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="leading-tight">
                            <span class="font-bold">Verification Rejected:</span>
                            <span class="text-red-700 dark:text-red-300/80 text-[11px] block mt-0.5">{{ auth()->user()->rejection_reason ?? 'Please reach out to your Parish Youth Executive.' }}</span>
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
            <!-- BOTTOM NAVIGATION BAR (RICH MINIMALISM) -->
            <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-[#121826]/95 backdrop-blur-md border-t border-slate-200/90 dark:border-slate-800/90 max-w-md mx-auto px-3 pt-2 safe-bottom-nav transition-all">
                <div class="flex items-center justify-around">
                    <!-- Tab 1: Home -->
                    <a href="/" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('/') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ request()->is('/') ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-[10px] tracking-tight">Home</span>
                        @if(request()->is('/'))
                            <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                        @endif
                    </a>

                    <!-- Tab 2: Study -->
                    <a href="/study" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('study*') || request()->is('lesson*') || request()->is('flashcards*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ (request()->is('study*') || request()->is('lesson*') || request()->is('flashcards*')) ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-[10px] tracking-tight">Study</span>
                        @if(request()->is('study*') || request()->is('lesson*') || request()->is('flashcards*'))
                            <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                        @endif
                    </a>

                    <!-- Tab 3: Quiz & Arena -->
                    <a href="/quiz" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('quiz*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                        <div class="relative">
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ request()->is('quiz*') ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] tracking-tight">Quiz</span>
                        @if(request()->is('quiz*'))
                            <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                        @endif
                    </a>

                    <!-- Tab 4: Ranks & Standings -->
                    <a href="/leaderboard" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('leaderboard*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ request()->is('leaderboard*') ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-[10px] tracking-tight">Ranks</span>
                        @if(request()->is('leaderboard*'))
                            <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                        @endif
                    </a>

                    <!-- Tab 5: Role-Specific Hub / Profile -->
                    @if(auth()->user()->isChairperson())
                        <a href="/parish" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('parish*') || request()->is('approvals*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                            <div class="relative">
                                <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ (request()->is('parish*') || request()->is('approvals*')) ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
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
                            <span class="text-[10px] tracking-tight">Parish</span>
                            @if(request()->is('parish*') || request()->is('approvals*'))
                                <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                            @endif
                        </a>
                    @elseif(auth()->user()->isSuperAdmin())
                        <a href="/diocese" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('diocese*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ request()->is('diocese*') ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                            </svg>
                            <span class="text-[10px] tracking-tight">Diocese</span>
                            @if(request()->is('diocese*'))
                                <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                            @endif
                        </a>
                    @else
                        <a href="/profile" class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all touch-press {{ request()->is('profile*') ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium' }}">
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="{{ request()->is('profile*') ? '2.3' : '1.8' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-[10px] tracking-tight">Profile</span>
                            @if(request()->is('profile*'))
                                <span class="w-1 h-1 rounded-full bg-purple-600 dark:bg-purple-400 mt-0.5"></span>
                            @endif
                        </a>
                    @endif
                </div>
            </nav>
        @endauth
    </div>

    <!-- PWA OFFLINE CONNECTIVITY ALERT BANNER -->
    <div x-data="{ isOffline: !navigator.onLine, reconnected: false }"
         x-init="
             window.addEventListener('online', () => { isOffline = false; reconnected = true; setTimeout(() => reconnected = false, 3500); });
             window.addEventListener('offline', () => { isOffline = true; reconnected = false; });
         "
         x-cloak>
        <div x-show="isOffline" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed top-3 left-4 right-4 max-w-sm mx-auto z-50 px-3.5 py-2 rounded-xl bg-amber-600 text-white text-[11px] font-bold flex items-center justify-between shadow-xl border border-amber-500">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>Offline Mode &bull; Cached Formation Available</span>
            </div>
            <a href="/offline" class="underline text-[10px] uppercase tracking-wider ml-2">Details</a>
        </div>
        <div x-show="reconnected" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed top-3 left-4 right-4 max-w-sm mx-auto z-50 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-xl border border-emerald-500">
            <span>✓ Online &bull; Connection Restored</span>
        </div>
    </div>

    <!-- PWA INSTALL PROMPT COMPONENT -->
    <div x-data="{ 
             deferredPrompt: null, 
             showInstall: false,
             dismissed: localStorage.getItem('pwa_prompt_dismissed') === 'true',
             init() {
                 window.addEventListener('beforeinstallprompt', (e) => {
                     e.preventDefault();
                     this.deferredPrompt = e;
                     if (!this.dismissed && !window.matchMedia('(display-mode: standalone)').matches) {
                         setTimeout(() => { this.showInstall = true; }, 2500);
                     }
                 });
                 window.addEventListener('appinstalled', () => {
                     this.showInstall = false;
                     this.deferredPrompt = null;
                 });
             },
             install() {
                 if (this.deferredPrompt) {
                     this.deferredPrompt.prompt();
                     this.deferredPrompt.userChoice.then((choiceResult) => {
                         this.showInstall = false;
                         this.deferredPrompt = null;
                     });
                 }
             },
             dismiss() {
                 this.showInstall = false;
                 localStorage.setItem('pwa_prompt_dismissed', 'true');
             }
         }"
         x-init="init()"
         x-cloak>
        <div x-show="showInstall" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-6"
             class="fixed bottom-20 left-4 right-4 max-w-md mx-auto z-40 bg-white dark:bg-[#121826] border border-purple-200 dark:border-purple-800/80 rounded-2xl p-3.5 shadow-2xl flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">Install Catholic Youth App</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Faster loading &amp; offline catechism access</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button @click="install()" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-sm">
                    Install
                </button>
                <button @click="dismiss()" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg text-xs" title="Dismiss">
                    &times;
                </button>
            </div>
        </div>
    </div>

    <!-- PWA SERVICE WORKER REGISTRATION SCRIPT -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) {
                        console.log('PWA ServiceWorker registered with scope:', reg.scope);
                    })
                    .catch(function(err) {
                        console.warn('PWA ServiceWorker registration notice:', err);
                    });
            });
        }
    </script>

    @livewireScripts
</body>
</html>
