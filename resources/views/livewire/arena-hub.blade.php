<div class="space-y-6 pb-6">

    @if($successMessage)
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <span>{{ $successMessage }}</span>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN QUESTION BANK & COMPETITIONS HUB                      -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        Diocesan Question Bank
                    </span>
                    <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">Quiz Arena &amp; Rallies</h2>
                    <p class="text-xs text-slate-500">Universal Question Bank &bull; Diocesan Rally Scheduler</p>
                </div>
                <button 
                    type="button" 
                    wire:click="openCreateCompetitionModal"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-xs whitespace-nowrap flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Schedule Rally</span>
                </button>
            </div>

            <!-- TABS SWITCHER -->
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-bold">
                <button 
                    type="button"
                    wire:click="setTab('bank')"
                    class="py-2.5 rounded-lg transition-all {{ $activeTab === 'bank' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-xs border border-slate-200 dark:border-slate-700 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                    Question Repository ({{ $totalQuestionsCount }} Qs)
                </button>
                <button 
                    type="button"
                    wire:click="setTab('rallies')"
                    class="py-2.5 rounded-lg transition-all {{ $activeTab === 'rallies' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-xs border border-slate-200 dark:border-slate-700 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                    Scheduled Rallies ({{ $diocesanCompetitions->count() }})
                </button>
            </div>

            <!-- TAB 1: QUESTION BANK (CATEGORY, LEVEL & QUESTION COUNT ONLY) -->
            @if($activeTab === 'bank')
                <div class="space-y-3">
                    <!-- Filters & Action Bar -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200/60 dark:border-purple-800">
                                    Total: {{ $totalQuestionsCount }} Qs
                                </span>
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200/60 dark:border-emerald-800">
                                    {{ $totalActiveQuestionsCount }} Active
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button 
                                    type="button" 
                                    wire:click="openImportModal"
                                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap touch-press">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Import</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="openCreateQuestionModal"
                                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-xs whitespace-nowrap touch-press">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>+ Add Q&amp;A</span>
                                </button>
                            </div>
                        </div>

                        <!-- Track and Level Filters -->
                        <div class="grid grid-cols-2 gap-2">
                            <select 
                                wire:model.live="selectedCategoryFilter"
                                class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 font-medium shadow-xs focus:outline-none focus:border-purple-600">
                                <option value="">All Categories &amp; Tracks</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>

                            <select 
                                wire:model.live="selectedLevelFilter"
                                class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-200 font-medium shadow-xs focus:outline-none focus:border-purple-600">
                                <option value="">All Formation Levels</option>
                                <option value="1">Level 1 - Junior</option>
                                <option value="2">Level 2 - Youth / Intermediate</option>
                                <option value="3">Level 3 - Advanced</option>
                                <option value="4">Level 4 - Expert</option>
                            </select>
                        </div>
                    </div>

                    <!-- Category & Level Question Pool Summary Cards (No Actual Questions Displayed) -->
                    <div class="space-y-3">
                        @forelse($trackLevelSummaries as $summary)
                            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 shadow-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold">
                                                {{ $summary->category?->name ?? 'Formation Track' }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                                                Level {{ $summary->level }}
                                                @if($summary->level == 1) &bull; Junior
                                                @elseif($summary->level == 2) &bull; Youth
                                                @elseif($summary->level == 3) &bull; Advanced
                                                @elseif($summary->level >= 4) &bull; Expert
                                                @endif
                                            </span>
                                        </div>

                                        <h4 class="font-bold text-slate-900 dark:text-white text-base">
                                            {{ $summary->category?->name ?? 'Universal Questions' }}
                                        </h4>
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <div class="text-lg font-black text-purple-700 dark:text-purple-300 font-serif">
                                            {{ $summary->total_questions }}
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            Questions
                                        </span>
                                    </div>
                                </div>

                                <!-- Availability Progress & Metric Pill -->
                                <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl flex items-center justify-between text-xs border border-slate-100 dark:border-slate-800/80">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                                            <strong>{{ $summary->active_questions }}</strong> Questions Active for Quizzes
                                        </span>
                                    </div>

                                    @if($summary->total_questions - $summary->active_questions > 0)
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $summary->total_questions - $summary->active_questions }} inactive
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions for this Track/Level -->
                                <div class="flex items-center justify-end gap-1.5 flex-wrap pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <button 
                                        type="button" 
                                        wire:click="toggleTrackQuestionsActive({{ $summary->category_id }}, {{ $summary->level }})"
                                        class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors"
                                        title="Toggle active status for questions in this track tier">
                                        {{ $summary->active_questions > 0 ? 'Deactivate All' : 'Activate All' }}
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openImportModalForTrack({{ $summary->category_id }})"
                                        class="px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Import</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openManageTrackModal({{ $summary->category_id }}, {{ $summary->level }})"
                                        class="px-2.5 py-1.5 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 text-purple-700 dark:text-purple-300 text-xs font-semibold rounded-xl flex items-center gap-1 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Update Track</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:confirm="Are you sure you want to delete all {{ $summary->total_questions }} questions in {{ $summary->category?->name ?? 'this track' }} (Level {{ $summary->level }})? This action cannot be undone."
                                        wire:click="deleteTrackQuestions({{ $summary->category_id }}, {{ $summary->level }})"
                                        class="px-2.5 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl transition-colors"
                                        title="Delete all questions in this track and level">
                                        Delete Bank
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openCreateQuestionModalForTrack({{ $summary->category_id }}, {{ $summary->level }})"
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Add Q&amp;A</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                                No questions found matching this filter. Click <strong>+ Add Q&amp;A</strong> or <strong>Import</strong> above to add questions to this track!
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- TAB 2: DIOCESAN RALLIES (CATEGORY, LEVEL & QUESTION COUNT ONLY) -->
            @if($activeTab === 'rallies')
                <div class="space-y-3">
                    @forelse($diocesanCompetitions as $comp)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 shadow-xs">
                            <div class="flex items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold">
                                            {{ $comp->category?->name ?? 'All Categories' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold">
                                            Level {{ $comp->level ?? 2 }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $comp->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ $comp->status }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold font-serif text-slate-900 dark:text-white text-base mt-1">{{ $comp->title }}</h4>
                                    @if($comp->description)
                                        <p class="text-xs text-slate-500">{{ $comp->description }}</p>
                                    @endif
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <div class="text-lg font-black text-purple-700 dark:text-purple-300 font-serif">
                                        {{ $comp->question_count }}
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        Questions
                                    </span>
                                </div>
                            </div>

                            <!-- Rally Metrics (PIN, Time Limit, Schedule) -->
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs border border-slate-100 dark:border-slate-800/80">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Rally Access PIN</span>
                                    <strong class="text-purple-600 dark:text-purple-400 font-mono text-xs font-bold">{{ $comp->rally_pin }}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Time Limit</span>
                                    <strong class="text-slate-700 dark:text-slate-300 font-semibold">{{ $comp->time_limit_seconds }} seconds</strong>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <span class="text-[10px] text-slate-400 block">Active Window</span>
                                    <strong class="text-slate-700 dark:text-slate-300 font-semibold text-[11px]">{{ $comp->start_time?->format('M d') }} &rarr; {{ $comp->end_time?->format('M d, Y') }}</strong>
                                </div>
                            </div>

                            <!-- Card Action Footer -->
                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                                <button 
                                    type="button" 
                                    wire:click="toggleCompetitionStatus('{{ $comp->id }}')"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $comp->status === 'active' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                    {{ $comp->status === 'active' ? 'Conclude Rally' : 'Activate Rally' }}
                                </button>

                                <div class="flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="editCompetition('{{ $comp->id }}')"
                                        class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl">
                                        Edit
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:confirm="Delete this competition?"
                                        wire:click="deleteCompetition('{{ $comp->id }}')"
                                        class="px-3 py-1.5 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 text-red-600 dark:text-red-400 text-xs font-semibold rounded-xl">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                            No scheduled rallies found. Click <strong>+ Schedule Rally</strong> above to set up a new diocesan competition!
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- A. QUESTION CREATE & EDIT MODAL -->
        @if($showQuestionModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                    <div class="flex items-center justify-between border-b pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $editQuestionId ? 'Edit Q&A Item' : 'Add New Q&A Item' }}
                            </h3>
                        </div>
                        <button wire:click="$set('showQuestionModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <div class="space-y-3 text-xs max-h-[70vh] overflow-y-auto pr-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Category / Track *</label>
                                <select wire:model="newQuestionCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('newQuestionCategoryId') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Difficulty Level</label>
                                <select wire:model="newQuestionLevel" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                    <option value="1">Level 1 - Easy</option>
                                    <option value="2">Level 2 - Medium</option>
                                    <option value="3">Level 3 - Hard</option>
                                    <option value="4">Level 4 - Expert</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Question Prompt *</label>
                            <textarea wire:model="newQuestionText" rows="2" placeholder="e.g. Which Gospel contains the Beatitudes?" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                            @error('newQuestionText') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- 4 Options -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option A *</label>
                                <input type="text" wire:model="optionA" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                @error('optionA') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option B *</label>
                                <input type="text" wire:model="optionB" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                @error('optionB') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option C *</label>
                                <input type="text" wire:model="optionC" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                @error('optionC') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Option D *</label>
                                <input type="text" wire:model="optionD" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                @error('optionD') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Correct Answer *</label>
                                <select wire:model="correctOption" class="w-full px-3 py-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-700 rounded-xl font-bold text-emerald-800 dark:text-emerald-200">
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Scripture / CCC Citation</label>
                                <input type="text" wire:model="newQuestionCitation" placeholder="e.g. CCC 1213, Matthew 28:19" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Doctrinal Explanation *</label>
                            <textarea wire:model="newQuestionExplanation" rows="2" placeholder="Why is this answer correct according to Catholic doctrine?" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                            @error('newQuestionExplanation') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <button wire:click="$set('showQuestionModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                        <button wire:click="saveQuestion" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">Save Q&amp;A</button>
                    </div>
                </div>
            </div>
        @endif

        <!-- B. DYNAMIC FILE IMPORT MODAL -->
        @if($showImportModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                    <div class="flex items-center justify-between border-b pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Import Questions &amp; Content</h3>
                                <p class="text-[11px] text-slate-500">Upload your custom CSV, Excel (.xlsx), or JSON file</p>
                            </div>
                        </div>
                        <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <!-- Template Download Section -->
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-xl flex items-center justify-between text-xs">
                        <span class="text-purple-900 dark:text-purple-200 font-medium">Need sample formatting?</span>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                wire:click="downloadSampleTemplate('csv')"
                                class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-sm">
                                Download CSV
                            </button>
                            <button 
                                type="button" 
                                wire:click="downloadSampleTemplate('json')"
                                class="px-2.5 py-1 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg font-bold text-[11px] flex items-center gap-1">
                                Download JSON
                            </button>
                        </div>
                    </div>

                    <!-- File Upload Input Area -->
                    <div class="space-y-3 text-xs">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-bold text-slate-700 dark:text-slate-300">Select File (CSV, XLSX, JSON)</label>
                                <span class="text-[10px] text-slate-400 font-semibold">Max 10MB</span>
                            </div>
                            <input 
                                type="file" 
                                wire:model="importFile"
                                accept=".csv, .xlsx, .xls, .json, text/csv, application/json, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                            @error('importFile') <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Fallback Category / Track</label>
                                <select wire:model="importTrackId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                    <option value="">Auto-Detect from File</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Duplicate Handling</label>
                                <select wire:model="importDuplicateStrategy" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                    <option value="skip">Skip duplicates</option>
                                    <option value="overwrite">Overwrite existing</option>
                                    <option value="error">Abort on error</option>
                                </select>
                            </div>
                        </div>

                        @if($isImporting)
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl text-center text-indigo-700 dark:text-indigo-300 flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Parsing file and validating questions...</span>
                            </div>
                        @endif

                        @if($importResults)
                            <div class="p-3 bg-slate-100 dark:bg-slate-900 rounded-xl space-y-1 text-[11px]">
                                <div class="font-bold text-slate-900 dark:text-white">Import Summary:</div>
                                <div class="text-emerald-600 font-medium">&bull; {{ $importResults['successful'] }} items imported</div>
                                @if($importResults['skipped'] > 0)
                                    <div class="text-amber-600">&bull; {{ $importResults['skipped'] }} duplicates skipped</div>
                                @endif
                                @if(!empty($importResults['errors']))
                                    <div class="text-red-500 font-bold">&bull; {{ count($importResults['errors']) }} row errors:</div>
                                    <ul class="list-disc pl-4 text-[10px] text-red-400 max-h-20 overflow-y-auto">
                                        @foreach(array_slice($importResults['errors'], 0, 5) as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <button wire:click="$set('showImportModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Close</button>
                        <button 
                            type="button" 
                            wire:click="processDynamicImport" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm">
                            <span wire:loading.remove wire:target="processDynamicImport">Run Import</span>
                            <span wire:loading wire:target="processDynamicImport">Importing...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- C. DIOCESAN COMPETITION / RALLY MODAL -->
        @if($showDiocesanCompModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $editCompId ? 'Edit Rally / Competition' : 'Schedule Diocesan Rally' }}
                                </h3>
                                <p class="text-[11px] text-slate-500">Configure scope, eligibility, and youth participation rules</p>
                            </div>
                        </div>
                        <button wire:click="$set('showDiocesanCompModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <div class="space-y-3.5 text-xs max-h-[75vh] overflow-y-auto pr-1">
                        <!-- Rally Title -->
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally Title *</label>
                            <input type="text" wire:model="newCompTitle" placeholder="e.g. 2026 Livingstone Diocesan Youth Bible Rally" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:border-purple-600">
                            @error('newCompTitle') <span class="text-red-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <!-- Scope & Classification -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Participation Scope *</label>
                                <select wire:model.live="newCompScopeType" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-purple-600">
                                    <option value="diocese">Diocese (All Livingstone Youth)</option>
                                    <option value="deanery">Deanery (Specific Deanery)</option>
                                    <option value="parish">Parish (Specific Parish)</option>
                                    <option value="custom">Custom (Individual Personal Codes)</option>
                                </select>
                            </div>

                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Event Classification *</label>
                                <select wire:model="newCompClassification" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:border-purple-600">
                                    <option value="diocesan">Diocesan Championship</option>
                                    <option value="deanery">Deanery Championship</option>
                                    <option value="parish">Parish Tournament</option>
                                    <option value="youth_rally">Youth Congress / Rally</option>
                                </select>
                            </div>
                        </div>

                        <!-- Scope Condition: Deanery Selector -->
                        @if($newCompScopeType === 'deanery')
                            <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-xl space-y-1">
                                <label class="font-bold text-purple-900 dark:text-purple-200 block">Select Target Deanery *</label>
                                <select wire:model="newCompDeaneryId" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-purple-300 dark:border-purple-700 rounded-xl text-slate-900 dark:text-white">
                                    <option value="">-- Choose Deanery --</option>
                                    @foreach($deaneries as $deanery)
                                        <option value="{{ $deanery->id }}">{{ $deanery->name }}</option>
                                    @endforeach
                                </select>
                                @error('newCompDeaneryId') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Scope Condition: Parish Selector -->
                        @if($newCompScopeType === 'parish')
                            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl space-y-1">
                                <label class="font-bold text-amber-900 dark:text-amber-200 block">Select Target Parish *</label>
                                <select wire:model="newCompParishId" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-xl text-slate-900 dark:text-white">
                                    <option value="">-- Choose Parish --</option>
                                    @foreach($parishes as $parish)
                                        <option value="{{ $parish->id }}">{{ $parish->name }} ({{ $parish->deanery?->name ?? 'Deanery' }})</option>
                                    @endforeach
                                </select>
                                @error('newCompParishId') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Scope Condition: Custom Youth Participants Selector -->
                        @if($newCompScopeType === 'custom')
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        Select Youth Participants ({{ count($selectedCustomUserIds) }} selected)
                                    </span>
                                    <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Each youth gets a personal code</span>
                                </div>

                                <!-- Selected Chips -->
                                @if(!empty($selectedCustomUserIds))
                                    <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                                        @foreach($selectedCustomUserIds as $uid)
                                            @php $u = $allYouth->firstWhere('id', $uid) ?? \App\Models\User::find($uid); @endphp
                                            @if($u)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 rounded-lg text-[11px] font-medium text-purple-800 dark:text-purple-200">
                                                    <span>{{ $u->name }}</span>
                                                    <button type="button" wire:click="removeCustomUser('{{ $u->id }}')" class="text-purple-500 hover:text-purple-800 font-bold">&times;</button>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Search Input -->
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.250ms="youthSearchTerm" 
                                    placeholder="Search youth by name, email, or parish..." 
                                    class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs">

                                <!-- Available Youth List -->
                                <div class="max-h-36 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-[#121826]">
                                    @forelse($allYouth as $youth)
                                        @php $isSelected = in_array((string)$youth->id, array_map('strval', $selectedCustomUserIds)); @endphp
                                        <div 
                                            wire:click="toggleCustomUser('{{ $youth->id }}')"
                                            class="p-2 flex items-center justify-between hover:bg-purple-50/50 dark:hover:bg-purple-950/20 cursor-pointer transition-colors text-[11px] {{ $isSelected ? 'bg-purple-50/70 dark:bg-purple-950/40' : '' }}">
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-white block">{{ $youth->name }}</span>
                                                <span class="text-slate-400 text-[10px]">{{ $youth->parish?->name ?? 'No Parish' }} &bull; {{ $youth->email }}</span>
                                            </div>
                                            <input 
                                                type="checkbox" 
                                                {{ $isSelected ? 'checked' : '' }} 
                                                class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 pointer-events-none">
                                        </div>
                                    @empty
                                        <div class="p-3 text-center text-slate-400 text-[11px]">No youth accounts found matching search.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <!-- Description -->
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Description / Guidelines *</label>
                            <textarea wire:model="newCompDescription" rows="2" placeholder="Rules, syllabus, and participant instructions..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:border-purple-600"></textarea>
                            @error('newCompDescription') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category / Track -->
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Category / Formation Track</label>
                            <select wire:model="newCompCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                                <option value="">All Categories (Mixed Doctrine &amp; Scripture)</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rally Schedule Dates -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally Start Date &amp; Time *</label>
                                <input type="datetime-local" wire:model="newCompStartTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                                @error('newCompStartTime') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally End Date &amp; Time *</label>
                                <input type="datetime-local" wire:model="newCompEndTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                                @error('newCompEndTime') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Registration Window -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Registration Opens</label>
                                <input type="datetime-local" wire:model="newCompRegistrationOpenAt" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Registration Closes</label>
                                <input type="datetime-local" wire:model="newCompRegistrationCloseAt" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Timing & Question Count -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Seconds per Question</label>
                                <input type="number" wire:model="newCompTimeLimit" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Total Question Count</label>
                                <input type="number" wire:model="newCompQuestionCount" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Join Requests Toggle -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">Allow Public Join Requests</span>
                                <span class="text-[10px] text-slate-400">Youth can request entry from the public rally catalog</span>
                            </div>
                            <input 
                                type="checkbox" 
                                wire:model="newCompJoinRequestsEnabled" 
                                class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 w-4 h-4">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button wire:click="$set('showDiocesanCompModal', false)" class="px-3.5 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700">Cancel</button>
                        <button wire:click="saveCompetition" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-xs touch-press">
                            {{ $editCompId ? 'Save Changes' : 'Schedule Rally' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MANAGE & UPDATE TRACK Q&A BANK MODAL -->
        @if($showManageTrackModal)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-lg w-full space-y-4 shadow-xl my-8">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Update Track Q&amp;A Bank</h3>
                                <p class="text-[11px] text-slate-500">Edit track metadata, reassign questions, or batch toggle availability</p>
                            </div>
                        </div>
                        <button wire:click="$set('showManageTrackModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Track / Formation Name</label>
                            <input type="text" wire:model="manageTrackName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white">
                            @error('manageTrackName') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Description</label>
                            <textarea wire:model="manageTrackDescription" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5">
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 block">Batch Actions for Questions in This Track @if($manageTrackLevel) (Level {{ $manageTrackLevel }}) @endif</span>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 block mb-1">Reassign Formation Level</label>
                                    <select wire:model="manageTargetLevel" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200">
                                        <option value="1">Level 1 - Junior</option>
                                        <option value="2">Level 2 - Youth / Intermediate</option>
                                        <option value="3">Level 3 - Advanced</option>
                                        <option value="4">Level 4 - Expert</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 block mb-1">Reassign Track / Category</label>
                                    <select wire:model="manageTargetCategoryId" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">Batch Active State</label>
                                <select wire:model="manageBatchActiveAction" class="w-full px-2.5 py-1.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-700 dark:text-slate-200">
                                    <option value="keep">Keep Current Status</option>
                                    <option value="activate_all">Activate All Questions in Track Tier</option>
                                    <option value="deactivate_all">Deactivate All Questions in Track Tier</option>
                                </select>
                            </div>
                        </div>

                        <!-- Danger Zone inside Modal -->
                        <div class="p-3 bg-red-50/70 dark:bg-red-950/20 border border-red-200/70 dark:border-red-900/40 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-bold text-red-700 dark:text-red-400 block text-xs">Delete Track Questions</span>
                                <span class="text-[10px] text-red-600/80 dark:text-red-400/80">Permanently delete all questions in this bank tier.</span>
                            </div>
                            <button 
                                type="button" 
                                wire:confirm="Are you sure you want to delete all questions in this track tier? This action cannot be undone."
                                wire:click="deleteTrackQuestions({{ $manageTrackCategoryId ?? 0 }}, {{ $manageTrackLevel }})"
                                class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-[11px] transition-colors">
                                Delete Now
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button wire:click="$set('showManageTrackModal', false)" class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                        <button wire:click="saveTrackQAManagement" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">Save Changes</button>
                    </div>
                </div>
            </div>
        @endif

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) PARISH QUIZ MANAGEMENT                 -->
    <!-- ========================================================================= -->
    @elseif($user->isChairperson())
        <div class="space-y-5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        Parish Quiz Battles
                    </span>
                    <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white tracking-tight mt-1">{{ $parish->name }}</h2>
                    <p class="text-xs text-slate-500">Live Quizzes &amp; Parish Formative Competitions</p>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('showParishQuizModal', true)"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-sm">
                    + Host Parish Quiz
                </button>
            </div>

            <!-- PARISH QUIZZES LIST -->
            <div class="space-y-3">
                @forelse($parishCompetitions as $pq)
                    <div class="p-5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold font-serif text-slate-900 dark:text-white text-base">{{ $pq->title }}</h4>
                            <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold text-[10px]">
                                PIN: {{ $pq->rally_pin }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">{{ $pq->description }}</p>
                        <div class="grid grid-cols-2 gap-1 text-[11px] pt-2 border-t border-slate-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">
                            <span>Track: {{ $pq->category?->name ?? 'General Doctrine' }}</span>
                            <span>Time: {{ $pq->time_limit_seconds }}s</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-8 text-center bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl">
                        No active parish quiz battles. Host one for your youth!
                    </p>
                @endforelse
            </div>
        </div>

        <!-- HOST PARISH QUIZ MODAL -->
        @if($showParishQuizModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Host Parish Live Quiz</h3>
                        <button wire:click="$set('showParishQuizModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>

                    <form wire:submit.prevent="createParishQuiz" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Quiz Title</label>
                            <input type="text" wire:model="newParishQuizTitle" placeholder="e.g. Parish Confirmation Quiz Challenge" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newParishQuizDescription" rows="2" placeholder="Instructions for youth..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Curriculum Track</label>
                            <select wire:model="newParishQuizCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                                <option value="">All Categories</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showParishQuizModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">
                                Host Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH PRACTICE & COMPETE ARENA (RICH MINIMALISM)                  -->
    <!-- ========================================================================= -->
    @else
        <!-- 1. ARENA HEADER -->
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Formation Arena
                </span>
            </div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">
                Quiz &amp; Competition Arena
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Self-paced catechetical practice and live diocesan rallies
            </p>
        </div>

        <!-- 2. SEGMENTED SWITCHER: PRACTICE vs COMPETE -->
        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
            <button 
                type="button"
                wire:click="setTab('practice')"
                class="py-2.5 rounded-xl transition-all touch-press flex items-center justify-center gap-1.5 {{ $activeTab === 'practice' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Practice</span>
            </button>
            <button 
                type="button"
                wire:click="setTab('compete')"
                class="py-2.5 rounded-xl transition-all touch-press flex items-center justify-center gap-1.5 {{ $activeTab === 'compete' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Compete</span>
            </button>
        </div>

        <!-- 3. PRACTICE MODE VIEW -->
        @if($activeTab === 'practice')
            <div class="space-y-4">
                <!-- DIFFICULTY SELECTOR -->
                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-sm">
                    <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider block">Formation Level Difficulty</span>
                    <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                        <button type="button" wire:click="setLevel(1)" class="py-2 rounded-xl transition-all {{ $selectedLevel === 1 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-800' }}">Level 1 (Basic)</button>
                        <button type="button" wire:click="setLevel(2)" class="py-2 rounded-xl transition-all {{ $selectedLevel === 2 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-800' }}">Level 2 (Core)</button>
                        <button type="button" wire:click="setLevel(3)" class="py-2 rounded-xl transition-all {{ $selectedLevel === 3 ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800' : 'bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-800' }}">Level 3 (Advanced)</button>
                    </div>
                </div>

                <!-- AVAILABLE PRACTICE TRACKS -->
                <div class="space-y-2.5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Available Practice Quizzes</h3>
                    @forelse($categories as $cat)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs hover:border-purple-300 transition-colors shadow-sm">
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $cat->name }}</h4>
                                <span class="text-[11px] {{ $cat->questions_count > 0 ? 'text-slate-500 dark:text-slate-400' : 'text-slate-400' }}">
                                    {{ $cat->questions_count }} Questions Available
                                </span>
                            </div>
                            @if($cat->questions_count > 0)
                                <a href="/quiz/play/{{ $cat->id }}?level={{ $selectedLevel }}&mode=practice" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors touch-press shadow-sm">
                                    Practice &rarr;
                                </a>
                            @else
                                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 font-semibold rounded-xl text-[11px]">
                                    Awaiting Questions
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-center">
                            <p class="text-xs text-slate-400">No formation tracks available. Check back soon!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- 4. COMPETE & RALLY MODE VIEW -->
        @if($activeTab === 'compete')
            <div class="space-y-5">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Competitive Play</span>
                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white">Livingstone Diocesan Ranked Arena</h3>
                    <p class="text-xs text-slate-500">Official Deanery, Parish &amp; Diocesan Formation Competitions</p>
                </div>

                @if($errorMessage)
                    <div class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-xs text-rose-800 dark:text-rose-200 font-semibold flex items-center justify-between animate-fade-in shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ $errorMessage }}</span>
                        </div>
                        <button wire:click="$set('errorMessage', null)" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                    </div>
                @endif

                <!-- RALLY PIN / ACCESS CODE LOBBY CARD -->
                <div class="p-5 bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 text-white border border-purple-800/40 rounded-2xl space-y-4 shadow-sm">
                    <div class="space-y-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 font-bold uppercase text-[10px] border border-white/15">
                            Live Youth Rally Lobby
                        </span>
                        <h3 class="text-base font-bold font-serif">Enter Rally PIN or Access Code</h3>
                        <p class="text-xs text-purple-200/80">Enter the public Rally PIN or your unique personal access code (e.g. LV26-K7X9-P2).</p>
                    </div>

                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model="rallyPin" 
                            wire:keydown.enter="enterRallyWithPin"
                            placeholder="e.g. LV-CATH-7K29X or LV26-K7X9-P2" 
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-xs font-mono text-white placeholder-purple-300/50 focus:outline-none focus:border-purple-400 uppercase tracking-wider">
                        <button 
                            type="button" 
                            wire:click="enterRallyWithPin" 
                            class="px-5 py-3 bg-purple-500 hover:bg-purple-600 text-white font-bold rounded-xl text-xs whitespace-nowrap transition-colors touch-press shadow-sm flex items-center gap-1.5">
                            <span>Enter Rally</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- MY RALLIES -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center justify-between">
                        <span>My Rallies &amp; Invites</span>
                        <span class="text-[10px] text-slate-400 font-normal">Active &bull; Upcoming &bull; Completed</span>
                    </h4>

                    @if(empty($myRallies['active']) && empty($myRallies['upcoming']) && empty($myRallies['completed']) && $myRallies['pending_requests']->isEmpty())
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-center text-xs text-slate-500">
                            You have not joined any rallies yet. Explore the available rallies below!
                        </div>
                    @else
                        <!-- Active Participations -->
                        @foreach($myRallies['active'] as $item)
                            @php $p = $item['participant']; $r = $item['rally']; @endphp
                            <div class="p-4 bg-white dark:bg-[#121826] border-2 border-emerald-500/60 dark:border-emerald-500/40 rounded-2xl space-y-2.5 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                                LIVE NOW
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                                {{ $r->scope_type ?? 'Diocese' }}
                                            </span>
                                        </div>
                                        <h5 class="font-bold text-slate-900 dark:text-white text-sm mt-1">{{ $r->title }}</h5>
                                    </div>
                                    <a href="/quiz/play?competition={{ $r->id }}&code={{ $p->access_code }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs touch-press whitespace-nowrap">
                                        Enter &rarr;
                                    </a>
                                </div>
                                @if($p->access_code)
                                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-xl flex items-center justify-between text-[11px] font-mono text-slate-700 dark:text-slate-300">
                                        <span>Personal Code: <strong class="text-purple-600 dark:text-purple-400">{{ $p->access_code }}</strong></span>
                                        <span class="text-[10px] text-slate-400 font-sans">Assigned to your account</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- Upcoming Participations -->
                        @foreach($myRallies['upcoming'] as $item)
                            @php $p = $item['participant']; $r = $item['rally']; @endphp
                            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2 text-xs shadow-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                                {{ $r->scope_type ?? 'Diocese' }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-semibold">
                                                Registered
                                            </span>
                                        </div>
                                        <h5 class="font-bold text-slate-900 dark:text-white text-sm mt-1">{{ $r->title }}</h5>
                                        <p class="text-[11px] text-slate-500">Starts: {{ $r->start_time ? $r->start_time->format('d M Y, H:i') : 'TBA' }}</p>
                                    </div>
                                    @if($p->access_code)
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-400 block">Your Access Code</span>
                                            <span class="font-mono font-bold text-purple-600 dark:text-purple-400 text-xs">{{ $p->access_code }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Completed Participations -->
                        @foreach($myRallies['completed'] as $item)
                            @php $p = $item['participant']; $r = $item['rally']; @endphp
                            <div class="p-4 bg-white dark:bg-[#121826] border border-indigo-200 dark:border-indigo-900/60 rounded-2xl space-y-2.5 shadow-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold uppercase">
                                                COMPLETED
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                                {{ $r->scope_type ?? 'Diocese' }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                                Score: {{ $p->score }} pts
                                            </span>
                                        </div>
                                        <h5 class="font-bold text-slate-900 dark:text-white text-sm mt-1">{{ $r->title }}</h5>
                                        <p class="text-[11px] text-slate-400">
                                            Submitted: {{ $p->completed_at ? $p->completed_at->format('d M Y, H:i') : 'Completed' }}
                                        </p>
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="openRallyReview('{{ $p->id }}')" 
                                        class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs touch-press whitespace-nowrap">
                                        View Score &amp; Answers
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pending Join Requests -->
                        @foreach($myRallies['pending_requests'] as $req)
                            <div class="p-3.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl flex items-center justify-between text-xs">
                                <div>
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-[10px] font-bold">
                                        REQUEST PENDING REVIEW
                                    </span>
                                    <h5 class="font-bold text-slate-900 dark:text-white text-xs mt-1">{{ $req->rally?->title }}</h5>
                                </div>
                                <span class="text-[11px] text-amber-700 dark:text-amber-300 font-medium">Awaiting Approval</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- DISCOVER AVAILABLE RALLIES -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Discover Available &amp; Upcoming Deanery Rallies
                    </h4>

                    <div class="space-y-3">
                        @forelse($availableRallies as $availRally)
                            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                                Scope: {{ $availRally->scope_type ?? 'Diocese' }}
                                            </span>
                                            @if($availRally->isLiveNow())
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                                    Live
                                                </span>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-slate-900 dark:text-white text-sm">{{ $availRally->title }}</h5>
                                        <p class="text-[11px] text-slate-500 line-clamp-2">{{ $availRally->description }}</p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div class="text-[11px] text-slate-500">
                                        <span>{{ $availRally->question_count ?: 15 }} Questions &bull; {{ $availRally->time_limit_seconds ?: 15 }}s per Q</span>
                                    </div>

                                    @if($availRally->isLiveNow())
                                        <a href="/quiz/play?competition={{ $availRally->id }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs touch-press">
                                            Enter &rarr;
                                        </a>
                                    @elseif($availRally->join_requests_enabled)
                                        <button 
                                            type="button" 
                                            wire:click="openJoinRequestModal('{{ $availRally->id }}')" 
                                            class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs touch-press">
                                            Request to Join
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400">Opens {{ $availRally->start_time ? $availRally->start_time->format('d M') : 'soon' }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl text-center text-xs text-slate-400">
                                No public rallies available for your jurisdiction at this time.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- JOIN REQUEST MODAL -->
                @if($showJoinModal)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
                        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-4 shadow-2xl">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Request Rally Entry</h4>
                                <button wire:click="$set('showJoinModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                            </div>

                            <div class="space-y-3 text-xs">
                                <p class="text-slate-600 dark:text-slate-400 text-[11px]">
                                    Submit your request to the diocesan and parish rally coordinators. Once approved, your personalized access code will be generated.
                                </p>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1 text-[11px]">Optional Message / Notes</label>
                                    <textarea 
                                        wire:model="joinRequestMessage" 
                                        rows="2" 
                                        placeholder="e.g. St. Mary's Youth Choir representative..." 
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-purple-600"></textarea>
                                </div>

                                <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <button 
                                        type="button" 
                                        wire:click="$set('showJoinModal', false)" 
                                        class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                                        Cancel
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="submitJoinRequest" 
                                        class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                                        Submit Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- RALLY SCORE & ANSWERS REVIEW MODAL -->
                @if($showRallyReviewModal && $rallyReviewData)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
                        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl my-8">
                            <!-- Header -->
                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold uppercase">
                                            Rally Official Result
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                            {{ $rallyReviewData['rally']->scope_type ?? 'Diocese' }} Scope
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white mt-1.5">
                                        {{ $rallyReviewData['rally']->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Completed: {{ $rallyReviewData['completed_at'] ? \Carbon\Carbon::parse($rallyReviewData['completed_at'])->format('d M Y \a\t H:i') : 'Recorded' }}
                                    </p>
                                </div>
                                <button wire:click="$set('showRallyReviewModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
                            </div>

                            <!-- Score Card Summary -->
                            <div class="grid grid-cols-3 gap-2.5 text-center">
                                <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl">
                                    <span class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 block">Final Score</span>
                                    <span class="text-lg font-extrabold text-indigo-900 dark:text-indigo-100">{{ $rallyReviewData['score'] }} pts</span>
                                </div>
                                <div class="p-3 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 rounded-2xl">
                                    <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block">Accuracy</span>
                                    <span class="text-lg font-extrabold text-emerald-900 dark:text-emerald-100">
                                        {{ $rallyReviewData['attempt'] ? "{$rallyReviewData['attempt']->correct_answers_count} / {$rallyReviewData['attempt']->total_questions}" : 'Submitted' }}
                                    </span>
                                </div>
                                <div class="p-3 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/50 rounded-2xl">
                                    <span class="text-[10px] uppercase font-bold text-amber-600 dark:text-amber-400 block">Time Taken</span>
                                    <span class="text-lg font-extrabold text-amber-900 dark:text-amber-100">
                                        {{ $rallyReviewData['attempt'] ? "{$rallyReviewData['attempt']->time_taken_seconds}s" : '--' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Questions & Answers Review Breakdown -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center justify-between">
                                    <span>Questions &amp; Formational Answers</span>
                                    <span class="text-[10px] text-slate-400 font-normal">1 Attempt Registered</span>
                                </h4>

                                <div class="space-y-3.5 max-h-96 overflow-y-auto pr-1">
                                    @forelse($rallyReviewData['answers'] as $index => $answer)
                                        @php $q = $answer->question; @endphp
                                        @if($q)
                                            <div class="p-4 rounded-2xl border text-xs space-y-2.5 transition-all {{ $answer->is_correct ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' }}">
                                                <!-- Question Header -->
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $answer->is_correct ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                        <span class="font-bold text-slate-900 dark:text-white">{{ $q->category?->name ?? 'Doctrine' }}</span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200' }}">
                                                        {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                                                    </span>
                                                </div>

                                                <!-- Question Text -->
                                                <p class="font-semibold text-slate-800 dark:text-slate-100 text-xs leading-relaxed">
                                                    {{ $q->question_text }}
                                                </p>

                                                <!-- Options Breakdown -->
                                                <div class="grid grid-cols-1 gap-1.5 pt-1">
                                                    @foreach($q->options ?? [] as $optKey => $optVal)
                                                        @php
                                                            $isUserChoice = ($answer->selected_option_key === $optKey);
                                                            $isCorrectOption = ($q->correct_option_key === $optKey);
                                                        @endphp
                                                        <div class="p-2 rounded-xl border flex items-center justify-between text-[11px] {{ $isCorrectOption ? 'bg-emerald-100/80 dark:bg-emerald-900/40 border-emerald-300 dark:border-emerald-700 font-bold text-emerald-900 dark:text-emerald-100' : ($isUserChoice ? 'bg-rose-100/80 dark:bg-rose-900/40 border-rose-300 dark:border-rose-700 line-through text-rose-900 dark:text-rose-200' : 'bg-white dark:bg-[#121826] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300') }}">
                                                            <div class="flex items-center gap-2">
                                                                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-mono border {{ $isCorrectOption ? 'border-emerald-600 bg-emerald-600 text-white' : ($isUserChoice ? 'border-rose-600 bg-rose-600 text-white' : 'border-slate-300 text-slate-500') }}">
                                                                    {{ $optKey }}
                                                                </span>
                                                                <span>{{ $optVal }}</span>
                                                            </div>
                                                            @if($isCorrectOption)
                                                                <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold">&check; Correct Answer</span>
                                                            @elseif($isUserChoice)
                                                                <span class="text-[10px] text-rose-700 dark:text-rose-300 font-bold">&times; Your Choice</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Scripture & Catechism References & Explanation -->
                                                @if($q->reference_citation || $q->explanation)
                                                    <div class="p-2.5 bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl space-y-1 text-[11px]">
                                                        @if($q->reference_citation)
                                                            <div class="flex items-center gap-1.5 text-purple-700 dark:text-purple-300 font-semibold text-[10px]">
                                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                                <span>Reference: {{ $q->reference_citation }}</span>
                                                            </div>
                                                        @endif
                                                        @if($q->explanation)
                                                            <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                                                                {{ $q->explanation }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @empty
                                        <div class="p-4 text-center text-slate-400 text-xs">
                                            Detailed answer history is recorded in your diocesan profile.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                <button 
                                    type="button" 
                                    wire:click="$set('showRallyReviewModal', false)" 
                                    class="px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs rounded-xl shadow-xs touch-press">
                                    Close Review
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- RALLY SCORE & ANSWERS REVIEW MODAL (GLOBAL TO ARENA) -->
    @if($showRallyReviewModal && $rallyReviewData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in overflow-y-auto">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl my-8">
                <!-- Header -->
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold uppercase">
                                Rally Official Result
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold uppercase">
                                {{ $rallyReviewData['rally']->scope_type ?? 'Diocese' }} Scope
                            </span>
                        </div>
                        <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white mt-1.5">
                            {{ $rallyReviewData['rally']->title }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Completed: {{ $rallyReviewData['completed_at'] ? \Carbon\Carbon::parse($rallyReviewData['completed_at'])->format('d M Y \a\t H:i') : 'Recorded' }}
                        </p>
                    </div>
                    <button wire:click="$set('showRallyReviewModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1">&times;</button>
                </div>

                <!-- Score Card Summary -->
                <div class="grid grid-cols-3 gap-2.5 text-center">
                    <div class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl">
                        <span class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 block">Final Score</span>
                        <span class="text-lg font-extrabold text-indigo-900 dark:text-indigo-100">{{ $rallyReviewData['score'] }} pts</span>
                    </div>
                    <div class="p-3 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 rounded-2xl">
                        <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block">Accuracy</span>
                        <span class="text-lg font-extrabold text-emerald-900 dark:text-emerald-100">
                            {{ $rallyReviewData['attempt'] ? "{$rallyReviewData['attempt']->correct_answers_count} / {$rallyReviewData['attempt']->total_questions}" : 'Submitted' }}
                        </span>
                    </div>
                    <div class="p-3 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/50 rounded-2xl">
                        <span class="text-[10px] uppercase font-bold text-amber-600 dark:text-amber-400 block">Time Taken</span>
                        <span class="text-lg font-extrabold text-amber-900 dark:text-amber-100">
                            {{ $rallyReviewData['attempt'] ? "{$rallyReviewData['attempt']->time_taken_seconds}s" : '--' }}
                        </span>
                    </div>
                </div>

                <!-- Questions & Answers Review Breakdown -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center justify-between">
                        <span>Questions &amp; Formational Answers</span>
                        <span class="text-[10px] text-slate-400 font-normal">1 Attempt Registered</span>
                    </h4>

                    <div class="space-y-3.5 max-h-96 overflow-y-auto pr-1">
                        @forelse($rallyReviewData['answers'] as $index => $answer)
                            @php $q = $answer->question; @endphp
                            @if($q)
                                <div class="p-4 rounded-2xl border text-xs space-y-2.5 transition-all {{ $answer->is_correct ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' }}">
                                    <!-- Question Header -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $answer->is_correct ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $q->category?->name ?? 'Doctrine' }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200' }}">
                                            {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                                        </span>
                                    </div>

                                    <!-- Question Text -->
                                    <p class="font-semibold text-slate-800 dark:text-slate-100 text-xs leading-relaxed">
                                        {{ $q->question_text }}
                                    </p>

                                    <!-- Options Breakdown -->
                                    <div class="grid grid-cols-1 gap-1.5 pt-1">
                                        @foreach($q->options ?? [] as $optKey => $optVal)
                                            @php
                                                $isUserChoice = ($answer->selected_option_key === $optKey);
                                                $isCorrectOption = ($q->correct_option_key === $optKey);
                                            @endphp
                                            <div class="p-2 rounded-xl border flex items-center justify-between text-[11px] {{ $isCorrectOption ? 'bg-emerald-100/80 dark:bg-emerald-900/40 border-emerald-300 dark:border-emerald-700 font-bold text-emerald-900 dark:text-emerald-100' : ($isUserChoice ? 'bg-rose-100/80 dark:bg-rose-900/40 border-rose-300 dark:border-rose-700 line-through text-rose-900 dark:text-rose-200' : 'bg-white dark:bg-[#121826] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300') }}">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-mono border {{ $isCorrectOption ? 'border-emerald-600 bg-emerald-600 text-white' : ($isUserChoice ? 'border-rose-600 bg-rose-600 text-white' : 'border-slate-300 text-slate-500') }}">
                                                        {{ $optKey }}
                                                    </span>
                                                    <span>{{ $optVal }}</span>
                                                </div>
                                                @if($isCorrectOption)
                                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold">&check; Correct Answer</span>
                                                @elseif($isUserChoice)
                                                    <span class="text-[10px] text-rose-700 dark:text-rose-300 font-bold">&times; Your Choice</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Scripture & Catechism References & Explanation -->
                                    @if($q->reference_citation || $q->explanation)
                                        <div class="p-2.5 bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl space-y-1 text-[11px]">
                                            @if($q->reference_citation)
                                                <div class="flex items-center gap-1.5 text-purple-700 dark:text-purple-300 font-semibold text-[10px]">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    <span>Reference: {{ $q->reference_citation }}</span>
                                                </div>
                                            @endif
                                            @if($q->explanation)
                                                <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                                                    {{ $q->explanation }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @empty
                            <div class="p-4 text-center text-slate-400 text-xs">
                                Detailed answer history is recorded in your diocesan profile.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Footer -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button 
                        type="button" 
                        wire:click="$set('showRallyReviewModal', false)" 
                        class="px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs rounded-xl shadow-xs touch-press">
                        Close Review
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
