<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Overview Banner -->
        <div class="bg-gradient-to-r from-amber-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Catholic Formation Curriculum Matrix</h2>
                <p class="text-amber-100 text-sm mt-1">Real-time pedagogical gap analysis across tracks, topics, and levels.</p>
            </div>
            <div class="text-right">
                <span class="text-xs uppercase tracking-wider text-amber-200">Gaps Detected</span>
                <div class="text-3xl font-black text-white">{{ $analysisData['total_gaps'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Track Breakdown -->
        <div class="space-y-6">
            @foreach($analysisData['matrix'] ?? [] as $track)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                            <span>📜</span> {{ $track['track_name'] }}
                        </h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ count($track['topics']) }} Topics
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Topic / Category</th>
                                    <th class="px-6 py-3 text-center">Study Resources</th>
                                    <th class="px-6 py-3 text-center">Total Questions</th>
                                    <th class="px-6 py-3 text-center">Beginner / Easy</th>
                                    <th class="px-6 py-3 text-center">Advanced / Hard</th>
                                    <th class="px-6 py-3 text-right">Coverage Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($track['topics'] as $topic)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $topic['topic_name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $topic['category_name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium">{{ $topic['resource_count'] }}</td>
                                        <td class="px-6 py-4 text-center font-bold text-indigo-600 dark:text-indigo-400">{{ $topic['question_count'] }}</td>
                                        <td class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400">{{ $topic['beginner_count'] }}</td>
                                        <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400">{{ $topic['advanced_count'] }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ empty($topic['gaps']) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                                {{ $topic['health_badge'] }}
                                            </span>
                                            @if(!empty($topic['gaps']))
                                                <div class="text-[10px] text-rose-500 mt-1">
                                                    {{ implode(', ', $topic['gaps']) }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No topics configured in this track yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
