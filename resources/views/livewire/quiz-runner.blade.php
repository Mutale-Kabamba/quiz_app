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
        <div class="flex items-center justify-between mb-3">
            <a href="/" class="flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Exit</span>
            </a>

            <!-- Level & Mode Badge -->
            <div class="flex items-center gap-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $mode === 'ranked' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                    {{ ucfirst($mode) }} &bull; Level {{ $level }}
                </span>
                <button 
                    type="button"
                    wire:click="$set('showReportModal', true)"
                    class="p-1 rounded-lg text-slate-500 hover:text-amber-400 transition-colors"
                    title="Report question issue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </button>
            </div>
        </div>

        <!-- PROGRESS BAR & TIMER HEADER -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-4 shadow-xl mb-4">
            <div class="flex items-center justify-between mb-2.5">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Question Progress</span>
                    <p class="text-sm font-black text-white font-display">
                        {{ $currentIndex + 1 }} <span class="text-slate-500 font-normal">of {{ count($questions) }}</span>
                    </p>
                </div>

                <!-- Animated Circular Timer -->
                <div class="flex items-center gap-2">
                    <div class="relative w-11 h-11 flex items-center justify-center rounded-full border-4 transition-colors"
                         :class="{
                            'border-emerald-500 text-emerald-400 shadow-glow-emerald': timeLeft > 8,
                            'border-amber-500 text-amber-400 shadow-glow-gold': timeLeft <= 8 && timeLeft > 4,
                            'border-rose-500 text-rose-400 animate-pulse': timeLeft <= 4
                         }">
                        <span class="text-sm font-black font-display" x-text="timeLeft"></span>
                    </div>
                </div>

                <!-- Score Counter -->
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Points</span>
                    <p class="text-sm font-black text-amber-400 font-display">+{{ $totalScore }}</p>
                </div>
            </div>

            <!-- Linear Bar Indicator -->
            <div class="w-full bg-slate-950 h-2 rounded-full overflow-hidden p-0.5 border border-slate-800">
                <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 rounded-full transition-all duration-300"
                     style="width: {{ (($currentIndex + 1) / count($questions)) * 100 }}%"></div>
            </div>
        </div>

        <!-- QUESTION CARD -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl mb-4">
            <!-- Category Citation Pill -->
            <div class="mb-3">
                <span class="inline-block px-2.5 py-0.5 rounded-lg bg-amber-500/10 text-amber-400 font-extrabold text-[10px] uppercase tracking-wider">
                    {{ $questions[$currentIndex]['category_name'] }}
                </span>
            </div>

            <h2 class="text-base font-extrabold text-white leading-relaxed font-display mb-5">
                {{ $questions[$currentIndex]['question_text'] }}
            </h2>

            <!-- 4 TOUCHABLE OPTION BUTTONS -->
            <div class="space-y-2.5">
                @foreach($questions[$currentIndex]['options'] as $key => $optionText)
                    @php
                        $btnStyle = 'w-full p-3.5 rounded-2xl font-bold border-2 transition-all flex items-center justify-between text-left touch-press text-xs ';
                        if (!$isAnswerSubmitted) {
                            $btnStyle .= 'border-slate-800 bg-slate-950/80 hover:border-amber-500/60 hover:bg-slate-950 text-slate-200';
                        } else {
                            if ($key === $questions[$currentIndex]['correct_option_key']) {
                                $btnStyle .= 'border-emerald-500 bg-emerald-950/40 text-emerald-300 shadow-glow-emerald';
                            } elseif ($selectedOption === $key) {
                                $btnStyle .= 'border-rose-500 bg-rose-950/40 text-rose-300';
                            } else {
                                $btnStyle .= 'border-slate-800/60 bg-slate-950/40 text-slate-500 opacity-40';
                            }
                        }
                    @endphp

                    <button 
                        type="button"
                        wire:click="submitAnswer('{{ $key }}')" 
                        @click="triggerHaptic()"
                        @disabled($isAnswerSubmitted)
                        class="{{ $btnStyle }}">
                        <span class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black {{ $isAnswerSubmitted && $key === $questions[$currentIndex]['correct_option_key'] ? 'bg-emerald-500 text-slate-950' : ($isAnswerSubmitted && $selectedOption === $key ? 'bg-rose-500 text-white' : 'bg-slate-800 text-amber-400') }}">
                                {{ $key }}
                            </span>
                            <span class="leading-snug">{{ $optionText }}</span>
                        </span>

                        @if($isAnswerSubmitted)
                            @if($key === $questions[$currentIndex]['correct_option_key'])
                                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            @elseif($selectedOption === $key)
                                <svg class="w-5 h-5 text-rose-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- CATECHETICAL EXPLANATION & CITATION CARD -->
        @if($isAnswerSubmitted)
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-3xl p-4 mb-4 animate-fade-in">
                <div class="flex items-center gap-2 text-amber-300 font-extrabold text-xs mb-1.5">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span>Catechetical Reference &amp; Explanation</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">{{ $questions[$currentIndex]['explanation'] }}</p>
                @if(!empty($questions[$currentIndex]['reference_citation']))
                    <div class="mt-2 inline-block px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-lg text-[10px] font-black uppercase tracking-wider">
                        {{ $questions[$currentIndex]['reference_citation'] }}
                    </div>
                @endif
            </div>

            <!-- NEXT QUESTION BUTTON -->
            <button 
                type="button"
                wire:click="nextQuestion" 
                class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-2xl shadow-glow-gold transition-all text-sm touch-press">
                {{ $currentIndex + 1 === count($questions) ? 'Finish & Save Score' : 'Next Question &rarr;' }}
            </button>
        @endif

    @elseif($quizFinished)
        <!-- FINAL CELEBRATION / PODIUM SCREEN -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl text-center">
            <div class="w-20 h-20 bg-gradient-to-tr from-amber-500 to-yellow-400 text-slate-950 rounded-3xl flex items-center justify-center mx-auto mb-3 shadow-glow-gold">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
            </div>
            
            <h2 class="text-2xl font-black font-display text-white mb-0.5">Quiz Finished!</h2>
            <p class="text-xs text-slate-400 mb-5">Livingstone Diocese Catechism Challenge</p>

            <!-- STATS GRID -->
            <div class="grid grid-cols-3 gap-2.5 bg-slate-950 p-3.5 rounded-2xl border border-slate-800 mb-5">
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Points</span>
                    <span class="text-lg font-black text-amber-400 font-display">{{ $totalScore }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Accuracy</span>
                    <span class="text-lg font-black text-emerald-400 font-display">{{ $correctCount }}/{{ count($questions) }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Duration</span>
                    <span class="text-lg font-black text-slate-200 font-display">{{ $totalTimeTaken }}s</span>
                </div>
            </div>

            <div class="space-y-2">
                <a href="/leaderboard" class="block w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-black rounded-2xl shadow-glow-gold text-xs uppercase tracking-wider touch-press">
                    View Diocesan Leaderboard &rarr;
                </a>
                <a href="/" class="block w-full py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-2xl text-xs">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center">
            <p class="text-sm font-semibold text-slate-400">No active questions available in this study track yet.</p>
            <a href="/" class="mt-4 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-xl font-bold text-xs">Return Home</a>
        </div>
    @endif

    <!-- REPORT QUESTION DISPUTE MODAL -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 max-w-xs w-full shadow-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-white font-display">Report Question Issue</h3>
                    <button wire:click="$set('showReportModal', false)" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                @if($reportSubmitted)
                    <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-xs text-emerald-400 text-center font-bold">
                        Report submitted to Diocesan Admins for review!
                    </div>
                @else
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Issue Type</label>
                        <select wire:model="reportType" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white">
                            <option value="wrong_answer">Wrong Answer Key</option>
                            <option value="typo">Typo in Question / Options</option>
                            <option value="bad_reference">Incorrect Scripture / YOUCAT citation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Notes / Details</label>
                        <textarea wire:model="reportNotes" rows="2" placeholder="Briefly describe what is wrong..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white"></textarea>
                    </div>

                    <button 
                        type="button"
                        wire:click="submitReport" 
                        class="w-full py-2.5 bg-rose-500 text-white font-bold rounded-xl text-xs">
                        Submit Audit Report
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
