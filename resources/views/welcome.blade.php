<x-layouts.app>
    <div class="space-y-5">
        <!-- Banner -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 space-y-3">
            <span class="inline-block px-2.5 py-0.5 rounded bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase tracking-wider border border-purple-200 dark:border-purple-800">
                Faith Formation &amp; Catechetical Library
            </span>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Livingstone Diocese Catholic Youth Platform</h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                Master Holy Scripture, YOUCAT, DOCAT, African Church History, and Liturgical Traditions.
            </p>
            <div class="pt-1">
                <a href="/quiz" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-lg transition-colors touch-press">
                    <span>Enter Quiz Arena</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Categories & Study Tracks -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Formation Tracks</h3>
                <span class="text-xs text-purple-600 dark:text-purple-400 font-semibold">Active Curriculum</span>
            </div>

            <div class="space-y-2">
                @php
                    $categories = \App\Models\Category::withCount('questions')->orderBy('display_order')->get();
                @endphp

                @foreach($categories as $category)
                    <div class="p-3.5 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-colors flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $category->name }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $category->description }}</p>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $category->questions_count }} Questions loaded</span>
                            </div>
                        </div>
                        <a href="/quiz/play/{{ $category->id }}?mode=practice" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-purple-600 hover:text-white text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors">
                            Study
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
