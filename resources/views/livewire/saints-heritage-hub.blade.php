<div class="space-y-4 pb-16">
    <!-- IN-PAGE TOP NAVIGATION HEADER -->
    <div class="flex items-center justify-between gap-3 pb-1">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('home') }}" class="p-2 rounded-xl bg-white dark:bg-[#121826] hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 transition-colors touch-press flex-shrink-0 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold font-serif text-slate-900 dark:text-white leading-tight truncate">Saints &amp; African Heritage</h1>
                <p class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold truncate">{{ $saints->count() }} Patrons &bull; {{ $this->activeCategoryTitle }}</p>
            </div>
        </div>

        <a href="/study" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 flex-shrink-0">
            <span>Study Hub</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- HERO SHOWCASE CARD -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-950 text-white p-5 border border-purple-800/40 shadow-lg">
        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none text-white">
            <svg class="w-64 h-64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>

        <div class="relative z-10 space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-purple-200 text-[10px] font-bold uppercase tracking-wider border border-white/20">
                    Livingstone Diocesan Heritage
                </span>
                <span class="text-xs text-purple-300 font-semibold">{{ $africanCount }} African Witnesses</span>
            </div>

            <div>
                <h2 class="text-xl font-bold font-serif text-white leading-tight">
                    Witnesses of Faith, Courage &amp; Holiness
                </h2>
                <p class="text-xs text-purple-200/90 leading-relaxed mt-1.5">
                    Discover the holy men and women who shaped Catholic history—from the North African Church Fathers and Uganda Martyrs to our Cathedral Patroness St. Thérèse of Lisieux.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-white/10 text-xs">
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Cathedral Patroness</span>
                    <span class="font-bold text-white text-xs block mt-0.5">St. Theresa of Lisieux</span>
                </div>
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Youth Patrons</span>
                    <span class="font-bold text-white text-xs block mt-0.5">Uganda Martyrs (Namugongo)</span>
                </div>
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Doctor of Grace</span>
                    <span class="font-bold text-white text-xs block mt-0.5">St. Augustine of Hippo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH AND FILTER CONTROLS -->
    <div class="space-y-3">
        <!-- Search Bar -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search saints by name, country, virtue, or title..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-xs">
            @if(!empty($search))
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                    &times;
                </button>
            @endif
        </div>

        <!-- Category Filter Tabs (No Ugly Scrollbar with hide-scrollbar) -->
        <div class="flex items-center gap-1.5 overflow-x-auto hide-scrollbar -mx-4 px-4 pb-1 text-xs">
            <button type="button" wire:click="setFilter('all')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'all' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                All Saints ({{ $counts['all'] ?? $totalCount }})
            </button>
            <button type="button" wire:click="setFilter('african')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'african' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                🌍 African Heritage ({{ $counts['african'] ?? $africanCount }})
            </button>
            <button type="button" wire:click="setFilter('youth')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'youth' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                🔥 Youth Patrons ({{ $counts['youth'] ?? 0 }})
            </button>
            <button type="button" wire:click="setFilter('martyrs')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'martyrs' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                🛡️ Martyrs of Faith ({{ $counts['martyrs'] ?? 0 }})
            </button>
            <button type="button" wire:click="setFilter('doctors')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'doctors' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                📜 Doctors of Church ({{ $counts['doctors'] ?? 0 }})
            </button>
            <button type="button" wire:click="setFilter('fathers')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all whitespace-nowrap touch-press cursor-pointer {{ $activeFilter === 'fathers' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                🗝️ African Popes &amp; Fathers ({{ $counts['fathers'] ?? 0 }})
            </button>
        </div>
    </div>

    <!-- SAINTS DIRECTORY CARDS (CLEAN 1-COLUMN MOBILE CARDS) -->
    @if($saints->isNotEmpty())
        <div class="space-y-4">
            @foreach($saints as $saint)
                <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-xs hover:border-purple-300 dark:hover:border-purple-800/80 transition-all">
                    
                    <!-- Badges Row (Wraps cleanly without overflow) -->
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                            {{ $saint->slug === 'st-theresa-of-lisieux' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : ($saint->is_african_heritage ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800') }}">
                            {{ $saint->slug === 'st-theresa-of-lisieux' ? 'Diocesan Cathedral Patroness' : ($saint->is_african_heritage ? 'African Catholic Heritage' : 'Universal Doctor') }}
                        </span>

                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Feast: {{ $saint->feast_day_month_day ? \Carbon\Carbon::createFromFormat('m-d', $saint->feast_day_month_day)->format('M j') : 'Oct 1' }}</span>
                        </span>
                    </div>

                    <!-- Title & Designation -->
                    <div>
                        <h3 class="text-lg font-bold font-serif text-slate-900 dark:text-white leading-snug">
                            {{ $saint->name }}
                        </h3>
                        <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 mt-0.5">
                            {{ $saint->title_designation }} &bull; {{ $saint->country_region }}
                        </p>
                    </div>

                    <!-- Short Bio excerpt -->
                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-3 leading-relaxed">
                        {{ $saint->biography }}
                    </p>

                    <!-- Virtues Tags -->
                    @if(!empty($saint->virtues_exemplified))
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach(array_slice($saint->virtues_exemplified, 0, 3) as $virtue)
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 text-[10px] font-medium border border-slate-200/60 dark:border-slate-700/60">
                                    {{ $virtue }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Action Footer -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400">
                            {{ $saint->birth_year && $saint->death_year ? "{$saint->birth_year} – {$saint->death_year} AD" : "Universal Witness" }}
                        </span>

                        <a href="{{ route('saints.show', $saint->slug) }}"
                           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all touch-press flex items-center gap-1.5 shadow-xs">
                            <span>Learn &amp; Pray</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- EMPTY STATE -->
        <div class="p-8 text-center bg-white dark:bg-[#121826] rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 mx-auto flex items-center justify-center text-xl">
                🔍
            </div>
            <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">No Saints Found</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                We could not find any saints matching "{{ $search }}". Try searching for "Uganda", "Augustine", "Theresa", or clear your filter.
            </p>
            <button type="button" wire:click="$set('search', ''); $set('activeFilter', 'all')"
                    class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold hover:bg-purple-700 transition-colors cursor-pointer">
                Reset Search &amp; Filters
            </button>
        </div>
    @endif
</div>
