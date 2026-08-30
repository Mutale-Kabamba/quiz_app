<div class="py-2" 
     x-data="{ 
        timeLeft: @entangle('timeRemaining').live, 
        isSubmitted: @entangle('isAnswerSubmitted').live,
        isFinished: @entangle('quizFinished').live,
        timeLimit: {{ $timeLimit }},
        timer: null,
        triggerHaptic() {
            if ('vibrate' in navigator) {
                navigator.vibrate(30);
            }
        },
        stopTimer() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        startTimer() {
            this.stopTimer();
            if (this.isSubmitted || this.isFinished) {
                return;
            }
            this.timer = setInterval(() => {
                if (this.isSubmitted || this.isFinished) {
                    this.stopTimer();
                    return;
                }
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                    if (this.timeLeft === 0) {
                        this.stopTimer();
                        this.triggerHaptic();
                        $wire.submitAnswer(null);
                    }
                } else {
                    this.stopTimer();
                }
            }, 1000);
        },
        selectAnswer(key) {
            if (this.isSubmitted || this.isFinished) return;
            this.stopTimer();
            this.triggerHaptic();
            $wire.submitAnswer(key);
        }
     }" 
     x-init="
        startTimer();
        $watch('isSubmitted', value => {
            if (value) {
                stopTimer();
            }
        });
        $watch('isFinished', value => {
            if (value) {
                stopTimer();
            }
        });
     "
     x-on:destroy="stopTimer()"
     @reset-timer.window="
        stopTimer();
        timeLeft = $event.detail.time || timeLimit;
        $nextTick(() => {
            startTimer();
        });
     ">

    @if(!$quizFinished && count($questions) > 0)
        <!-- 1. TOP APP BAR & LEVEL BADGE -->
        <div class="flex items-center justify-between mb-4">
            <a href="/quiz" class="flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors touch-press">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Exit</span>
            </a>

            <!-- Mode / Track Badge -->
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    {{ $challenge === 'today' ? 'Daily Challenge' : ucfirst($mode) . ' • Level ' . $level }}
                </span>
                <button 
                    type="button"
                    wire:click="$set('showReportModal', true)"
                    class="p-1 rounded-lg text-slate-400 hover:text-amber-500 transition-colors"
                    title="Report question issue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- 2. PROGRESS & COUNTDOWN TIMER BAR -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-4 mb-4 space-y-2.5 shadow-sm">
            <div class="flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">Question Progress</span>
                    <span class="font-black text-slate-900 dark:text-white text-base">
                        {{ $currentIndex + 1 }} <span class="text-slate-400 font-normal text-xs">/ {{ count($questions) }}</span>
                    </span>
                </div>

                <!-- Timer Indicator with Dynamic Liturgical Warning Colors -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-mono text-xs font-bold transition-all"
                     :class="{
                        'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800': timeLeft > 8,
                        'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 animate-pulse': timeLeft <= 8 && timeLeft > 4,
                        'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800 animate-pulse': timeLeft <= 4
                     }">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="timeLeft + 's'"></span>
                </div>

                <!-- Live Score Points -->
                <div class="text-right">
                    <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider">XP Score</span>
                    <span class="font-black text-purple-600 dark:text-purple-400 text-base">+{{ $totalScore }}</span>
                </div>
            </div>

            <!-- Linear Progress Indicator -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                     style="width: {{ (($currentIndex + 1) / count($questions)) * 100 }}%"></div>
            </div>
        </div>

        <!-- 3. IMMERSIVE QUESTION STATEMENT CARD -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-4 space-y-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="inline-block px-3 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-bold text-[10px] uppercase tracking-wider border border-purple-200/60 dark:border-purple-800/60">
                    {{ $questions[$currentIndex]['category_name'] }}
                </span>
                <span class="text-[11px] font-semibold text-slate-400">
                    Level {{ $questions[$currentIndex]['difficulty'] ?? 1 }}
                </span>
            </div>

            <!-- Editorial Question Typography (Hero Focus) -->
            <h2 class="text-lg sm:text-xl font-bold font-serif text-slate-900 dark:text-white leading-relaxed">
                {{ $questions[$currentIndex]['question_text'] }}
            </h2>

            <!-- 4 TOUCHABLE OPTION BUTTONS WITH DISTINCT HIERARCHY -->
            <div class="space-y-3 pt-2">
                @foreach($questions[$currentIndex]['options'] as $key => $optionText)
                    @php
                        $baseStyle = 'w-full p-4 rounded-2xl border text-left flex items-center justify-between text-xs transition-all touch-press font-medium ';
                        if (!$isAnswerSubmitted) {
                            $baseStyle .= 'bg-white dark:bg-[#121826] border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:border-purple-500 hover:bg-purple-50/30 dark:hover:bg-purple-950/20';
                        } else {
                            if ($key === $questions[$currentIndex]['correct_option_key']) {
                                $baseStyle .= 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-900 dark:text-emerald-100 font-bold shadow-sm';
                            } elseif ($selectedOption === $key) {
                                $baseStyle .= 'bg-red-50 dark:bg-red-950/40 border-red-500 text-red-900 dark:text-red-100 font-bold shadow-sm';
                            } else {
                                $baseStyle .= 'bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 text-slate-400 opacity-50';
                            }
                        }
                    @endphp

                    <button 
                        type="button"
                        @click="selectAnswer('{{ $key }}')" 
                        @disabled($isAnswerSubmitted)
                        class="{{ $baseStyle }}">
                        <span class="flex items-center gap-3.5">
                            <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 transition-colors {{ $isAnswerSubmitted && $key === $questions[$currentIndex]['correct_option_key'] ? 'bg-emerald-600 text-white shadow-sm' : ($isAnswerSubmitted && $selectedOption === $key ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                {{ $key }}
                            </span>
                            <span class="text-xs sm:text-sm leading-snug">{{ $optionText }}</span>
                        </span>

                        @if($isAnswerSubmitted)
                            @if($key === $questions[$currentIndex]['correct_option_key'])
                                <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @elseif($selectedOption === $key)
                                <div class="w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- 4. CATECHETICAL EXPLANATION & CITATION FEEDBACK -->
        @if($isAnswerSubmitted)
            <div class="bg-purple-50/90 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/60 rounded-2xl p-5 mb-4 space-y-2.5 animate-fade-in shadow-sm">
                <div class="flex items-center gap-2 text-purple-900 dark:text-purple-200 font-bold text-xs">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Teaching Note &amp; Catechetical Reference</span>
                </div>
                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $questions[$currentIndex]['explanation'] }}</p>
                @if(!empty($questions[$currentIndex]['reference_citation']))
                    <div class="inline-block px-2.5 py-1 bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        Citation: {{ $questions[$currentIndex]['reference_citation'] }}
                    </div>
                @endif
            </div>

            <!-- NEXT QUESTION BUTTON -->
            <button 
                type="button"
                wire:click="nextQuestion" 
                class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl transition-colors text-xs uppercase tracking-wider touch-press shadow-md">
                {{ $currentIndex + 1 === count($questions) ? 'Finish & View Formation Report →' : 'Next Question →' }}
            </button>
        @endif

    @elseif($quizFinished)
        <!-- 5. FINAL FORMATION REPORT & MASTERY INTELLIGENCE SCREEN -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 text-center space-y-5 shadow-sm">
            <div class="w-14 h-14 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mx-auto border border-purple-200 dark:border-purple-800 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                </svg>
            </div>
            
            <div class="space-y-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    Quiz Completed
                </span>
                <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white">Formation Report</h2>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">+{{ $xpEarned }} XP Earned &bull; Formation Streak Advanced</p>
            </div>

            <!-- STATS 3-COL GRID -->
            <div class="grid grid-cols-3 gap-2.5 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-center">
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Points</span>
                    <span class="text-base font-black text-purple-600 dark:text-purple-400">+{{ $totalScore }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Accuracy</span>
                    <span class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ $correctCount }}/{{ count($questions) }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Duration</span>
                    <span class="text-base font-black text-slate-700 dark:text-slate-300">{{ $totalTimeTaken }}s</span>
                </div>
            </div>

            <!-- RECOMMENDED FOCUS TOPIC -->
            @if(!empty($weakTopics))
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 text-left space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Recommended Review Topic
                    </span>
                    <p class="text-xs text-slate-700 dark:text-slate-300">
                        Review <span class="font-bold text-slate-900 dark:text-white">{{ $weakTopics[0]['name'] }}</span> to strengthen understanding before the next diocesan rally.
                    </p>
                </div>
            @endif

            <div class="space-y-2.5 pt-2 text-xs">
                @if($mode === 'ranked')
                    <a href="/leaderboard" class="block w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition-colors touch-press shadow-sm">
                        View Diocesan Leaderboard &rarr;
                    </a>
                @endif

                <a href="/quiz" class="block w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl transition-colors touch-press">
                    Practice Another Quiz
                </a>
                <a href="/" class="block w-full py-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-semibold">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center space-y-4">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No active questions available in this track yet.</p>
            <a href="/" class="inline-block px-4 py-2.5 bg-purple-600 text-white rounded-xl font-bold text-xs">Return Home</a>
        </div>
    @endif

    <!-- REPORT QUESTION DISPUTE MODAL -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-xs w-full space-y-3.5 shadow-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Report Question Issue</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-slate-600 text-base font-bold">&times;</button>
                </div>

                @if($reportSubmitted)
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 text-center font-bold">
                        Report submitted to Diocesan Reviewers!
                    </div>
                @else
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Issue Type</label>
                            <select wire:model="reportType" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                                <option value="wrong_answer">Wrong Answer Key</option>
                                <option value="typo">Typo in Question / Options</option>
                                <option value="bad_reference">Incorrect Scripture / YOUCAT citation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Notes</label>
                            <textarea wire:model="reportNotes" rows="2" placeholder="Briefly explain the issue..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                        </div>

                        <button 
                            type="button" 
                            wire:click="submitReport" 
                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                            Submit Report
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
