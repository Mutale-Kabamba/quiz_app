<div class="py-2" 
     x-data="{ 
        timeLeft: @entangle('timeRemaining'), 
        timeLimit: {{ $timeLimit }},
        timer: null,
        triggerHaptic() {
            if ('vibrate' in navigator) {
                navigator.vibrate(40);
            }
        },
        startTimer() {
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.timeLeft > 0 && !@json($isAnswerSubmitted) && !@json($quizFinished)) {
                    this.timeLeft--;
                } else if (this.timeLeft === 0 && !@json($isAnswerSubmitted)) {
                    clearInterval(this.timer);
                    this.triggerHaptic();
                    $wire.submitAnswer(null);
                }
            }, 1000);
        }
     }" 
     x-init="startTimer()"
     @reset-timer.window="timeLeft = $event.detail.time; startTimer()">

    @if(!$quizFinished && count($questions) > 0)
        <!-- TOP APP BAR & LEVEL BADGE -->
        <div class="flex items-center justify-between mb-4">
            <a href="/quiz" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Exit Quiz</span>
            </a>

            <!-- Mode / Track Badge -->
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    {{ $challenge === 'today' ? 'Daily Challenge' : ucfirst($mode) . ' • Level ' . $level }}
                </span>
                <button 
                    type="button"
                    wire:click="$set('showReportModal', true)"
                    class="p-1 rounded-md text-slate-400 hover:text-amber-500 transition-colors"
                    title="Report question issue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- PROGRESS & TIMER BAR (FLAT M3) -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 mb-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wider">Question Progress</span>
                    <span class="font-bold text-slate-900 dark:text-white text-sm">
                        {{ $currentIndex + 1 }} <span class="text-slate-400 font-normal">of {{ count($questions) }}</span>
                    </span>
                </div>

                <!-- Timer Indicator -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border font-mono text-xs font-bold transition-colors"
                     :class="{
                        'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800': timeLeft > 8,
                        'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800': timeLeft <= 8 && timeLeft > 4,
                        'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800': timeLeft <= 4
                     }">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="timeLeft + 's'"></span>
                </div>

                <!-- Score Points -->
                <div class="text-right">
                    <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wider">Score</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400 text-sm">+{{ $totalScore }}</span>
                </div>
            </div>

            <!-- Linear Progress Indicator -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                     style="width: {{ (($currentIndex + 1) / count($questions)) * 100 }}%"></div>
            </div>
        </div>

        <!-- QUESTION STATEMENT CARD -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 mb-4 space-y-4">
            <!-- Category Tag -->
            <span class="inline-block px-2.5 py-0.5 rounded bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 font-bold text-[10px] uppercase tracking-wider">
                {{ $questions[$currentIndex]['category_name'] }}
            </span>

            <h2 class="text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                {{ $questions[$currentIndex]['question_text'] }}
            </h2>

            <!-- 4 TOUCHABLE OPTION BUTTONS (ACCESSIBLE + VISUAL STATES) -->
            <div class="space-y-2.5">
                @foreach($questions[$currentIndex]['options'] as $key => $optionText)
                    @php
                        $baseStyle = 'w-full p-3.5 rounded-xl border text-left flex items-center justify-between text-xs transition-colors touch-press font-medium ';
                        if (!$isAnswerSubmitted) {
                            $baseStyle .= 'bg-white dark:bg-[#121826] border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:border-purple-400 hover:bg-purple-50/40 dark:hover:bg-purple-950/20';
                        } else {
                            if ($key === $questions[$currentIndex]['correct_option_key']) {
                                $baseStyle .= 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500 text-emerald-800 dark:text-emerald-200 font-semibold';
                            } elseif ($selectedOption === $key) {
                                $baseStyle .= 'bg-red-50 dark:bg-red-950/30 border-red-500 text-red-800 dark:text-red-200 font-semibold';
                            } else {
                                $baseStyle .= 'bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 text-slate-400 opacity-60';
                            }
                        }
                    @endphp

                    <button 
                        type="button"
                        wire:click="submitAnswer('{{ $key }}')" 
                        @click="triggerHaptic()"
                        @disabled($isAnswerSubmitted)
                        class="{{ $baseStyle }}">
                        <span class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold {{ $isAnswerSubmitted && $key === $questions[$currentIndex]['correct_option_key'] ? 'bg-emerald-600 text-white' : ($isAnswerSubmitted && $selectedOption === $key ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                {{ $key }}
                            </span>
                            <span class="leading-snug">{{ $optionText }}</span>
                        </span>

                        @if($isAnswerSubmitted)
                            @if($key === $questions[$currentIndex]['correct_option_key'])
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($selectedOption === $key)
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- CATECHETICAL EXPLANATION & CITATION FEEDBACK -->
        @if($isAnswerSubmitted)
            <div class="bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/50 rounded-xl p-4 mb-4 space-y-2">
                <div class="flex items-center gap-2 text-purple-800 dark:text-purple-300 font-bold text-xs">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Teaching Note &amp; Reference</span>
                </div>
                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $questions[$currentIndex]['explanation'] }}</p>
                @if(!empty($questions[$currentIndex]['reference_citation']))
                    <div class="inline-block px-2 py-0.5 bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded text-[10px] font-bold uppercase tracking-wider">
                        {{ $questions[$currentIndex]['reference_citation'] }}
                    </div>
                @endif
            </div>

            <!-- NEXT QUESTION BUTTON -->
            <button 
                type="button"
                wire:click="nextQuestion" 
                class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors text-xs uppercase tracking-wider touch-press">
                {{ $currentIndex + 1 === count($questions) ? 'Complete Quiz & View Results' : 'Next Question →' }}
            </button>
        @endif

    @elseif($quizFinished)
        <!-- FINAL SMART RESULTS & LEARNING INTELLIGENCE SCREEN -->
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-6 text-center space-y-4">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mx-auto border border-purple-200 dark:border-purple-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Quiz Completed</h2>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-1">+{{ $xpEarned }} XP Earned &bull; Formation Streak Advanced</p>
            </div>

            <!-- STATS GRID (FLAT) -->
            <div class="grid grid-cols-3 gap-2 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-lg border border-slate-200 dark:border-slate-800 text-center">
                <div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold uppercase">Points</span>
                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">+{{ $totalScore }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold uppercase">Accuracy</span>
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $correctCount }}/{{ count($questions) }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-semibold uppercase">Duration</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $totalTimeTaken }}s</span>
                </div>
            </div>

            <!-- FOCUS AREAS / WEAK TOPICS -->
            @if(!empty($weakTopics))
                <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 text-left space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300 flex items-center gap-1">
                        Recommended Review
                    </span>
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Consider reviewing <span class="font-semibold text-slate-900 dark:text-white">{{ $weakTopics[0]['name'] }}</span> to strengthen your understanding before the next competition.
                    </p>
                </div>
            @endif

            <div class="space-y-2 pt-2 text-xs">
                @if($mode === 'ranked')
                    <a href="/leaderboard" class="block w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors touch-press">
                        View Diocesan Leaderboard &rarr;
                    </a>
                @endif

                <a href="/quiz" class="block w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-lg transition-colors">
                    Try Another Quiz
                </a>
                <a href="/" class="block w-full py-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-medium">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-8 text-center space-y-3">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No active questions available in this track yet.</p>
            <a href="/" class="inline-block px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold text-xs">Return Home</a>
        </div>
    @endif

    <!-- REPORT QUESTION DISPUTE MODAL -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-xs w-full space-y-3 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Report Question Issue</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                @if($reportSubmitted)
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs text-emerald-700 dark:text-emerald-300 text-center font-semibold">
                        Report submitted to Diocesan Content Reviewers!
                    </div>
                @else
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Issue Type</label>
                        <select wire:model="reportType" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white">
                            <option value="wrong_answer">Wrong Answer Key</option>
                            <option value="typo">Typo in Question / Options</option>
                            <option value="bad_reference">Incorrect Scripture / YOUCAT citation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Notes</label>
                        <textarea wire:model="reportNotes" rows="2" placeholder="Briefly describe what is wrong..." class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-900 dark:text-white"></textarea>
                    </div>

                    <button 
                        type="button" 
                        wire:click="submitReport" 
                        class="w-full py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-xs transition-colors">
                        Submit Audit Report
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
