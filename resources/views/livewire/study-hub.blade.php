<div class="space-y-6 pb-6">

    @if($successMessage)
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN CURRICULUM & CONTENT MANAGEMENT                       -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        Curriculum Management
                    </span>
                    <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">Universal Formation Tracks</h2>
                    <p class="text-xs text-slate-500">Catholic Diocese of Livingstone • Youth Ministry Formation Catalog</p>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('showCategoryModal', true)"
                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors touch-press flex items-center gap-1 shadow-sm">
                    <span>+ Add Track</span>
                </button>
            </div>

            <!-- TRACKS GRID -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Catalog Tracks</h3>
                <div class="grid grid-cols-1 gap-2.5">
                    @foreach($categories as $cat)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $cat->name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $cat->code }} &bull; {{ $cat->description }}</span>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold text-[10px] border border-purple-200/60 dark:border-purple-800/60">
                                    {{ $cat->lessons_count }} Lessons
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-1">
                                    {{ $cat->questions_count }} Questions
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- LESSONS DIRECTORY -->
            <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Lessons Directory</h3>
                
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search curriculum lessons..." 
                    class="w-full px-3.5 py-2.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500">

                <div class="space-y-2.5">
                    @foreach($lessons as $l)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $l->title }}</h4>
                                <button 
                                    type="button" 
                                    wire:click="toggleLessonStatus('{{ $l->id }}')" 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $l->status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($l->status) }}
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $l->category?->name }} &bull; {{ $l->subheading }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ADD TRACK MODAL -->
        @if($showCategoryModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Register Formation Track</h3>
                        <button wire:click="$set('showCategoryModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>

                    <form wire:submit.prevent="createCategory" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Track Name</label>
                            <input type="text" wire:model="newCatName" placeholder="e.g. Sacraments & Liturgy" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            @error('newCatName') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Code</label>
                            <input type="text" wire:model="newCatCode" placeholder="e.g. SACRAMENTS" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            @error('newCatCode') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newCatDescription" rows="2" placeholder="Doctrinal track overview..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showCategoryModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold">
                                Save Track
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) FORMATION OVERSIGHT                    -->
    <!-- ========================================================================= -->
    @elseif($user->isChairperson())
        <div class="space-y-5">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Parish Formation
                </span>
                <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">{{ $parish->name }}</h2>
                <p class="text-xs text-slate-500">Youth Catechetical Progress &amp; Completions</p>
            </div>

            <!-- LESSON COMPLETIONS -->
            <div class="space-y-2.5">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Lesson Completion Tracker</h3>
                <div class="space-y-2">
                    @foreach($lessons as $pl)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $pl->title }}</h4>
                                <span class="text-[11px] text-slate-500">{{ $pl->category?->name }}</span>
                            </div>
                            <span class="px-3 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold text-[10px] border border-purple-200/50">
                                {{ $pl->completions_count }} Youth Completed
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER FORMATION STUDY HUB (RICH MINIMALISM DISCOVERY)     -->
    <!-- ========================================================================= -->
    @else
        <!-- 1. DISCOVERY HEADER -->
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Catholic Formation Library
                </span>
            </div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">
                Study &amp; Discover
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Holy Scripture, Catechism (CCC), YOUCAT, DOCAT, and African Patristics
            </p>
        </div>

        <!-- 2. SEARCH INPUT WITH INTEGRATED FILTER -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search scripture, catechism, doctrine, saints..."
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-500 transition-colors shadow-sm">
        </div>

        <!-- 3. SPACED REPETITION FLASHCARDS HERO DRILL -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white border border-slate-800 space-y-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-purple-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold leading-tight">Spaced Flashcard Review</h4>
                        <p class="text-[11px] text-slate-300">
                            {{ $flashcardStats['due_today'] > 0 ? "{$flashcardStats['due_today']} doctrinal terms due today" : 'Strengthen Catholic memory & retention' }}
                        </p>
                    </div>
                </div>

                <a href="/flashcards{{ $selectedCategoryId ? '/' . $selectedCategoryId : '' }}" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition-colors touch-press flex-shrink-0">
                    Drill &rarr;
                </a>
            </div>
        </div>

        <!-- 4. HORIZONTAL CATEGORY FILTER RAILS -->
        <div class="space-y-2">
            <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1 -mx-4 px-4">
                <button 
                    type="button"
                    wire:click="selectCategory(null)"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all touch-press {{ is_null($selectedCategoryId) ? 'bg-purple-600 text-white shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
                    All Tracks ({{ $categories->sum('lessons_count') }})
                </button>
                @foreach($categories as $cat)
                    <button 
                        type="button"
                        wire:click="selectCategory({{ $cat->id }})"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all touch-press {{ $selectedCategoryId === $cat->id ? 'bg-purple-600 text-white shadow-sm' : 'bg-white dark:bg-[#121826] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
                        {{ $cat->name }} ({{ $cat->lessons_count }})
                    </button>
                @endforeach
            </div>
        </div>

        <!-- 5. SERIES FORMATION DIRECTORY (COMPACT & EXPANDABLE) -->
        <div class="space-y-3.5">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
                <span class="font-bold uppercase tracking-wider text-[11px] text-slate-700 dark:text-slate-300">
                    Formation Series ({{ $totalSeriesCount }})
                </span>
                <span>
                    Showing {{ count($seriesList) }} of {{ $totalSeriesCount }} series
                </span>
            </div>

            @forelse($seriesList as $series)
                <div x-data="{ expanded: false }" 
                     class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-300 dark:hover:border-purple-800/80 rounded-2xl transition-all shadow-xs overflow-hidden">
                    
                    <!-- COMPACT SERIES HEADER (CLICKABLE ACCORDION TRIGGER) -->
                    <button type="button" 
                            @click="expanded = !expanded" 
                            class="w-full text-left p-4 sm:p-5 space-y-2.5 cursor-pointer focus:outline-none select-none transition-colors hover:bg-slate-50/50 dark:hover:bg-white/[0.02]">
                        
                        <!-- Top Metadata Row -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60 truncate max-w-[200px]">
                                {{ $series['category_name'] }}
                            </span>
                            
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                    {{ $series['lessons_count'] }} {{ \Illuminate\Support\Str::plural('Lesson', $series['lessons_count']) }}
                                </span>

                                <span class="text-[11px] text-slate-400 font-medium hidden sm:inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $series['total_read_minutes'] }} min
                                </span>

                                @if($series['is_completed'])
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold text-[10px] border border-emerald-200 dark:border-emerald-800">
                                        Done ✓
                                    </span>
                                @elseif($series['in_progress'])
                                    <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-bold text-[10px] border border-amber-200 dark:border-amber-800">
                                        {{ $series['completed_count'] }}/{{ $series['lessons_count'] }} Done
                                    </span>
                                @endif

                                <!-- Animated Chevron Indicator -->
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800/80 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-transform duration-200"
                                     :class="expanded ? 'rotate-180 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : ''">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Series Title & Summary -->
                        <div>
                            <h4 class="font-bold font-serif text-slate-900 dark:text-white text-base sm:text-lg leading-snug">
                                {{ $series['name'] }}
                            </h4>
                            @if(!empty($series['summary']))
                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 mt-1 leading-relaxed">
                                    {{ $series['summary'] }}
                                </p>
                            @endif
                        </div>

                        <!-- Bottom Click Prompt & Citations -->
                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 truncate max-w-[220px]">
                                {{ $series['citation'] ?? "{$series['total_read_minutes']} min study track" }}
                            </span>
                            <span class="text-purple-600 dark:text-purple-400 font-bold flex items-center gap-1"
                                  x-text="expanded ? 'Hide lessons ↑' : 'View all {{ $series['lessons_count'] }} lessons ↓'">
                                View all {{ $series['lessons_count'] }} lessons ↓
                            </span>
                        </div>
                    </button>

                    <!-- EXPANDED LESSONS LIST (ACCORDION BODY) -->
                    <div x-show="expanded" 
                         x-collapse 
                         class="border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40 p-3 sm:p-4 space-y-2">
                        
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-1 pb-1">
                            Lessons in this Series:
                        </div>

                        <div class="space-y-2">
                            @foreach($series['lessons'] as $idx => $sLesson)
                                @php
                                    $isDone = $sLesson->progress->first()?->is_completed ?? false;
                                    $hasStarted = $sLesson->progress->isNotEmpty();
                                    
                                    // Extract clean subtitle if contains Part X
                                    $lessonSubtitle = $sLesson->title;
                                    if (preg_match('/^(?:.*?)(?:\((?:Part|Lesson)\s*(\d+)\)|\b(?:Part|Lesson)\s*(\d+)\b)\s*:\s*(.*)$/i', $sLesson->title, $pMatches)) {
                                        $partNum = $pMatches[1] ?: $pMatches[2];
                                        $partLabel = "Part {$partNum}";
                                        $lessonSubtitle = trim($pMatches[3]);
                                    } else {
                                        $partLabel = "Lesson " . ($idx + 1);
                                    }
                                @endphp

                                <a href="/lesson/{{ $sLesson->id }}" 
                                   class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-400 dark:hover:border-purple-700 rounded-xl flex items-center justify-between gap-3 transition-all touch-press group shadow-2xs">
                                    
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                {{ $partLabel }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $sLesson->estimated_read_minutes ?? 5 }} min
                                            </span>
                                        </div>

                                        <h5 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors leading-snug line-clamp-2">
                                            {{ $lessonSubtitle }}
                                        </h5>

                                        @if($sLesson->subheading)
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">
                                                {{ $sLesson->subheading }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($isDone)
                                            <span class="px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold text-[10px] border border-emerald-200 dark:border-emerald-800 flex items-center gap-1">
                                                <span>Review</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </span>
                                        @elseif($hasStarted)
                                            <span class="px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-bold text-[10px] border border-amber-200 dark:border-amber-800 flex items-center gap-1">
                                                <span>Resume</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg bg-purple-600 text-white font-bold text-[10px] shadow-2xs group-hover:bg-purple-700 flex items-center gap-1">
                                                <span>Start</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-center space-y-2">
                    <p class="text-xs font-semibold text-slate-400">No formation series found matching your search.</p>
                    <button wire:click="selectCategory(null)" class="text-xs text-purple-600 font-bold hover:underline">Reset Filters</button>
                </div>
            @endforelse

            <!-- 6. VIEW MORE / SHOW ALL SERIES AT BOTTOM -->
            @if($totalSeriesCount > 5)
                <div class="pt-3 text-center">
                    @if(!$showAllSeries)
                        <button 
                            type="button"
                            wire:click="toggleShowAllSeries"
                            class="w-full py-3.5 bg-white dark:bg-[#121826] hover:bg-purple-50/50 dark:hover:bg-purple-950/20 border-2 border-dashed border-purple-300 dark:border-purple-800/80 rounded-2xl text-purple-700 dark:text-purple-300 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-xs touch-press cursor-pointer">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            <span>View More Series ({{ $totalSeriesCount - 5 }} More Available)</span>
                        </button>
                    @else
                        <button 
                            type="button"
                            wire:click="toggleShowAllSeries"
                            class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-xs transition-all touch-press cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                            <span>Show Top 5 Series Only</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

</div>
