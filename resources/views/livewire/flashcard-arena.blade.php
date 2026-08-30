<div class="space-y-4 pb-10">

    <!-- ARENA TOP BAR -->
    <div class="flex items-center justify-between pt-1">
        <a href="/study" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Exit Flashcards</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-purple-700 dark:text-purple-300">
                Card {{ min($currentIndex + 1, $totalCards) }} / {{ $totalCards }}
            </span>
        </div>
    </div>

    <!-- PROGRESS BAR -->
    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
        <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
             style="width: {{ $totalCards > 0 ? (($currentIndex) / $totalCards) * 100 : 0 }}%"></div>
    </div>

    @if(!$sessionCompleted && $currentCard)
        <!-- FLASHCARD CONTAINER -->
        <div class="min-h-[320px] flex flex-col justify-center">
            <div 
                wire:click="flipCard"
                class="w-full min-h-[300px] rounded-xl p-6 transition-colors cursor-pointer select-none flex flex-col justify-between border {{ $isFlipped ? 'bg-purple-50/50 dark:bg-purple-950/20 border-purple-300 dark:border-purple-800' : 'bg-white dark:bg-[#121826] border-slate-200 dark:border-slate-800' }}">

                <!-- TOP CARD BADGE & TAP HINT -->
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded {{ $isFlipped ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                        {{ $isFlipped ? 'Answer / Doctrinal Definition' : 'Question / Concept' }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Tap to flip
                    </span>
                </div>

                <!-- CARD MAIN CONTENT -->
                <div class="py-6 text-center space-y-3">
                    @if(!$isFlipped)
                        <h2 class="text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                            {{ $currentCard['front_text'] }}
                        </h2>
                    @else
                        <h2 class="text-sm font-semibold text-purple-900 dark:text-purple-200 leading-relaxed">
                            {{ $currentCard['back_text'] }}
                        </h2>
                    @endif
                </div>

                <!-- CARD CITATION FOOTER -->
                <div class="text-center pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 font-semibold">
                        {{ $currentCard['reference_citation'] ?? 'Diocese of Livingstone Youth Formation' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- SPACED REPETITION RATING BUTTONS (Active on Flip) -->
        <div class="space-y-2 pt-2">
            @if($isFlipped)
                <div class="grid grid-cols-3 gap-2">
                    <!-- AGAIN -->
                    <button 
                        type="button" 
                        wire:click="rateCard(1)" 
                        class="p-2.5 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900/50 text-center transition-colors">
                        <span class="text-xs font-bold block">Review Soon</span>
                        <span class="text-[10px] text-red-500 block">&lt; 1 day</span>
                    </button>

                    <!-- GOOD -->
                    <button 
                        type="button" 
                        wire:click="rateCard(2)" 
                        class="p-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 text-center transition-colors">
                        <span class="text-xs font-bold block">Good</span>
                        <span class="text-[10px] text-amber-600 block">3 days</span>
                    </button>

                    <!-- EASY -->
                    <button 
                        type="button" 
                        wire:click="rateCard(3)" 
                        class="p-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50 text-center transition-colors">
                        <span class="text-xs font-bold block">Mastered</span>
                        <span class="text-[10px] text-emerald-600 block">7 days</span>
                    </button>
                </div>
            @else
                <button 
                    type="button" 
                    wire:click="flipCard" 
                    class="w-full py-3 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 hover:border-purple-400 text-slate-800 dark:text-slate-200 font-semibold rounded-xl text-xs uppercase tracking-wider transition-colors">
                    Show Answer
                </button>
            @endif
        </div>
    @elseif($sessionCompleted)
        <!-- SESSION COMPLETED SUMMARY -->
        <div class="p-6 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-4 shadow-sm">
            <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>

            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Flashcard Drill Completed</h2>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-1">+{{ $xpEarned }} XP Earned &bull; Formation Streak Advanced</p>
            </div>

            <!-- REVIEW BREAKDOWN METRICS -->
            <div class="grid grid-cols-3 gap-2 py-2">
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-sm font-bold text-red-600 dark:text-red-400 block">{{ $againCount }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Review Soon</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400 block">{{ $goodCount }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Good</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 block">{{ $easyCount }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Mastered</span>
                </div>
            </div>

            <div class="space-y-2 pt-2 text-xs">
                <button 
                    type="button" 
                    wire:click="restartSession" 
                    class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                    Review Another Set &rarr;
                </button>
                <a href="/study" class="block w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors">
                    Return to Study Library
                </a>
            </div>
        </div>
    @else
        <!-- EMPTY STATE WHEN NO FLASHCARDS PUBLISHED -->
        <div class="p-8 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-3 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">No Flashcards Available</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                    There are currently no flashcards published in this track. Check back soon as diocesan leadership adds new material!
                </p>
            </div>
            <div class="pt-2">
                <a href="/study" class="inline-block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors">
                    Return to Study Library
                </a>
            </div>
        </div>
    @endif
</div>
