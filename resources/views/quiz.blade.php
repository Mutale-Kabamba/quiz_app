<x-layouts.app>
    <div class="py-2">
        <div class="flex items-center justify-between mb-4">
            <a href="/" class="flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Back to Tracks</span>
            </a>
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">{{ $category->name }}</span>
        </div>

        @livewire('quiz-session', [
            'categoryId' => $category->id,
            'level' => request('level', 1),
            'mode' => request('mode', 'ranked')
        ])
    </div>
</x-layouts.app>
