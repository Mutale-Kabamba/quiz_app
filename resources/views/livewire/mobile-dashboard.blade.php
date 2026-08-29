<div class="space-y-5 pb-6">

    <!-- ========================================================================= -->
    <!-- CASE 1: SUPER ADMIN EXECUTIVE HOME OVERVIEW                               -->
    <!-- ========================================================================= -->
    @if($user->isSuperAdmin())
        <div class="space-y-4">
            <!-- HEADER -->
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                            Diocesan Headquarters
                        </span>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-1">Livingstone Diocese Overview</h2>
                        <p class="text-xs text-slate-500">Super Administrator: {{ $user->name }}</p>
                    </div>
                    <a href="/diocese" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                        Command Hub &rarr;
                    </a>
                </div>

                <!-- QUICK SHORTCUTS -->
                <div class="grid grid-cols-4 gap-1.5 pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="/diocese" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">+ Parish</span>
                    </a>
                    <a href="/study" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Curriculum</span>
                    </a>
                    <a href="/quiz" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Questions</span>
                    </a>
                    <a href="/leaderboard" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Standings</span>
                    </a>
                </div>
            </div>

            <!-- DIOCESAN KPIS -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Diocesan Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($diocesanKpis['total_youth']) }}</span>
                        <span class="text-xs text-slate-500 font-medium">members</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-1">
                        {{ $diocesanKpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Parishes &amp; Territory</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ $diocesanKpis['total_parishes'] }}</span>
                        <span class="text-xs text-slate-500 font-medium">Parishes</span>
                    </div>
                    <span class="text-[11px] text-purple-600 font-semibold block mt-1">
                        {{ $deaneries->count() }} Deaneries
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Average Mastery</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $diocesanKpis['average_mastery'] }}%</span>
                        <span class="text-xs text-slate-500 font-medium">accuracy</span>
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-1">
                        {{ number_format($diocesanKpis['total_xp']) }} total XP
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formation Sessions</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($diocesanKpis['quizzes_completed']) }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-1">
                        {{ number_format($diocesanKpis['lessons_completed']) }} lessons completed
                    </span>
                </div>
            </div>

            <!-- DEANERIES HEALTH -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Deanery Performance Overview</h3>
                    <a href="/leaderboard" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">View Ranks &rarr;</a>
                </div>

                <div class="space-y-2">
                    @foreach($deaneries as $deanery)
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $deanery->name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $deanery->parishes_count }} Parishes</span>
                            </div>
                            <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded font-bold text-[10px]">
                                Code: {{ $deanery->code }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- SYSTEM AUDIT TRAIL STREAM -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Recent System Audit Stream</h3>
                    <a href="/diocese" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">Full Log &rarr;</a>
                </div>

                <div class="space-y-1.5 text-xs">
                    @foreach($recentAudits as $audit)
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-slate-900 dark:text-white block">{{ str_replace('_', ' ', ucwords($audit->action)) }}</span>
                                <span class="text-[10px] text-slate-400">By: {{ $audit->user?->name ?? 'System' }} &bull; {{ $audit->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="text-[10px] font-mono text-slate-500 bg-slate-200 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                {{ $audit->ip_address ?? '127.0.0.1' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 2: PARISH ADMIN (CHAIRPERSON) EXECUTIVE HOME OVERVIEW                -->
    <!-- ========================================================================= -->
    @elseif($user->isChairperson())
        <div class="space-y-4">
            <!-- HEADER -->
            <div class="p-4 rounded-xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded">
                            Parish Youth Ministry
                        </span>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight mt-1">{{ $parish->name }}</h2>
                        <p class="text-xs text-slate-500">Chairperson: {{ $user->name }} &bull; {{ $parish->deanery?->name }}</p>
                    </div>
                    <a href="/parish" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                        Manage &rarr;
                    </a>
                </div>

                <!-- QUICK SHORTCUTS -->
                <div class="grid grid-cols-4 gap-1.5 pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="/parish" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">+ Youth</span>
                    </a>
                    <a href="/study" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Curriculum</span>
                    </a>
                    <a href="/quiz" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Quizzes</span>
                    </a>
                    <a href="/leaderboard" class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <span class="text-[10px] font-bold block">Ranks</span>
                    </a>
                </div>
            </div>

            <!-- PARISH KPIS -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Parish Youth</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $parishKpis['total_youth'] }}</span>
                        <span class="text-xs text-slate-500 font-medium">Members</span>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-1">
                        {{ $parishKpis['active_this_week'] }} active this week
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Parish Engagement</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ $parishKpis['engagement_level'] }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-1">
                        {{ $parishKpis['engagement_pct'] }}% participation
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formation Points</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($parishKpis['total_parish_xp']) }}</span>
                        <span class="text-xs text-slate-500 font-medium">XP</span>
                    </div>
                    <span class="text-[11px] text-amber-600 font-semibold block mt-1">
                        Avg Score: {{ $parishKpis['avg_quiz_score'] }} pts
                    </span>
                </div>

                <div class="p-3.5 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Quizzes Answered</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $parishKpis['quizzes_completed'] }}</span>
                    </div>
                    <span class="text-[11px] text-slate-500 block mt-1">
                        {{ $parishKpis['lessons_completed'] }} lessons finished
                    </span>
                </div>
            </div>

            <!-- ATTENTION REQUIRED ALERT BOX -->
            <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2.5">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Parish Action Items &bull; Attention Required
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Inactive Youth (14+ days)</span>
                            <span class="text-[11px] text-slate-500">{{ $parishKpis['inactive_youth_count'] }} youth need pastoral motivation</span>
                        </div>
                        <a href="/parish" class="text-purple-600 font-bold text-[11px] hover:underline">View &rarr;</a>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Pending Registrations</span>
                            <span class="text-[11px] text-slate-500">{{ $parishKpis['pending_approvals_count'] }} waiting for chairperson sign-off</span>
                        </div>
                        <a href="/parish" class="text-purple-600 font-bold text-[11px] hover:underline">Review &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- CASE 3: YOUTH LEARNER FORMATION DASHBOARD                                 -->
    <!-- ========================================================================= -->
    @else
        <!-- GREETING & FORMATION STATUS -->
        <div class="pt-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        {{ now()->format('l, M j') }}
                    </p>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight mt-0.5">
                        Peace be with you, {{ explode(' ', $user->name)[0] }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Catholic Youth Formation &bull; {{ $user->parish?->name ?? 'Livingstone Diocese' }}
                    </p>
                </div>

                <!-- Level & XP Micro-Badge -->
                <div class="text-right flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>Level {{ $currentLevel }}</span>
                    </span>
                    <span class="text-[10px] text-slate-400 block mt-1 font-medium">{{ number_format($currentXp) }} XP</span>
                </div>
            </div>

            <!-- Level XP Linear Progress Bar -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-3 border border-slate-200/50 dark:border-slate-700/50">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                     style="width: {{ $levelProgressPercentage }}%"></div>
            </div>
        </div>

        <!-- FORMATION PROGRESS SECTION -->
        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Formation Progress</h3>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg">
                    <span class="text-[10px] text-slate-400 block">Formation Level</span>
                    <span class="font-bold text-slate-900 dark:text-white text-sm">Level {{ $currentLevel }}</span>
                </div>
                <div class="p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg">
                    <span class="text-[10px] text-slate-400 block">Total XP</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400 text-sm">{{ number_format($currentXp) }} XP</span>
                </div>
            </div>
        </div>

        <!-- TODAY'S FORMATION ("LEARN IN 5 MINUTES") -->
        @if($microLesson)
            <div class="bg-white dark:bg-[#121826] border-2 border-purple-500/30 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-bold uppercase text-[10px]">
                        Today's Formation
                    </span>
                    <span class="text-slate-400 text-[11px]">Learn in 5 Minutes</span>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ $microLesson->title }}</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">{{ $microLesson->summary ?? $microLesson->subheading }}</p>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] text-slate-400 font-medium">Estimated: 5 mins</span>
                    <a href="/lesson/{{ $microLesson->id }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">
                        {{ $microLessonCompleted ? 'Re-read Lesson' : 'Start Micro-Lesson' }}
                    </a>
                </div>
            </div>
        @endif

        <!-- RALLY PREPARATION ("Prepare for the Rally") -->
        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 block">Prepare for the Rally</span>
            <h4 class="font-bold text-slate-900 dark:text-white text-sm">Livingstone Diocesan Youth Rally 2026</h4>
            <p class="text-xs text-slate-500">Train with high-yield Catholic questions to represent your parish.</p>
            <div class="pt-1">
                <a href="/quiz?tab=compete" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold block text-center">
                    Enter Rally Lobby &rarr;
                </a>
            </div>
        </div>

        <!-- PARISH COMMUNITY CHALLENGE ("Parish Formation Challenge") -->
        <div class="p-4 bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Parish Formation Challenge</span>
            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $activeParishChallenge?->title ?? '5,000 XP Parish Collective Sprint' }}</h4>
            <p class="text-xs text-slate-500">{{ $activeParishChallenge?->description ?? 'Work together with all youth in your parish to earn bonus XP!' }}</p>
        </div>

        <!-- CONTINUE LEARNING -->
        @if($continueLesson)
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Continue Formation</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $continueLesson->title }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $continueLesson->category?->name }}</p>
                <div class="pt-2">
                    <a href="/lesson/{{ $continueLesson->id }}" class="w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-semibold block text-center">
                        Resume Lesson &rarr;
                    </a>
                </div>
            </div>
        @endif
    @endif

</div>
