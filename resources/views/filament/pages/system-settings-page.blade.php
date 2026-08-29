<x-filament-panels::page>
    <form wire:submit="saveSettings" class="space-y-6">

        <!-- GENERAL DIOCESE BRANDING -->
        <x-filament::section>
            <x-slot name="heading">
                <span class="text-base font-bold">Diocesan Identity &amp; Platform Branding</span>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Diocese Name</label>
                    <input type="text" wire:model="dioceseName" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Youth Ministry Tagline</label>
                    <input type="text" wire:model="motto" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
            </div>
        </x-filament::section>

        <!-- GAMIFICATION & XP RULES -->
        <x-filament::section>
            <x-slot name="heading">
                <span class="text-base font-bold">Authoritative XP &amp; Reward Configuration</span>
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Lesson Mastery XP</label>
                    <input type="number" wire:model="xpLesson" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Daily Challenge XP</label>
                    <input type="number" wire:model="xpChallenge" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Flashcard Mastery XP</label>
                    <input type="number" wire:model="xpFlashcard" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Ranked Quiz Win XP</label>
                    <input type="number" wire:model="xpRankedQuiz" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-amber-500 focus:outline-none" />
                </div>
            </div>
        </x-filament::section>

        <!-- SAVE BUTTON -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm transition-all shadow-md">
                Save Diocesan Configuration
            </button>
        </div>

    </form>
</x-filament-panels::page>
