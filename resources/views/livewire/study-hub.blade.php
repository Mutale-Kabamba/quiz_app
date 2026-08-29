<div class="space-y-5 pb-6">

    <!-- STUDY HUB HEADER -->
    <div class="pt-1">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Study Library</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Catholic Scripture, Catechism, Saints, and African Heritage</p>
    </div>

    <!-- SEARCH INPUT -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Search doctrine, scripture, catechism..."
            class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors">
    </div>

    <!-- SPACED REPETITION FLASHCARDS CARD -->
    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Flashcard Drill</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $flashcardStats['due_today'] > 0 ? "{$flashcardStats['due_today']} flashcards ready for review" : 'Review key Catholic doctrinal terms' }}
                </p>
            </div>
        </div>
        <a href="/flashcards{{ $selectedCategoryId ? '/' . $selectedCategoryId : '' }}" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs transition-colors">
            Review
        </a>
    </div>

    <!-- CATEGORY FILTER PILLS -->
    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-0.5">
        <button 
            type="button"
            wire:click="selectCategory(null)"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ is_null($selectedCategoryId) ? 'bg-purple-600 text-white' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
            All Tracks
        </button>
        @foreach($categories as $cat)
            <button 
                type="button"
                wire:click="selectCategory({{ $cat->id }})"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors {{ $selectedCategoryId === $cat->id ? 'bg-purple-600 text-white' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    <!-- LESSONS LIST -->
    <div class="space-y-3">
        @forelse($lessons as $lesson)
            @php
                $userProgress = $lesson->progress->first();
                $isCompleted = $userProgress?->is_completed ?? false;
                $isBookmarked = $userProgress?->is_bookmarked ?? false;
            @endphp

            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3 hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1 pr-2">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/30 px-2 py-0.5 rounded">
                                {{ $lesson->category?->name }}
                            </span>
                            <span class="text-[11px] text-slate-400">&bull; {{ $lesson->estimated_read_minutes }} min</span>
                            
                            @if($isCompleted)
                                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Completed
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                            {{ $lesson->title }}
                        </h3>
                        @if($lesson->subheading)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $lesson->subheading }}
                            </p>
                        @endif
                    </div>

                    @if($isBookmarked)
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                        </svg>
                    @endif
                </div>

                <!-- CITATIONS & ACTIONS BAR -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px] truncate max-w-[150px]">
                        {{ $lesson->scripture_citations ?? $lesson->catechism_citations ?? 'Livingstone Diocese' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="/flashcards/{{ $lesson->category_id }}" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs transition-colors">
                            Cards
                        </a>
                        <a href="/lesson/{{ $lesson->id }}" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs transition-colors touch-press flex items-center gap-1">
                            <span>{{ $isCompleted ? 'Review' : 'Read' }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-2">
                <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xs font-bold text-slate-900 dark:text-white">No Lessons Found</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Try searching for other catechetical terms or choosing another study track.</p>
            </div>
        @endforelse
    </div>
</div>
