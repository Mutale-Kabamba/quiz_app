<div class="space-y-5 pb-6">

    @if($successMessage)
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in">
            <span>{{ $successMessage }}</span>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN CURRICULUM & CONTENT MANAGEMENT                       -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Curriculum Studio</h2>
                    <p class="text-xs text-slate-500">Universal Catholic Formation Content &bull; 30 Tracks</p>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('showCategoryModal', true)"
                    class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                    + Add Track
                </button>
            </div>

            <!-- CURRICULUM TRACKS OVERVIEW -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Formation Tracks</h3>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($categories as $cat)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white text-sm block">{{ $cat->name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $cat->code }} &bull; {{ $cat->description }}</span>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px] block">
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

            <!-- LESSON DIRECTORY -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Universal Lessons Directory</h3>
                
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search lessons..." 
                    class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">

                <div class="space-y-2">
                    @foreach($lessons as $l)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $l->title }}</h4>
                                <button 
                                    type="button" 
                                    wire:click="toggleLessonStatus('{{ $l->id }}')" 
                                    class="px-2 py-0.5 rounded text-[10px] font-bold {{ $l->status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600' }}">
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
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Register Formation Track</h3>
                        <button wire:click="$set('showCategoryModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="createCategory" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Track Name</label>
                            <input type="text" wire:model="newCatName" placeholder="e.g. Sacraments & Liturgy" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            @error('newCatName') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Code</label>
                            <input type="text" wire:model="newCatCode" placeholder="e.g. SACRAMENTS" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            @error('newCatCode') <span class="text-[10px] text-red-500 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newCatDescription" rows="2" placeholder="Doctrinal track overview..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showCategoryModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
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
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Parish Formation Oversight</h2>
                <p class="text-xs text-slate-500">{{ $parish->name }} &bull; Catechetical Completion &amp; Mastery</p>
            </div>

            <!-- FORMATION TRACKS MASTERY -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Parish Track Engagement</h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $cat->name }}</span>
                                <span class="text-purple-600 dark:text-purple-400 font-semibold">{{ $cat->questions_count }} Questions</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-purple-600 h-full rounded-full" style="width: 70%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- PARISH YOUTH LESSON COMPLETIONS -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Lesson Completion Tracking</h3>
                
                <div class="space-y-2">
                    @foreach($lessons as $pl)
                        <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $pl->title }}</h4>
                                <span class="text-[11px] text-slate-500">{{ $pl->category?->name }}</span>
                            </div>
                            <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px]">
                                {{ $pl->completions_count }} Youth Completed
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER FORMATION STUDY HUB                                 -->
    <!-- ========================================================================= -->
    @else
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
                <a href="/lesson/{{ $lesson->id }}" class="block p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 hover:border-purple-300 dark:hover:border-purple-800 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            {{ $lesson->category?->name ?? 'General Doctrine' }}
                        </span>
                        @if($lesson->progress->first()?->is_completed)
                            <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold text-[10px]">
                                Completed ✓
                            </span>
                        @endif
                    </div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $lesson->title }}</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">{{ $lesson->summary ?? $lesson->subheading }}</p>
                </a>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">No lessons found in this formation track.</p>
            @endforelse
        </div>
    @endif

</div>
