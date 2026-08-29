<div class="space-y-4 pb-8">

    <!-- TITLE & SEARCH -->
    <div class="space-y-2">
        <h2 class="text-xl font-black font-display text-white">Study Hub &amp; Flashcards</h2>
        <p class="text-xs text-slate-400">Catholic Catechism, Social Doctrine &amp; African Saints</p>
        
        <!-- Search Input -->
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search scripture, YOUCAT, DOCAT summaries..."
                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
        </div>
    </div>

    <!-- CATEGORY FILTER CHIPS (HORIZONTAL SCROLL) -->
    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-1">
        <button 
            type="button"
            wire:click="selectCategory(null)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ is_null($selectedCategoryId) ? 'bg-amber-500 text-slate-950 shadow-glow-gold font-black' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
            All Tracks
        </button>
        @foreach($categories as $cat)
            <button 
                type="button"
                wire:click="selectCategory({{ $cat->id }})"
                class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $selectedCategoryId === $cat->id ? 'bg-amber-500 text-slate-950 shadow-glow-gold font-black' : 'bg-slate-900 text-slate-400 border border-slate-800' }}">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    <!-- EXPANDABLE STUDY CARDS -->
    <div class="space-y-3">
        @forelse($notes as $note)
            <div class="p-4 rounded-3xl bg-slate-900 border border-slate-800/90 shadow-xl space-y-2 transition-all">
                <div class="flex items-start justify-between cursor-pointer" wire:click="openNote('{{ $note->id }}')">
                    <div class="flex-1 pr-2">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md">
                                {{ $note->reference_code ?? $note->category?->name }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-semibold">&bull; {{ $note->estimated_read_minutes }} min read</span>
                        </div>
                        <h3 class="text-sm font-bold text-white font-display leading-tight">{{ $note->title }}</h3>
                        @if($note->subheading)
                            <p class="text-xs text-slate-400 mt-0.5 leading-tight">{{ $note->subheading }}</p>
                        @endif
                    </div>
                    <button class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center flex-shrink-0 transition-transform {{ $activeNoteId === $note->id ? 'rotate-180 text-amber-400' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <!-- EXPANDED CONTENT BODY -->
                @if($activeNoteId === $note->id)
                    <div class="pt-3 border-t border-slate-800 text-xs text-slate-300 leading-relaxed space-y-3 animate-fade-in">
                        <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800/80">
                            {{ $note->content_body }}
                        </div>
                        <div class="flex items-center justify-between">
                            <a href="/quiz/{{ $note->category_id }}?mode=practice" class="text-xs font-black text-amber-400 hover:underline flex items-center gap-1">
                                <span>Practice Quiz on this Track &rarr;</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 rounded-3xl bg-slate-900 border border-slate-800 text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto text-xl font-bold">
                    📖
                </div>
                <h3 class="text-sm font-bold text-white">No Notes Found</h3>
                <p class="text-xs text-slate-400">Try searching for other doctrine terms or selecting a different track.</p>
            </div>
        @endforelse
    </div>
</div>
