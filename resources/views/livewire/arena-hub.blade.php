<div class="space-y-5 pb-6">

    @if($successMessage)
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in">
            <span>{{ $successMessage }}</span>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN QUESTION BANK & COMPETITIONS HUB                      -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Question Bank &amp; Rallies</h2>
                    <p class="text-xs text-slate-500">Universal Question Repository &bull; Diocesan Competitions</p>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('showDiocesanCompModal', true)"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    + Schedule Rally
                </button>
            </div>

            <!-- TABS SWITCHER -->
            <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                <button 
                    type="button"
                    wire:click="setTab('bank')"
                    class="py-2 rounded-lg transition-colors {{ $activeTab === 'bank' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Question Bank
                </button>
                <button 
                    type="button"
                    wire:click="setTab('rallies')"
                    class="py-2 rounded-lg transition-colors {{ $activeTab === 'rallies' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                    Diocesan Competitions
                </button>
            </div>

            <!-- TAB 1: QUESTION BANK -->
            @if($activeTab === 'bank')
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchQuestion" 
                            placeholder="Search question bank..." 
                            class="w-full px-3 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        <select 
                            wire:model.live="selectedCategoryFilter"
                            class="px-2.5 py-2 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            <option value="">All Tracks</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        @foreach($questions as $q)
                            <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase text-purple-600 dark:text-purple-400">{{ $q->category?->name ?? 'Doctrine' }} &bull; Level {{ $q->level }}</span>
                                    <button 
                                        type="button" 
                                        wire:click="toggleQuestionStatus('{{ $q->id }}')" 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold {{ $q->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $q->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $q->question_text }}</h4>
                                <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg text-[11px] text-slate-600 dark:text-slate-300 space-y-0.5">
                                    <div><strong>Correct Key:</strong> Option {{ $q->correct_option_key }}</div>
                                    <div class="text-[10px] text-slate-400 italic">{{ $q->explanation }}</div>
                                    @if($q->reference_citation)
                                        <div class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Citation: {{ $q->reference_citation }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- TAB 2: DIOCESAN RALLIES -->
            @if($activeTab === 'rallies')
                <div class="space-y-2">
                    @foreach($diocesanCompetitions as $comp)
                        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $comp->title }}</h4>
                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px] uppercase">
                                    {{ $comp->competition_type }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $comp->description }}</p>
                            <div class="grid grid-cols-2 gap-1 text-[11px] pt-1 border-t border-slate-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">
                                <span>Rally PIN: <strong class="text-purple-600 font-bold">{{ $comp->rally_pin }}</strong></span>
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
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Schedule Diocesan Rally</h3>
                        <button wire:click="$set('showDiocesanCompModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="createDiocesanCompetition" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Competition Title</label>
                            <input type="text" wire:model="newCompTitle" placeholder="e.g. 2026 Livingstone Diocesan Bible Rally" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newCompDescription" rows="2" placeholder="Rules & guidelines..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
                                <input type="datetime-local" wire:model="newCompStartTime" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">End Date</label>
                                <input type="datetime-local" wire:model="newCompEndTime" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showDiocesanCompModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
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
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Parish Quizzes &amp; Rallies</h2>
                    <p class="text-xs text-slate-500">{{ $parish->name }} &bull; Live Battles &amp; Competitions</p>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('showParishQuizModal', true)"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    + Host Parish Quiz
                </button>
            </div>

            <!-- PARISH QUIZZES LIST -->
            <div class="space-y-2">
                @forelse($parishCompetitions as $pq)
                    <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $pq->title }}</h4>
                            <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px]">
                                PIN: {{ $pq->rally_pin }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">{{ $pq->description }}</p>
                        <div class="grid grid-cols-2 gap-1 text-[11px] pt-1 border-t border-slate-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">
                            <span>Track: {{ $pq->category?->name ?? 'General' }}</span>
                            <span>Time: {{ $pq->time_limit_seconds }}s</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No active parish quiz battles. Host one for your youth!</p>
                @endforelse
            </div>
        </div>

        <!-- HOST PARISH QUIZ MODAL -->
        @if($showParishQuizModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-sm w-full space-y-3 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Host Parish Live Quiz</h3>
                        <button wire:click="$set('showParishQuizModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <form wire:submit.prevent="createParishQuiz" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Quiz Title</label>
                            <input type="text" wire:model="newParishQuizTitle" placeholder="e.g. Parish Confirmation Quiz Battle" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                            <textarea wire:model="newParishQuizDescription" rows="2" placeholder="Instructions..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Curriculum Track</label>
                            <select wire:model="newParishQuizCategoryId" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                                <option value="">All Categories</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showParishQuizModal', false)" class="w-1/2 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                                Host Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH PRACTICE & COMPETE ARENA                                    -->
    <!-- ========================================================================= -->
    @else
        <!-- ARENA HEADER -->
        <div class="pt-1">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Quiz &amp; Competition Arena</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Practice self-paced questions or compete for your Parish</p>
        </div>

        <!-- 2-OPTION SEGMENTED SWITCHER: PRACTICE vs COMPETE -->
        <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
            <button 
                type="button"
                wire:click="setTab('practice')"
                class="py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $activeTab === 'practice' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                Practice
            </button>
            <button 
                type="button"
                wire:click="setTab('compete')"
                class="py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 {{ $activeTab === 'compete' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 font-bold border border-slate-200 dark:border-slate-700' : 'text-slate-500 hover:text-slate-900' }}">
                Compete
            </button>
        </div>

        <!-- PRACTICE MODE -->
        @if($activeTab === 'practice')
            <div class="space-y-4">
                <!-- DIFFICULTY TIER SELECTOR -->
                <div class="p-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
                    <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider block">Difficulty Level</span>
                    <div class="grid grid-cols-3 gap-1.5 text-xs font-medium">
                        <button type="button" wire:click="setLevel(1)" class="py-1.5 rounded-lg {{ $selectedLevel === 1 ? 'bg-purple-50 text-purple-700 border border-purple-300 font-semibold' : 'bg-slate-50 text-slate-600' }}">Level 1</button>
                        <button type="button" wire:click="setLevel(2)" class="py-1.5 rounded-lg {{ $selectedLevel === 2 ? 'bg-purple-50 text-purple-700 border border-purple-300 font-semibold' : 'bg-slate-50 text-slate-600' }}">Level 2</button>
                        <button type="button" wire:click="setLevel(3)" class="py-1.5 rounded-lg {{ $selectedLevel === 3 ? 'bg-purple-50 text-purple-700 border border-purple-300 font-semibold' : 'bg-slate-50 text-slate-600' }}">Level 3</button>
                    </div>
                </div>

                <!-- AVAILABLE PRACTICE QUIZZES -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Available Practice Quizzes</h3>
                    @foreach($categories as $cat)
                        <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $cat->name }}</h4>
                                <span class="text-[11px] text-slate-400">{{ $cat->questions_count }} Questions Available</span>
                            </div>
                            <a href="/quiz/play/{{ $cat->id }}?level={{ $selectedLevel }}&mode=practice" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs">
                                Practice &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- COMPETE MODE -->
        @if($activeTab === 'compete')
            <div class="space-y-4">
                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Livingstone Diocesan Ranked Arena</h3>
                    <p class="text-xs text-slate-500">Compete in high-stakes timed quizzes to boost your Parish standings.</p>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Live Youth Rally Lobby</h3>
                    <div class="flex gap-2">
                        <input type="text" wire:model="rallyPin" placeholder="Enter 6-digit PIN..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-mono">
                        <button type="button" wire:click="joinRally" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs whitespace-nowrap">
                            Join Rally
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Upcoming Deanery Rallies</h3>
                    <p class="text-xs text-slate-500">Check with your parish chairperson for upcoming scheduled rally PINs.</p>
                </div>
            </div>
        @endif
    @endif

</div>
