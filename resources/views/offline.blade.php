<!DOCTYPE html>
<html lang="en" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <meta name="theme-color" content="#5B21B6">
    <title>Offline • Livingstone Diocese Catholic Youth Ministry</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;0,6..72,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        serif: ['"Newsreader"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans bg-[#F8FAFC] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 flex flex-col justify-center items-center p-6">
    <div class="max-w-sm w-full text-center space-y-6">
        
        <!-- Catholic Diocesan Icon with Wifi Disconnected Badge -->
        <div class="relative w-20 h-20 mx-auto rounded-3xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 flex items-center justify-center shadow-sm">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
            <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center ring-4 ring-[#F8FAFC] dark:ring-[#0B0F19]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m-2.828-2.828a5 5 0 000-7.072M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300">
                Livingstone Diocese &bull; Catholic Youth Ministry
            </span>
            <h1 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">You are currently offline</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs mx-auto">
                No active internet connection was detected. Some online features like live quiz rankings, inter-parish challenges, and profile synchronizations require network access.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-left space-y-2 text-xs">
            <div class="flex items-center gap-2 font-bold text-slate-800 dark:text-slate-200">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Offline Spiritual Formation</span>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                You can still read downloaded doctrinal summaries and review cached formation notes once your connection stabilizes.
            </p>
        </div>

        <div class="space-y-3 pt-2">
            <button 
                onclick="window.location.reload()" 
                class="w-full py-3 bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-white font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Retry Connection</span>
            </button>
            <a href="/" class="inline-block text-xs font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                Return to Application Home &rarr;
            </a>
        </div>
    </div>
</body>
</html>
