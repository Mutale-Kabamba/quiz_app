<div class="space-y-6 pb-12">

    <!-- LESSON TOP NAVIGATION BAR -->
    <div class="flex items-center justify-between pt-1">
        <a href="/study" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Study Library</span>
        </a>

        <div class="flex items-center gap-2">
            <!-- Bookmark Button -->
            <button 
                type="button" 
                wire:click="toggleBookmark" 
                class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 transition-colors {{ $isBookmarked ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 border-amber-300 dark:border-amber-700' : 'bg-white dark:bg-[#121826] text-slate-400 hover:text-slate-600' }}"
                title="Bookmark Lesson">
                <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </button>

            <!-- Category Tag -->
            <span class="px-2.5 py-1 rounded-md bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase tracking-wider border border-purple-200 dark:border-purple-800">
                {{ $lesson->category?->name }}
            </span>
        </div>
    </div>

    <!-- LESSON TITLE & METADATA -->
    <div class="space-y-2 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span>{{ $lesson->estimated_read_minutes }} min read</span>
            <span>&bull;</span>
            <span>Level {{ $lesson->difficulty }}</span>
            @if($lesson->scripture_citations)
                <span>&bull;</span>
                <span class="text-purple-600 dark:text-purple-400 font-semibold truncate max-w-[140px]">{{ $lesson->scripture_citations }}</span>
            @endif
        </div>

        <h1 class="text-xl font-bold text-slate-900 dark:text-white leading-snug">
            {{ $lesson->title }}
        </h1>
        
        @if($lesson->subheading)
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                {{ $lesson->subheading }}
            </p>
        @endif
    </div>

    <!-- 1. KEY TAKEAWAYS (Clean Callout) -->
    @if(!empty($lesson->summary_takeaways))
        <div class="p-4 rounded-xl bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/50 space-y-2">
            <div class="flex items-center gap-2 text-purple-800 dark:text-purple-300 font-bold text-xs">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Key Catechetical Takeaways</span>
            </div>

            <ul class="space-y-1.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                @foreach($lesson->summary_takeaways as $takeaway)
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600 font-bold mt-0.5">&bull;</span>
                        <span>{{ $takeaway }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. STRUCTURED READING CONTENT (DISTRACTION-FREE) -->
    @if(!empty($lesson->content_sections))
        <div class="space-y-6">
            @foreach($lesson->content_sections as $section)
                <div class="space-y-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                        {{ $section['heading'] }}
                    </h3>

                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                        {{ $section['body'] }}
                    </p>

                    <!-- Scripture Citation Callout -->
                    @if(!empty($section['scripture_quote']))
                        <div class="p-3 bg-white dark:bg-[#121826] rounded-xl border-l-4 border-purple-600 border border-slate-200 dark:border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 tracking-wider block">Holy Scripture</span>
                            <p class="text-xs text-slate-700 dark:text-slate-300 italic leading-relaxed">{{ $section['scripture_quote'] }}</p>
                        </div>
                    @endif

                    <!-- Catechism Citation Callout -->
                    @if(!empty($section['catechism_quote']))
                        <div class="p-3 bg-white dark:bg-[#121826] rounded-xl border-l-4 border-amber-600 border border-slate-200 dark:border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold uppercase text-amber-700 dark:text-amber-400 tracking-wider block">Catechism of the Catholic Church</span>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $section['catechism_quote'] }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- 3. KEY TERMS & VOCABULARY -->
    @if(!empty($lesson->key_terms))
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Key Doctrinal Terms
            </h3>

            <div class="space-y-2">
                @foreach($lesson->key_terms as $item)
                    <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                        <h4 class="text-xs font-bold text-purple-700 dark:text-purple-400">{{ $item['term'] }}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">{{ $item['definition'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. LESSON COMPLETION CARD -->
    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 space-y-4 text-center">
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Lesson Review &amp; Progress</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mark this lesson as completed to record your formation progress (+20 XP).</p>
        </div>

        @if($isCompleted)
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 font-semibold flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Lesson Completed</span>
            </div>
        @else
            <button 
                type="button" 
                wire:click="markAsCompleted" 
                class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl text-xs transition-colors touch-press">
                Mark Lesson as Completed (+20 XP)
            </button>
        @endif

        <!-- NEXT ACTIONS -->
        <div class="grid grid-cols-2 gap-2 text-xs">
            <a href="/flashcards/{{ $lesson->category_id }}" class="p-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium transition-colors">
                Flashcard Drill
            </a>
            <a href="/quiz/play/{{ $lesson->category_id }}?mode=practice" class="p-2.5 rounded-lg bg-purple-50 dark:bg-purple-950/30 hover:bg-purple-100 text-purple-700 dark:text-purple-300 font-semibold border border-purple-200 dark:border-purple-800 transition-colors">
                Practice Quiz
            </a>
        </div>

        @if($nextLesson)
            <a href="/lesson/{{ $nextLesson->id }}" class="block w-full py-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors">
                Next: {{ $nextLesson->title }} &rarr;
            </a>
        @endif
    </div>

    <!-- COMPLETION CELEBRATION MODAL -->
    @if($showCompletionCelebration)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-xs w-full text-center space-y-4 shadow-xl">
                <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Lesson Completed</h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-1">+{{ $xpEarned }} XP Earned &bull; Formation Streak Advanced</p>
                </div>

                <div class="space-y-2">
                    <a href="/flashcards/{{ $lesson->category_id }}" class="block w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs transition-colors">
                        Review Lesson Flashcards &rarr;
                    </a>
                    <button 
                        type="button"
                        wire:click="$set('showCompletionCelebration', false)" 
                        class="block w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-medium rounded-lg text-xs transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
