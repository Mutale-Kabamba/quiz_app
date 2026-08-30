<div class="space-y-6 pb-12">

    <!-- LESSON TOP NAVIGATION BAR -->
    <div class="flex items-center justify-between pt-1">
        <a href="/study" class="flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors touch-press">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Study Library</span>
        </a>

        <div class="flex items-center gap-2">
            <!-- Bookmark Button -->
            <button 
                type="button" 
                wire:click="toggleBookmark" 
                class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 transition-colors {{ $isBookmarked ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 border-amber-300 dark:border-amber-700' : 'bg-white dark:bg-[#121826] text-slate-400 hover:text-slate-600' }}"
                title="Bookmark Lesson">
                <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </button>

            <!-- Category Tag -->
            <span class="px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase tracking-wider border border-purple-200 dark:border-purple-800">
                {{ $lesson->category?->name }}
            </span>
        </div>
    </div>

    <!-- FORMATION SERIES PROGRESS TRACKER (If part of a series) -->
    @if($lesson instanceof \App\Models\Lesson && $lesson->isPartOfSeries() && $seriesLessons->isNotEmpty())
        <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-900/10 via-indigo-900/10 to-purple-900/10 dark:from-purple-950/40 dark:via-indigo-950/30 dark:to-purple-950/40 border border-purple-200 dark:border-purple-800/80 space-y-2.5 shadow-sm">
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 font-bold text-purple-900 dark:text-purple-300">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span>Series: {{ $lesson->series_title ?: ucwords(str_replace('-', ' ', $lesson->series_identifier)) }}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-200 text-[10px] font-bold">
                    Part {{ $lesson->series_order ?? 1 }} of {{ $seriesLessons->count() }}
                </span>
            </div>

            <!-- Series Step Breadcrumbs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                @foreach($seriesLessons as $sLesson)
                    @php
                        $isCurrent = ($sLesson->id === $lesson->id);
                        $isDone = in_array($sLesson->id, $completedSeriesLessonIds);
                    @endphp
                    <a href="/lesson/{{ $sLesson->id }}" 
                       class="px-2.5 py-1 rounded-lg text-[10px] font-bold shrink-0 transition-all flex items-center gap-1
                              {{ $isCurrent ? 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-400/50' : ($isDone ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400') }}">
                        @if($isDone)
                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                        <span>Part {{ $sLesson->series_order ?? $loop->iteration }}</span>
                    </a>
                @endforeach
            </div>

            @if(!$prerequisitesMet)
                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl text-[11px] text-amber-800 dark:text-amber-300 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>For progressive formation, we recommend completing earlier parts first.</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- LESSON TITLE & METADATA -->
    <div class="space-y-2 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span>{{ $lesson->estimated_read_minutes }} min read</span>
            <span>&bull;</span>
            <span>Level {{ $lesson->difficulty }}</span>
            @if($lesson->isPartOfSeries())
                <span>&bull;</span>
                <span class="text-purple-600 dark:text-purple-400 font-bold">Part {{ $lesson->series_order ?? 1 }}</span>
            @endif
            @if($lesson->scripture_citations)
                <span>&bull;</span>
                <span class="text-purple-600 dark:text-purple-400 font-bold truncate max-w-[140px]">{{ $lesson->scripture_citations }}</span>
            @endif
        </div>

        <h1 class="text-2xl font-bold font-serif text-slate-900 dark:text-white leading-snug">
            {{ $lesson->title }}
        </h1>
        
        @if($lesson->subheading)
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                {{ $lesson->subheading }}
            </p>
        @endif
    </div>

    <!-- 1. KEY TAKEAWAYS (Clean Editorial Callout) -->
    @if(!empty($lesson->summary_takeaways))
        <div class="p-5 rounded-2xl bg-purple-50/90 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/60 space-y-2.5 shadow-sm">
            <div class="flex items-center gap-2 text-purple-900 dark:text-purple-200 font-bold text-xs">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Key Catechetical Takeaways</span>
            </div>

            <ul class="space-y-1.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                @foreach((array) $lesson->summary_takeaways as $takeaway)
                    @php
                        $takeawayText = is_array($takeaway) ? ($takeaway['text'] ?? $takeaway['point'] ?? reset($takeaway)) : (string) $takeaway;
                    @endphp
                    @if(!empty(trim($takeawayText)))
                        <li class="flex items-start gap-2">
                            <span class="text-purple-600 font-bold mt-0.5">&bull;</span>
                            <span>{{ trim($takeawayText) }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 2. STRUCTURED READING CONTENT -->
    @if(!empty($lesson->content_sections))
        <div class="space-y-6">
            @foreach((array) $lesson->content_sections as $section)
                @php
                    $heading = is_array($section) ? ($section['heading'] ?? $section['title'] ?? null) : null;
                    $body = is_array($section) ? ($section['body'] ?? $section['content'] ?? $section['text'] ?? $section['paragraph'] ?? '') : (string) $section;
                    $scriptureQuote = is_array($section) ? ($section['scripture_quote'] ?? $section['scripture'] ?? null) : null;
                    $catechismQuote = is_array($section) ? ($section['catechism_quote'] ?? $section['catechism'] ?? $section['ccc'] ?? null) : null;
                @endphp
                <div class="space-y-3">
                    @if(!empty($heading))
                        <h3 class="text-lg font-bold font-serif text-slate-900 dark:text-white leading-snug">
                            {{ $heading }}
                        </h3>
                    @endif

                    @if(!empty($body))
                        <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $body }}
                        </p>
                    @endif

                    <!-- Scripture Citation Callout -->
                    @if(!empty($scriptureQuote))
                        <div class="p-4 bg-white dark:bg-[#121826] rounded-2xl border-l-4 border-purple-600 border border-slate-200 dark:border-slate-800 space-y-1 shadow-sm">
                            <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 tracking-wider block">Holy Scripture</span>
                            <p class="text-xs sm:text-sm font-serif text-slate-800 dark:text-slate-200 italic leading-relaxed">{{ $scriptureQuote }}</p>
                        </div>
                    @endif

                    <!-- Catechism Citation Callout -->
                    @if(!empty($catechismQuote))
                        <div class="p-4 bg-white dark:bg-[#121826] rounded-2xl border-l-4 border-amber-600 border border-slate-200 dark:border-slate-800 space-y-1 shadow-sm">
                            <span class="text-[10px] font-bold uppercase text-amber-700 dark:text-amber-400 tracking-wider block">Catechism of the Catholic Church</span>
                            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $catechismQuote }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif(!empty($lesson->content_body ?? $lesson->content ?? $lesson->body))
        <div class="p-5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 shadow-sm">
            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                {{ $lesson->content_body ?? $lesson->content ?? $lesson->body }}
            </p>
        </div>
    @endif

    <!-- 3. KEY DOCTRINAL TERMS -->
    @if(!empty($lesson->key_terms))
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-3.5 shadow-sm">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Key Doctrinal Terms
            </h3>

            <div class="space-y-2">
                @foreach((array) $lesson->key_terms as $key => $item)
                    @php
                        $term = '';
                        $definition = '';
                        if (is_array($item)) {
                            $term = $item['term'] ?? $item['name'] ?? $item['title'] ?? (is_string($key) ? $key : '');
                            $definition = $item['definition'] ?? $item['meaning'] ?? $item['desc'] ?? $item['description'] ?? '';
                        } elseif (is_string($item)) {
                            if (is_string($key) && !is_numeric($key)) {
                                $term = $key;
                                $definition = $item;
                            } elseif (str_contains($item, ':')) {
                                [$term, $definition] = explode(':', $item, 2);
                            } elseif (str_contains($item, ' - ')) {
                                [$term, $definition] = explode(' - ', $item, 2);
                            } else {
                                $term = $item;
                            }
                        }
                    @endphp
                    @if(!empty($term) || !empty($definition))
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            @if(!empty($term))
                                <h4 class="text-xs font-bold text-purple-700 dark:text-purple-400">{{ trim($term) }}</h4>
                            @endif
                            @if(!empty($definition))
                                <p class="text-xs text-slate-600 dark:text-slate-300 {{ !empty($term) ? 'mt-0.5' : '' }} leading-relaxed">{{ trim($definition) }}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. LESSON COMPLETION CARD -->
    <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 text-center shadow-sm">
        <div>
            <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white">Lesson Review &amp; Progress</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mark completed to record your diocesan formation progress (+20 XP).</p>
        </div>

        @if($isCompleted)
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 font-bold flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Lesson Completed</span>
            </div>
        @else
            <button 
                type="button" 
                wire:click="markAsCompleted" 
                class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl text-xs transition-colors touch-press shadow-sm">
                Mark Lesson as Completed (+20 XP)
            </button>
        @endif

        <!-- NEXT ACTIONS -->
        <div class="grid grid-cols-2 gap-2 text-xs">
            <a href="/flashcards/{{ $lesson->category_id }}" class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold transition-colors">
                Flashcard Drill
            </a>
            <a href="/quiz/play/{{ $lesson->category_id }}?mode=practice" class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 text-purple-700 dark:text-purple-300 font-bold border border-purple-200 dark:border-purple-800 transition-colors">
                Practice Quiz
            </a>
        </div>

        @if($nextLesson)
            <a href="/lesson/{{ $nextLesson->id }}" class="block w-full py-3.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-colors shadow-sm text-center">
                @if($lesson instanceof \App\Models\Lesson && $lesson->isPartOfSeries() && $nextLesson->series_identifier === $lesson->series_identifier)
                    Continue Series &bull; Part {{ $nextLesson->series_order ?? 'Next' }}: {{ $nextLesson->title }} &rarr;
                @else
                    Continue to Next Lesson &bull; {{ $nextLesson->title }} &rarr;
                @endif
            </a>
        @endif
    </div>

    <!-- COMPLETION CELEBRATION MODAL -->
    @if($showCompletionCelebration)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-xs w-full text-center space-y-4 shadow-2xl">
                <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white">Lesson Completed</h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">+{{ $xpEarned }} XP Earned &bull; Formation Streak Advanced</p>
                </div>

                <div class="space-y-2">
                    @if($nextLesson)
                        <a href="/lesson/{{ $nextLesson->id }}" class="block w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                            @if($lesson instanceof \App\Models\Lesson && $lesson->isPartOfSeries() && $nextLesson->series_identifier === $lesson->series_identifier)
                                Continue to Part {{ $nextLesson->series_order ?? 'Next' }}: {{ $nextLesson->title }} &rarr;
                            @else
                                Continue: {{ $nextLesson->title }} &rarr;
                            @endif
                        </a>
                    @endif
                    <a href="/flashcards/{{ $lesson->category_id }}" class="block w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors shadow-sm">
                        Review Lesson Flashcards &rarr;
                    </a>
                    <button 
                        type="button"
                        wire:click="$set('showCompletionCelebration', false)" 
                        class="block w-full py-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium text-xs transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
