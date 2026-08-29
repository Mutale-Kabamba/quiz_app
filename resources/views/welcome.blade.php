<x-layouts.app>
    <div class="space-y-6">
        <!-- Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-6 text-slate-950 shadow-2xl shadow-amber-500/10">
            <div class="relative z-10">
                <span class="inline-block px-3 py-1 rounded-full bg-slate-950/20 text-slate-950 text-xs font-black uppercase tracking-wider mb-2">
                    2026 Diocesan Youth Rally
                </span>
                <h2 class="text-2xl font-black leading-tight tracking-tight">Faith Formation &amp; Catechism Competition</h2>
                <p class="mt-2 text-sm font-semibold text-slate-900 leading-snug">
                    Master Scripture, YOUCAT, DOCAT, and Church Traditions. Climb the Livingstone Diocesan Leaderboard!
                </p>
                <div class="mt-4 flex items-center gap-3">
                    <a href="/quiz/1" class="px-5 py-2.5 rounded-2xl bg-slate-950 text-amber-400 font-extrabold text-sm shadow-xl hover:bg-slate-900 transition-transform active:scale-95">
                        Start Quick Quiz
                    </a>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-36 h-36 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Categories & Study Tracks -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-extrabold text-white">Study Tracks &amp; Quiz Categories</h3>
                <span class="text-xs text-amber-400 font-semibold">5 Tracks Active</span>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @php
                    $categories = \App\Models\Category::withCount('questions')->orderBy('display_order')->get();
                @endphp

                @foreach($categories as $category)
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/50 transition-all group flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l-10-5v9l10 5 10-5V6l-10 5z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">{{ $category->name }}</h4>
                                <p class="text-xs text-slate-400 line-clamp-1">{{ $category->description }}</p>
                                <span class="text-[11px] font-semibold text-slate-500">{{ $category->questions_count }} Questions loaded</span>
                            </div>
                        </div>
                        <a href="/quiz/{{ $category->id }}" class="px-3.5 py-2 rounded-xl bg-amber-500/10 text-amber-400 hover:bg-amber-500 hover:text-slate-950 font-bold text-xs transition-all">
                            Play
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
