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
                    wire:click="$set('showDiocesanCompModal', true)"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors touch-press shadow-sm">
                    + Schedule Rally
                </button>
            </div>

            <!-- TABS SWITCHER -->
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
                <button 
                    type="button"
                    wire:click="setTab('bank')"
                    class="py-2.5 rounded-xl transition-all {{ $activeTab === 'bank' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Question Repository
                </button>
                <button 
                    type="button"
                    wire:click="setTab('rallies')"
                    class="py-2.5 rounded-xl transition-all {{ $activeTab === 'rallies' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Scheduled Rallies
                </button>
            </div>

            <!-- TAB 1: QUESTION BANK -->
            @if($activeTab === 'bank')
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuestion" 
                            placeholder="Search question text..." 
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        <select 
                            wire:model.live="selectedCategoryFilter"
                            class="px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-medium">
                            <option value="">All Tracks</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($questions as $q)
                            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase text-purple-600 dark:text-purple-400">{{ $q->category?->name ?? 'Doctrine' }} &bull; Level {{ $q->level }}</span>
                                    <button 
                                        type="button" 
                                        wire:click="toggleQuestionStatus('{{ $q->id }}')" 
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $q->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $q->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug">{{ $q->question_text }}</h4>
                                <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl text-[11px] text-slate-600 dark:text-slate-300 space-y-1">
                                    <div><strong>Correct Key:</strong> Option {{ $q->correct_option_key }}</div>
                                    <div class="text-[10px] text-slate-500 italic">{{ $q->explanation }}</div>
                                    @if($q->reference_citation)
                                        <div class="text-[10px] text-purple-600 dark:text-purple-400 font-bold">Citation: {{ $q->reference_citation }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- TAB 2: DIOCESAN RALLIES -->
            @if($activeTab === 'rallies')
                <div class="space-y-3">
                    @foreach($diocesanCompetitions as $comp)
                        <div class="p-5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold font-serif text-slate-900 dark:text-white text-base">{{ $comp->title }}</h4>
                                <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-lg font-bold text-[10px] uppercase border border-purple-200/60">
                                    {{ $comp->competition_type }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">{{ $comp->description }}</p>
                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-2 border-t border-slate-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">
                                <span>Rally PIN: <strong class="text-purple-600 font-bold text-xs">{{ $comp->rally_pin }}</strong></span>
                                <span>Time Limit: {{ $comp->time_limit_seconds }}s</span>
                                <span>Start: {{ $comp->start_time?->format('M d, Y') }}</span>
                                <span>Status: <strong class="text-emerald-600">{{ ucfirst($comp->status) }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- CREATE DIOCESAN COMPETITION MODAL -->
        @if($showDiocesanCompModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Schedule Diocesan Rally</h3>
                        <button wire:click="$set('showDiocesanCompModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>

                    <form wire:submit.prevent="createDiocesanCompetition" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Competition Title</label>
                            <input type="text" wire:model="newCompTitle" placeholder="e.g. 2026 Livingstone Diocesan Youth Bible Rally" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newCompDescription" rows="2" placeholder="Rules & guidelines..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
                                <input type="datetime-local" wire:model="newCompStartTime" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">End Date</label>
                                <input type="datetime-local" wire:model="newCompEndTime" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showDiocesanCompModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">
                                Launch Rally
                            </button>
                        </div>
                    </form>
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
