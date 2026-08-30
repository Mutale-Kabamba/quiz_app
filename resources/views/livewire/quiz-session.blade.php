<div class="max-w-xl mx-auto p-4 sm:p-6" 
     x-data="{ 
        timeLeft: @entangle('timeRemaining').live, 
        isSubmitted: @entangle('isAnswerSubmitted').live,
        isFinished: @entangle('quizFinished').live,
        timeLimit: {{ $timeLimit }},
        timer: null,
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
        <!-- Quiz Header / Status -->
        <div class="flex items-center justify-between bg-slate-900 text-white rounded-2xl p-4 shadow-lg mb-6">
            <div>
                <span class="text-xs uppercase tracking-wider text-amber-400 font-semibold">Question</span>
                <p class="text-xl font-extrabold">{{ $currentIndex + 1 }} <span class="text-slate-400 text-sm font-normal">/ {{ count($questions) }}</span></p>
            </div>
            
            <!-- Circular Countdown Timer -->
            <div class="flex flex-col items-center">
                <div class="relative w-12 h-12 flex items-center justify-center rounded-full border-4"
                     :class="timeLeft <= 5 ? 'border-red-500 text-red-500 animate-pulse' : 'border-amber-400 text-white'">
                    <span class="text-lg font-bold" x-text="timeLeft"></span>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-semibold">Score</span>
                <p class="text-xl font-extrabold text-green-400">{{ $totalScore }}</p>
            </div>
        </div>

        <!-- Question Card -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-100 mb-6">
            <h2 class="text-lg font-bold text-slate-800 leading-relaxed mb-6">
                {{ $questions[$currentIndex]['question_text'] }}
            </h2>

            <!-- Options List -->
            <div class="space-y-3">
                @foreach($questions[$currentIndex]['options'] as $key => $optionText)
                    @php
                        $buttonClass = 'w-full text-left p-4 rounded-2xl font-semibold border-2 transition-all flex items-center justify-between ';
                        if (!$isAnswerSubmitted) {
                            $buttonClass .= 'border-slate-200 hover:border-amber-500 hover:bg-amber-50 text-slate-700 active:scale-[0.98]';
                        } else {
                            if ($key === $questions[$currentIndex]['correct_option_key']) {
                                $buttonClass .= 'border-green-500 bg-green-50 text-green-800';
                            } elseif ($selectedOption === $key) {
                                $buttonClass .= 'border-red-500 bg-red-50 text-red-800';
                            } else {
                                $buttonClass .= 'border-slate-100 bg-slate-50 text-slate-400 opacity-60';
                            }
                        }
                    @endphp

                    <button 
                        type="button"
                        @click="selectAnswer('{{ $key }}')" 
                        @disabled($isAnswerSubmitted)
                        class="{{ $buttonClass }}">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-700">
                                {{ $key }}
                            </span>
                            <span>{{ $optionText }}</span>
                        </span>
                        
                        @if($isAnswerSubmitted)
                            @if($key === $questions[$currentIndex]['correct_option_key'])
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            @elseif($selectedOption === $key)
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Catechetical Citation & Explanation -->
        @if($isAnswerSubmitted)
            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5 mb-6">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-sm mb-2">
                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span>Doctrine & Reference Citation</span>
                </div>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $questions[$currentIndex]['explanation'] }}</p>
                @if(!empty($questions[$currentIndex]['reference_citation']))
                    <div class="mt-2 inline-block px-3 py-1 bg-amber-200 text-amber-900 rounded-full text-xs font-bold">
                        {{ $questions[$currentIndex]['reference_citation'] }}
                    </div>
                @endif
            </div>

            <!-- Next Button -->
            <button 
                type="button"
                wire:click="nextQuestion" 
                class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold rounded-2xl shadow-xl transition-all active:scale-[0.98]">
                {{ $currentIndex + 1 === count($questions) ? 'Complete Quiz' : 'Next Question' }}
            </button>
        @endif

    @elseif($quizFinished)
        <!-- Quiz Completion Screen -->
        <div class="bg-white rounded-3xl p-8 shadow-2xl border border-slate-100 text-center">
            <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
            </div>
            
            <h1 class="text-2xl font-black text-slate-800 mb-1">Quiz Completed!</h1>
            <p class="text-sm text-slate-500 mb-6">Catechism competition session finished</p>

            <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl mb-6">
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Score</span>
                    <span class="text-lg font-black text-amber-600">{{ $totalScore }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Correct</span>
                    <span class="text-lg font-black text-green-600">{{ $correctCount }}/{{ count($questions) }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Time</span>
                    <span class="text-lg font-black text-slate-700">{{ $totalTimeTaken }}s</span>
                </div>
            </div>

            <a href="/" class="block w-full py-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-2xl shadow-lg transition-all mb-3">
                Home / Dashboard
            </a>
        </div>
    @else
        <div class="bg-white rounded-3xl p-8 shadow-xl text-center">
            <p class="text-slate-600 font-medium">No active questions available for this category and level.</p>
        </div>
    @endif
</div>
