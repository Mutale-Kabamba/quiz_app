<div class="space-y-6 pb-20">

    <!-- TOAST NOTIFICATIONS -->
    @if($successMessage)
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs text-emerald-800 dark:text-emerald-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    @if($errorMessage)
        <div class="p-3.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-xs text-red-800 dark:text-red-200 font-semibold flex items-center justify-between animate-fade-in shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', null)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. PERSONAL FORMATION IDENTITY CARD                                       -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 text-center space-y-4 shadow-sm">
        
        <!-- AVATAR CONTAINER WITH CAMERA ACTION -->
        <div class="relative inline-block mx-auto">
            <div class="w-22 h-22 rounded-full overflow-hidden border-2 border-purple-300 dark:border-purple-700 shadow-sm mx-auto aspect-square flex items-center justify-center bg-purple-50 dark:bg-purple-950/40">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover aspect-square">
                @else
                    <span class="text-purple-700 dark:text-purple-300 font-black text-2xl">{{ $user->initials }}</span>
                @endif
            </div>

            <button 
                type="button" 
                wire:click="$set('showAvatarModal', true)"
                class="absolute bottom-0 right-0 p-2 rounded-full bg-purple-600 hover:bg-purple-700 text-white border-2 border-white dark:border-[#121826] shadow-sm transition-transform active:scale-95 flex items-center justify-center aspect-square"
                title="Change Photo">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        <div class="space-y-1">
            <h2 class="text-xl font-bold font-serif text-slate-900 dark:text-white">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->parish?->name ?? 'Livingstone Diocese' }} &bull; {{ $user->parish?->deanery?->name ?? 'Diocesan Youth' }}</p>
            
            <div class="flex items-center justify-center gap-2 pt-1">
                <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    Level {{ $currentLevel }} &bull; {{ number_format($currentXp) }} XP
                </span>
                <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    {{ $averageMastery }}% Accuracy
                </span>
            </div>
        </div>

        <!-- PROFILE COMPLETION GAUGE -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400 font-semibold text-[11px]">Profile Completion</span>
                <span class="text-purple-700 dark:text-purple-400 font-black text-xs">{{ $user->profile_completion_percentage }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300" style="width: {{ $user->profile_completion_percentage }}%"></div>
            </div>
        </div>

        <!-- PROFILE ACTIONS: EDIT PROFILE & THEME SWITCH -->
        <div class="space-y-3 pt-1">
            <!-- 1. THEME SWITCHER -->
            <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/60 dark:border-slate-800 text-left space-y-1.5">
                <div class="flex items-center justify-between text-[11px] font-bold">
                    <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Appearance &amp; Theme</span>
                    <span class="text-purple-600 dark:text-purple-400 capitalize text-[10px]" x-text="currentTheme"></span>
                </div>
                <div class="grid grid-cols-3 gap-1 p-1 bg-slate-200/60 dark:bg-slate-800 rounded-lg text-xs font-bold">
                    <button 
                        type="button" 
                        @click="setTheme('light')"
                        :class="currentTheme === 'light' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="py-1.5 rounded-md flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                        <span>☀️</span>
                        <span>Light</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setTheme('dark')"
                        :class="currentTheme === 'dark' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="py-1.5 rounded-md flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                        <span>🌙</span>
                        <span>Dark</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setTheme('system')"
                        :class="currentTheme === 'system' ? 'bg-white dark:bg-[#121826] text-purple-700 dark:text-purple-300 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="py-1.5 rounded-md flex items-center justify-center gap-1 transition-all touch-press text-[11px]">
                        <span>💻</span>
                        <span>Auto</span>
                    </button>
                </div>
            </div>

            <!-- 2. PROFILE MANAGEMENT BUTTONS -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button 
                    type="button" 
                    wire:click="$set('showEditProfileModal', true)"
                    class="py-2.5 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors touch-press flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span>Edit Profile</span>
                </button>

                <button 
                    type="button" 
                    @click="document.getElementById('account-security')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                    class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors touch-press flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Security</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. FORMATION MASTERY & METRICS (RICH MINIMALISM)                          -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-sm">
        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase text-purple-700 dark:text-purple-400 tracking-wider block">Formation Rank</span>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                    @php
                        $levelTitle = match($currentLevel) {
                            1 => 'Seeker of Truth',
                            2 => 'Faithful Disciple',
                            3 => 'Catechetical Scholar',
                            4 => 'Scripture Pillar',
                            5 => 'Diocesan Evangelist',
                            default => 'Youth Champion',
                        };
                    @endphp
                    Level {{ $currentLevel }}: {{ $levelTitle }}
                </h3>
            </div>
            <span class="text-xs font-black text-purple-600 dark:text-purple-400">{{ number_format($currentXp) }} XP</span>
        </div>

        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
            <div class="h-full bg-purple-600 dark:bg-purple-500 rounded-full transition-all duration-300"
                 style="width: {{ $levelProgressPercentage }}%"></div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
            <span>To Level {{ $currentLevel + 1 }}</span>
            <span>{{ number_format(max(0, $nextThreshold - $currentXp)) }} XP needed</span>
        </div>

        <!-- 4-GRID FORMATION METRICS -->
        <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Lessons Mastered</span>
                <span class="text-lg font-black text-slate-900 dark:text-white block mt-0.5">{{ $completedLessonsCount }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Flashcards Mastered</span>
                <span class="text-lg font-black text-slate-900 dark:text-white block mt-0.5">{{ $masteredFlashcardsCount }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Quizzes Completed</span>
                <span class="text-lg font-black text-slate-900 dark:text-white block mt-0.5">{{ $totalQuizzes }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Formation Streak</span>
                <span class="text-lg font-black text-amber-600 dark:text-amber-400 block mt-0.5">{{ $user->current_streak ?? 0 }} Days</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. CATHOLIC ACHIEVEMENTS BADGE GALLERY (SVG ICONS - NO EMOJIS)           -->
    <!-- ========================================================================= -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                Formation Achievements
            </h3>
            <span class="text-[11px] text-purple-600 dark:text-purple-400 font-bold">
                {{ count($unlockedAchievementIds) }} Unlocked
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            @foreach($allAchievements as $ach)
                @php
                    $isUnlocked = in_array($ach->id, $unlockedAchievementIds);
                @endphp
                <div class="p-3.5 rounded-2xl border transition-all {{ $isUnlocked ? 'bg-white dark:bg-[#121826] border-purple-300 dark:border-purple-800 shadow-sm' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 {{ $isUnlocked ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }}">
                            <!-- Catholic Achievement Icon: Cross / Bible / Chalice / Flame -->
                            @if(str_contains(strtolower($ach->title), 'scripture') || str_contains(strtolower($ach->title), 'bible') || str_contains(strtolower($ach->title), 'reader'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @elseif(str_contains(strtolower($ach->title), 'streak') || str_contains(strtolower($ach->title), 'flame') || str_contains(strtolower($ach->title), 'fire'))
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.123 1.956-1.517 3.085-.395 1.13-.578 2.21-.578 2.867 0 .54.1 1.053.284 1.518-1.077-.92-1.762-2.31-1.762-3.868 0-.447-.07-1.002-.27-1.436a1 1 0 00-1.79.232c-.52 1.488-.89 3.25-.89 4.904 0 4.418 3.582 8 8 8s8-3.582 8-8c0-3.64-2.022-6.84-5.065-8.796z" clip-rule="evenodd" /></svg>
                            @elseif(str_contains(strtolower($ach->title), 'rally') || str_contains(strtolower($ach->title), 'champion') || str_contains(strtolower($ach->title), 'master'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                            @else
                                <!-- Catholic Cross Symbol -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/></svg>
                            @endif
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $ach->title }}</h4>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block line-clamp-2">{{ $ach->description }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. PERSONAL ACCOUNT DETAILS                                               -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                Personal Information
            </h3>
            <button wire:click="$set('showEditProfileModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">
                Edit
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold block">Full Name</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold block">Phone Number</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->phone }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold block">Email Address</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->email ?? 'Not provided' }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold block">Date of Birth</span>
                <span class="text-slate-900 dark:text-white font-medium block mt-0.5">{{ $user->dob ? $user->dob->format('M d, Y') : 'Not set' }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. PARISH MEMBERSHIP & ROSTER TRANSFER                                    -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                Parish Community
            </h3>
            <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-2.5 py-0.5 rounded-full border border-purple-200/60">
                {{ $user->parish?->deanery?->name ?? 'Livingstone Deanery' }}
            </span>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold block">Registered Parish</span>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">{{ $user->parish?->name ?? 'Diocesan Youth' }}</h4>
            </div>

            @if($pendingTransfer)
                <span class="px-3 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 text-[11px] font-bold">
                    Transfer Pending
                </span>
            @else
                <button 
                    type="button" 
                    wire:click="$set('showParishModal', true)"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors touch-press">
                    Request Transfer
                </button>
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. ACCOUNT SECURITY                                                       -->
    <!-- ========================================================================= -->
    <div id="account-security" 
         x-data="{
             registeringBiometrics: false,
             biometricError: null,

             init() {
                 window.addEventListener('biometric-enrolled-on-device', (e) => {
                     const payload = Array.isArray(e.detail) ? e.detail[0] : e.detail;
                     localStorage.setItem('catholic_youth_biometric_auth', JSON.stringify(payload));
                 });

                 window.addEventListener('biometric-revoked-on-device', () => {
                     localStorage.removeItem('catholic_youth_biometric_auth');
                 });
             },

             async registerBiometricsOnDevice() {
                 this.registeringBiometrics = true;
                 this.biometricError = null;

                 try {
                     let credentialId = null;

                     // Trigger phone hardware fingerprint/FaceID registration prompt
                     if (window.PublicKeyCredential && navigator.credentials && navigator.credentials.create) {
                         try {
                             const challenge = new Uint8Array(32);
                             window.crypto.getRandomValues(challenge);
                             const userIdBuffer = new Uint8Array(16);
                             window.crypto.getRandomValues(userIdBuffer);

                             const credential = await navigator.credentials.create({
                                 publicKey: {
                                     challenge: challenge,
                                     rp: { name: 'Catholic Youth Ministry', id: window.location.hostname },
                                     user: {
                                         id: userIdBuffer,
                                         name: '{{ $user->email ?? $user->phone }}',
                                         displayName: '{{ $user->name }}'
                                     },
                                     pubKeyCredParams: [
                                         { type: 'public-key', alg: -7 },
                                         { type: 'public-key', alg: -257 }
                                     ],
                                     authenticatorSelection: {
                                         authenticatorAttachment: 'platform',
                                         userVerification: 'required'
                                     },
                                     timeout: 60000,
                                     attestation: 'none'
                                 }
                             });

                             if (credential && credential.id) {
                                 credentialId = credential.id;
                             }
                         } catch (err) {
                             if (err.name === 'NotAllowedError' || err.name === 'AbortError') {
                                 this.registeringBiometrics = false;
                                 this.biometricError = 'Fingerprint registration was cancelled or unverified. Please scan a valid registered finger.';
                                 return;
                             }
                             console.warn('WebAuthn registration fallback notice:', err);
                         }
                     }

                     // Trigger NativePHP mobile biometric prompt if active
                     if (window.Native && window.Native.biometric) {
                         try {
                             await window.Native.biometric.prompt();
                         } catch (ne) {
                             console.warn('Native biometric prompt notice:', ne);
                         }
                     }

                     const enrolledPayload = await $wire.enableBiometrics(credentialId);
                     if (enrolledPayload) {
                         localStorage.setItem('catholic_youth_biometric_auth', JSON.stringify(enrolledPayload));
                     }
                     this.registeringBiometrics = false;
                 } catch (e) {
                     this.registeringBiometrics = false;
                     this.biometricError = 'Failed to register biometrics on this device.';
                 }
             }
         }"
         class="scroll-mt-20 p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-4 shadow-sm transition-all">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                Account Security &amp; Biometrics
            </h3>
            <button wire:click="$set('showPasswordModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">
                Change Password
            </button>
        </div>

        <!-- Biometric Registration Error Banner -->
        <div x-show="biometricError" x-cloak class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 rounded-xl text-xs text-red-700 dark:text-red-300 leading-snug flex items-start gap-2 animate-fade-in">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="biometricError"></span>
        </div>

        <div class="space-y-4 text-xs">
            <!-- 1. Password Security -->
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-900 dark:text-white font-semibold block">Password Security</span>
                    <span class="text-[11px] text-slate-400">
                        {{ $user->last_password_changed_at ? 'Changed ' . $user->last_password_changed_at->diffForHumans() : 'Protected with account password' }}
                    </span>
                </div>
                <button wire:click="$set('showPasswordModal', true)" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold touch-press transition-colors">
                    Update
                </button>
            </div>

            <!-- 2. Biometric Authentication -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                <div class="space-y-0.5 max-w-[70%]">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.5"/>
                        </svg>
                        <span class="text-slate-900 dark:text-white font-semibold">Biometric Login</span>
                    </div>
                    <span class="text-[11px] text-slate-400 block leading-tight">
                        Instant sign-in using Face ID, Touch ID, or Fingerprint on this device.
                    </span>
                </div>

                @if($biometricEnabled)
                    <button 
                        type="button" 
                        wire:click="disableBiometrics" 
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900 text-xs font-bold transition-colors touch-press">
                        Disable
                    </button>
                @else
                    <button 
                        type="button" 
                        @click="registerBiometricsOnDevice()"
                        :disabled="registeringBiometrics"
                        class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-colors shadow-sm touch-press disabled:opacity-75 flex items-center gap-1.5">
                        <span x-show="!registeringBiometrics">Enable</span>
                        <span x-show="registeringBiometrics" x-cloak class="flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Scanning...</span>
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. PREFERENCES & PRIVACY                                                  -->
    <!-- ========================================================================= -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                Preferences &amp; Privacy
            </h3>
            <button wire:click="$set('showPreferencesModal', true)" class="text-xs text-purple-600 dark:text-purple-400 font-bold hover:underline">
                Customize
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Formation Alerts</span>
                <span class="text-slate-900 dark:text-white font-semibold mt-0.5 block">{{ $notifyFormation ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Leaderboard Name</span>
                <span class="text-slate-900 dark:text-white font-semibold mt-0.5 block">{{ $showNameInRankings ? 'Public' : 'Anonymous' }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. SIGNOUT ACTIONS                                                        -->
    <!-- ========================================================================= -->
    <div class="pt-2 space-y-2">
        <button 
            type="button" 
            wire:click="logout" 
            class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-2xl transition-colors touch-press">
            Sign Out
        </button>

        <button 
            type="button" 
            wire:click="$set('showDeactivateModal', true)" 
            class="w-full py-2 text-center text-red-600 dark:text-red-400 text-xs font-bold hover:underline">
            Deactivate Account
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: AVATAR UPLOAD MODAL                                              -->
    <!-- ========================================================================= -->
    @if($showAvatarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-xs w-full space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Profile Photo</h3>
                    <button wire:click="$set('showAvatarModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-purple-300 dark:border-purple-700 shadow-sm mx-auto aspect-square flex items-center justify-center bg-purple-50 dark:bg-purple-950/40">
                    @if ($avatarFile)
                        <img src="{{ $avatarFile->temporaryUrl() }}" class="w-full h-full rounded-full object-cover aspect-square">
                    @elseif ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover aspect-square">
                    @else
                        <span class="text-purple-700 dark:text-purple-300 font-black text-2xl">{{ $user->initials }}</span>
                    @endif
                </div>

                <div class="space-y-3">
                    <input 
                        type="file" 
                        wire:model="avatarFile" 
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 dark:file:bg-purple-950/40 dark:file:text-purple-300">
                    
                    @error('avatarFile') 
                        <span class="text-[10px] text-red-500 font-medium block">{{ $message }}</span> 
                    @enderror

                    <div wire:loading wire:target="avatarFile" class="text-xs text-purple-600 flex items-center gap-1.5 justify-center">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Uploading image...</span>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    @if($avatarFile)
                        <button 
                            type="button" 
                            wire:click="uploadAvatar" 
                            class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                            Save Photo
                        </button>
                    @endif

                    @if($user->avatar_path)
                        <button 
                            type="button" 
                            wire:click="removeAvatar" 
                            class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-xl text-xs transition-colors">
                            Remove Photo
                        </button>
                    @endif

                    <button 
                        type="button" 
                        wire:click="$set('showAvatarModal', false)" 
                        class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: EDIT PROFILE MODAL                                               -->
    <!-- ========================================================================= -->
    @if($showEditProfileModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Edit Personal Info</h3>
                    <button wire:click="$set('showEditProfileModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="saveProfile" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('name') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (+260...)</label>
                        <input type="text" wire:model="phone" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('phone') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('email') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Date of Birth</label>
                            <input type="date" wire:model="dob" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                            <select wire:model="gender" class="w-full px-2.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showEditProfileModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: CHANGE PASSWORD MODAL                                            -->
    <!-- ========================================================================= -->
    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-xs w-full space-y-3.5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Change Password</h3>
                    <button wire:click="$set('showPasswordModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="changePassword" class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Current Password</label>
                        <input type="password" wire:model="currentPassword" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('currentPassword') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">New Password</label>
                        <input type="password" wire:model="newPassword" placeholder="At least 6 characters" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('newPassword') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm New Password</label>
                        <input type="password" wire:model="newPasswordConfirmation" placeholder="Repeat new password" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                        @error('newPasswordConfirmation') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showPasswordModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: REQUEST PARISH TRANSFER MODAL                                    -->
    <!-- ========================================================================= -->
    @if($showParishModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Request Parish Change</h3>
                    <button wire:click="$set('showParishModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="requestParishTransfer" class="space-y-3">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Parish change requests are reviewed by your Parish Chairperson to maintain diocesan roster integrity.
                    </p>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">New Target Parish</label>
                        <select wire:model="targetParishId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
                            <option value="">-- Select Parish --</option>
                            @foreach($parishes as $p)
                                @if($p->id !== $user->parish_id)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->deanery?->name }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('targetParishId') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason for Change</label>
                        <textarea wire:model="transferReason" rows="2" placeholder="e.g. Relocated to Maramba parish outstation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white"></textarea>
                        @error('transferReason') <span class="text-[10px] text-red-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showParishModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 5: PREFERENCES & PRIVACY MODAL                                      -->
    <!-- ========================================================================= -->
    @if($showPreferencesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-sm w-full space-y-3.5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase">Account Preferences</h3>
                    <button wire:click="$set('showPreferencesModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="savePreferences" class="space-y-3">
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Notifications</span>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="notifyFormation" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Daily Formation &amp; Streak Reminders</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="notifyCompetitions" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Diocesan Youth Rally &amp; Competitions</span>
                        </label>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Privacy in Public Rankings</span>
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="showNameInRankings" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Show my name on Diocesan Leaderboard</span>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showPreferencesModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 6: ACCOUNT DEACTIVATION CONFIRMATION MODAL                           -->
    <!-- ========================================================================= -->
    @if($showDeactivateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-5 max-w-xs w-full space-y-3 shadow-2xl">
                <h3 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase">Deactivate Your Account?</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    Your formation streak will pause. Historical quiz records and achievements will be safely preserved for diocesan youth records.
                </p>

                <div class="flex gap-2 pt-2">
                    <button type="button" wire:click="$set('showDeactivateModal', false)" class="w-1/2 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="button" wire:click="deactivateAccount" class="w-1/2 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-sm">
                        Deactivate
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
