<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ config('app.name', 'Livingstone Diocese Youth Quiz') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN for rapid preview) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            gold: '#F59E0B',
                            navy: '#0F172A',
                            crimson: '#991B1B',
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 flex flex-col justify-between selection:bg-amber-500 selection:text-slate-950">
    <!-- Diocesan Header -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/80 border-b border-slate-800/80 px-4 py-3">
        <div class="max-w-xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-amber-500/20">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H4v2h7v9h2v-9h7v-2h-7V2z"/></svg>
                </a>
                <div>
                    <h1 class="text-sm font-extrabold text-white leading-tight">Livingstone Diocese</h1>
                    <p class="text-[11px] font-semibold text-amber-400">Catholic Youth Ministry Quiz</p>
                </div>
            </div>
            <a href="/admin" class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700">
                Admin Panel
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-xl w-full mx-auto p-4">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-900">
        <p>Catholic Diocese of Livingstone &bull; Youth Pastoral Commission</p>
    </footer>

    @livewireScripts
</body>
</html>
