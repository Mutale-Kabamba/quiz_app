<x-layouts.app>
    <div class="space-y-6">
        <!-- Hero Editorial Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 text-white p-6 space-y-4 border border-purple-800/40 shadow-sm">
            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-purple-200 text-[10px] font-bold uppercase tracking-wider border border-white/15">
                    Livingstone Diocese Catholic Youth Formation
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold font-serif text-white leading-tight">
                    Grow in Faith, Scripture &amp; Catechism
                </h1>
                <p class="text-xs text-purple-200/80 leading-relaxed max-w-sm">
                    Interactive catechetical learning for Catholic youth across all parishes of Livingstone Diocese.
                </p>
            </div>

            <div class="pt-2 flex flex-wrap gap-2.5">
                <a href="/login" class="px-4 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-bold text-xs rounded-xl transition-colors touch-press shadow-sm">
                    Sign In to Form
                </a>
                <a href="/register" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition-colors touch-press">
                    Register Parish Youth
                </a>
            </div>
        </div>

        <!-- Categories & Study Tracks -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Formation Tracks</h3>
                    <p class="text-[11px] text-slate-500">Universal Catholic curriculum catalog</p>
                </div>
                <span class="text-xs text-purple-600 dark:text-purple-400 font-bold">5 Tracks</span>
            </div>

            <div class="space-y-2.5">
                @php
                    $categories = \App\Models\Category::withCount('questions')->orderBy('display_order')->get();
                @endphp

                @foreach($categories as $category)
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-300 dark:hover:border-purple-800 transition-colors flex items-center justify-between group shadow-sm">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center flex-shrink-0 border border-purple-200/60 dark:border-purple-800/60">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $category->name }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $category->description }}</p>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $category->questions_count }} Questions loaded</span>
                            </div>
                        </div>
                        <a href="/quiz" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-purple-600 hover:text-white text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors flex-shrink-0">
                            Study
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
