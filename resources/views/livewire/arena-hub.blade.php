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
                                <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/80">
                                    <button 
                                        type="button" 
                                        wire:click="openImportModalForTrack({{ $summary->category_id }})"
                                        class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Import to Track</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="openCreateQuestionModalForTrack({{ $summary->category_id }}, {{ $summary->level }})"
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
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
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Select File (CSV, XLSX, JSON)</label>
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
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full space-y-4 shadow-xl my-8">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $editCompId ? 'Edit Rally / Competition' : 'Schedule Diocesan Rally' }}
                        </h3>
                        <button wire:click="$set('showDiocesanCompModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rally Title *</label>
                            <input type="text" wire:model="newCompTitle" placeholder="e.g. Diocesan Youth Bible Rally 2026" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            @error('newCompTitle') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Description / Rules *</label>
                            <textarea wire:model="newCompDescription" rows="2" placeholder="Rules & guidelines..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl"></textarea>
                            @error('newCompDescription') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Category / Formation Track</label>
                            <select wire:model="newCompCategoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                                <option value="">All Categories (Mixed)</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Start Date &amp; Time *</label>
                                <input type="datetime-local" wire:model="newCompStartTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">End Date &amp; Time *</label>
                                <input type="datetime-local" wire:model="newCompEndTime" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Time Limit (Seconds)</label>
                                <input type="number" wire:model="newCompTimeLimit" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Question Count</label>
                                <input type="number" wire:model="newCompQuestionCount" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border rounded-xl">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <button wire:click="$set('showDiocesanCompModal', false)" class="px-3 py-1.5 text-xs text-slate-500">Cancel</button>
                        <button wire:click="saveCompetition" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-bold">
                            {{ $editCompId ? 'Save Changes' : 'Schedule Rally' }}
                        </button>
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
                    @foreach($categories as $cat)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs hover:border-purple-300 transition-colors shadow-sm">
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $cat->name }}</h4>
                                <span class="text-[11px] text-slate-400">{{ $cat->questions_count }} Questions Available</span>
                            </div>
                            <a href="/quiz/play/{{ $cat->id }}?level={{ $selectedLevel }}&mode=practice" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors touch-press shadow-sm">
                                Practice &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 4. COMPETE & RALLY MODE VIEW -->
        @if($activeTab === 'compete')
            <div class="space-y-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Competitive Play</span>
                    <h3 class="text-base font-bold font-serif text-slate-900 dark:text-white">Livingstone Diocesan Ranked Arena</h3>
                </div>

                <!-- RALLY PIN LOBBY CARD -->
                <div class="p-5 bg-gradient-to-br from-purple-900 via-indigo-950 to-slate-900 text-white border border-purple-800/40 rounded-2xl space-y-4 shadow-sm">
                    <div class="space-y-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-purple-200 font-bold uppercase text-[10px] border border-white/15">
                            Live Youth Rally Lobby
                        </span>
                        <h3 class="text-base font-bold font-serif">Enter Official Rally PIN</h3>
                        <p class="text-xs text-purple-200/80">Input the 6-digit access PIN provided by your Parish Chairperson or Diocesan Director.</p>
                    </div>

                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model="rallyPin" 
                            placeholder="Enter 6-digit PIN..." 
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-sm font-mono text-white placeholder-purple-300/50 focus:outline-none focus:border-purple-400">
                        <button 
                            type="button" 
                            wire:click="joinRally" 
                            class="px-5 py-3 bg-purple-500 hover:bg-purple-600 text-white font-bold rounded-xl text-xs whitespace-nowrap transition-colors touch-press shadow-sm">
                            Join Rally
                        </button>
                    </div>
                </div>

                <!-- DIOCESAN RALLY TOURNAMENT INFO -->
                <div class="p-5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Upcoming Deanery Rallies</span>
                        <span class="text-[11px] text-slate-400">Annual Tournament</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Livingstone Diocesan Youth Rally 2026</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Competitive ranked quizzes earn parish championship points. Top ranking youth represent their deaneries at the Diocesan Youth Rally.
                    </p>
                </div>
            </div>
        @endif
    @endif

</div>
