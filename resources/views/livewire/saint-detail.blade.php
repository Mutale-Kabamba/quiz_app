<div class="space-y-4 pb-16">
    <!-- IN-PAGE TOP NAVIGATION -->
    <div class="flex items-center justify-between gap-3 pb-1">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('saints.index') }}" class="p-2 rounded-xl bg-white dark:bg-[#121826] hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 transition-colors touch-press flex-shrink-0 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold font-serif text-slate-900 dark:text-white leading-tight truncate max-w-[220px]">
                    {{ $saint->name }}
                </h1>
                <p class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold truncate">{{ $saint->title_designation }}</p>
            </div>
        </div>

        <a href="{{ route('saints.index') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 flex-shrink-0">
            <span>All Saints</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- HERO PROFILE CARD -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-950 text-white p-5 sm:p-6 border border-purple-800/40 shadow-xl space-y-3.5">
        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none text-white">
            <svg class="w-64 h-64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
            </svg>
        </div>

        <div class="relative z-10 space-y-3">
            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    {{ $saint->slug === 'st-theresa-of-lisieux' ? 'bg-amber-400/20 text-amber-300 border border-amber-300/30' : ($saint->is_african_heritage ? 'bg-rose-500/20 text-rose-300 border border-rose-400/30' : 'bg-purple-400/20 text-purple-200 border border-purple-300/30') }}">
                    {{ $saint->slug === 'st-theresa-of-lisieux' ? 'Diocesan Cathedral Patroness' : ($saint->is_african_heritage ? 'African Catholic Heritage' : 'Universal Doctor') }}
                </span>

                <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 text-[10px] font-semibold border border-white/15">
                    {{ $saint->country_region }}
                </span>

                @if($saint->birth_year && $saint->death_year)
                    <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 text-[10px] font-semibold border border-white/15">
                        {{ $saint->birth_year }} – {{ $saint->death_year }} AD
                    </span>
                @endif
            </div>

            <!-- Saint Title -->
            <div>
                <h2 class="text-2xl font-bold font-serif text-white leading-tight">
                    {{ $saint->name }}
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-purple-300 mt-0.5">
                    {{ $saint->title_designation }}
                </p>
            </div>

            <!-- Key Attributes Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-3 border-t border-white/10 text-xs">
                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Feast Day</span>
                    <span class="font-bold text-white text-xs sm:text-sm block mt-0.5">
                        {{ $saint->feast_day_month_day ? \Carbon\Carbon::createFromFormat('m-d', $saint->feast_day_month_day)->format('F j') : 'October 1' }}
                    </span>
                </div>

                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Origin</span>
                    <span class="font-bold text-white text-xs sm:text-sm block mt-0.5 truncate">
                        {{ $saint->country_region }}
                    </span>
                </div>

                <div class="p-2.5 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[10px] text-purple-300 uppercase block font-semibold">Patronage</span>
                    <span class="font-bold text-white text-xs sm:text-sm block mt-0.5 truncate">
                        {{ !empty($saint->patronages) ? implode(', ', array_slice($saint->patronages, 0, 2)) : 'Universal Church' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- BIOGRAPHY & LIFE WITNESS -->
    <section class="p-5 bg-white dark:bg-[#121826] rounded-3xl border border-slate-200 dark:border-slate-800 space-y-3 shadow-xs">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-2.5">
            <span class="p-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </span>
            <div>
                <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">Life &amp; Sacred Witness</h3>
                <p class="text-[11px] text-slate-500">Historical biography and Catholic witness</p>
            </div>
        </div>

        <div class="text-slate-700 dark:text-slate-300 font-serif leading-relaxed text-sm space-y-2.5">
            <p>{{ $saint->biography }}</p>
        </div>
    </section>

    <!-- VIRTUES & SPIRITUAL GIFTS -->
    @if(!empty($saint->virtues_exemplified))
        <section class="p-5 bg-white dark:bg-[#121826] rounded-3xl border border-slate-200 dark:border-slate-800 space-y-2.5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Virtues Exemplified</span>
                <span class="text-[11px] text-slate-400">Christian character</span>
            </div>

            <div class="flex flex-wrap gap-1.5 pt-1">
                @foreach($saint->virtues_exemplified as $virtue)
                    <div class="px-3 py-1 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60 font-semibold text-xs flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                        <span>{{ $virtue }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- KEY TEACHINGS & QUOTES -->
    @if(!empty($saint->key_teachings_quotes))
        <section class="p-5 bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl border border-purple-800/40 space-y-3.5 shadow-md">
            <div class="flex items-center gap-2">
                <span class="text-lg">💬</span>
                <div>
                    <h3 class="font-serif font-bold text-base text-white">Words of Wisdom &amp; Teachings</h3>
                    <p class="text-[11px] text-purple-300">Spiritual guidance for daily Christian living</p>
                </div>
            </div>

            <div class="space-y-2.5">
                @foreach($saint->key_teachings_quotes as $quote)
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <blockquote class="font-serif italic text-xs sm:text-sm text-purple-50 leading-relaxed">
                            &ldquo;{{ $quote }}&rdquo;
                        </blockquote>
                        <span class="text-[10px] text-purple-300/80 block text-right font-medium">&mdash; {{ $saint->name }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- PATRONAGE & OFFICIAL INTERCESSORY PRAYER -->
    @if(!empty($saint->patronage_prayer))
        <section x-data="{ copied: false }" class="p-5 bg-white dark:bg-[#121826] rounded-3xl border-2 border-purple-500/40 dark:border-purple-600/40 space-y-3.5 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold text-base">
                        🙏
                    </span>
                    <div>
                        <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">Intercessory Prayer</h3>
                        <p class="text-[11px] text-slate-500">Prayer for the intercession of {{ $saint->name }}</p>
                    </div>
                </div>

                <button type="button"
                        @click="navigator.clipboard.writeText('{{ addslashes($saint->patronage_prayer) }}'); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/60 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 text-xs font-bold transition-all touch-press flex items-center gap-1 cursor-pointer flex-shrink-0">
                    <span x-text="copied ? '✓ Copied' : 'Copy Prayer'"></span>
                </button>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#0d121f] border border-slate-200 dark:border-slate-800">
                <p class="font-serif italic text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
                    {{ $saint->patronage_prayer }}
                </p>
            </div>

            @if(!empty($saint->patronages))
                <div class="pt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Patron Saint of:</span>
                    <span>{{ implode(' • ', $saint->patronages) }}</span>
                </div>
            @endif
        </section>
    @endif

    <!-- EXPLORE MORE SAINTS RAIL -->
    @if($otherSaints->isNotEmpty())
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="font-serif font-bold text-base text-slate-900 dark:text-white">More Holy Patrons</h3>
                <a href="{{ route('saints.index') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-2.5">
                @foreach($otherSaints as $other)
                    <a href="{{ route('saints.show', $other->slug) }}"
                       class="p-3.5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-400 transition-all touch-press space-y-1 block shadow-2xs">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 block">
                            {{ $other->is_african_heritage ? 'African Heritage' : 'Universal Doctor' }}
                        </span>
                        <h4 class="font-serif font-bold text-sm text-slate-900 dark:text-white line-clamp-1">{{ $other->name }}</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-1">{{ $other->title_designation }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
