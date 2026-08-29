<div class="relative py-8 max-w-sm mx-auto flex flex-col justify-center min-h-[85vh] overflow-hidden"
     x-data="{ 
         showPassword: false,
         hasBiometric: false,
         biometricUser: null,
         biometricAuthenticating: false,
         biometricError: null,
         showPasswordForm: false,
         
         init() {
             try {
                 const raw = localStorage.getItem('catholic_youth_biometric_auth');
                 if (raw) {
                     const data = JSON.parse(raw);
                     if (data && (data.userId || data.credentialId)) {
                         this.hasBiometric = true;
                         this.biometricUser = data;
                     }
                 }
             } catch(e) {
                 console.error('Biometric storage read error:', e);
             }

             // Listen for biometric enrollment event from server (e.g. on login)
             window.addEventListener('biometric-enrolled-on-device', (e) => {
                 const payload = Array.isArray(e.detail) ? e.detail[0] : (e.detail?.payload || e.detail);
                 if (payload) {
                     localStorage.setItem('catholic_youth_biometric_auth', JSON.stringify(payload));
                     this.hasBiometric = true;
                     this.biometricUser = payload;
                 }
             });

             window.addEventListener('biometric-auth-failed', () => {
                 this.biometricAuthenticating = false;
                 this.showPasswordForm = true;
             });

             window.addEventListener('biometric-auth-success', (e) => {
                 this.biometricAuthenticating = false;
             });
         },

         async triggerBiometricLogin() {
             this.biometricAuthenticating = true;
             this.biometricError = null;

             try {
                 // Trigger subtle haptic feedback
                 if ('vibrate' in navigator) {
                     navigator.vibrate([25, 40, 25]);
                 }

                 // Check if user entered phone/email on the form
                 const typedIdentifier = this.$wire.identifier ? this.$wire.identifier.trim() : '';

                 if (!this.biometricUser && typedIdentifier) {
                     const remoteUser = await this.$wire.initiateBiometricForIdentifier(typedIdentifier);
                     if (remoteUser && remoteUser.userId) {
                         this.biometricUser = remoteUser;
                     } else {
                         this.biometricAuthenticating = false;
                         return;
                     }
                 }

                 if (!this.biometricUser && !typedIdentifier) {
                     this.biometricAuthenticating = false;
                     this.biometricError = 'Please enter your phone/email or sign in with password to use fingerprint on this device.';
                     return;
                 }

                 let hardwareVerified = false;
                 let assertionCredentialId = null;

                 // 1. Trigger hardware biometric / passkey sensor
                 if (window.PublicKeyCredential && navigator.credentials && navigator.credentials.get) {
                     try {
                         const challenge = new Uint8Array(32);
                         window.crypto.getRandomValues(challenge);

                         const getOptions = {
                             challenge: challenge,
                             rpId: window.location.hostname,
                             userVerification: 'required',
                             timeout: 60000
                         };

                         const assertion = await navigator.credentials.get({
                             publicKey: getOptions
                         });

                         if (assertion) {
                             hardwareVerified = true;
                             assertionCredentialId = assertion.id;
                         }
                     } catch (authErr) {
                         if (authErr.name === 'NotAllowedError' || authErr.name === 'AbortError') {
                             this.biometricAuthenticating = false;
                             this.biometricError = 'Fingerprint scan was not recognized or was cancelled.';
                             return;
                         }
                         console.warn('WebAuthn hardware fallback notice:', authErr);
                     }
                 }

                 // 2. If NativePHP Mobile Bridge is active
                 if (window.Native && window.Native.biometric) {
                     try {
                         await window.Native.biometric.prompt();
                     } catch(ne) {
                         console.warn('Native biometric prompt notification:', ne);
                     }
                 }

                 // Dispatch login to Livewire
                 if (this.biometricUser?.userId && this.biometricUser?.token) {
                     $wire.biometricLogin(this.biometricUser.userId, this.biometricUser.token);
                 } else if (typedIdentifier) {
                     $wire.biometricLoginDirect(typedIdentifier);
                 } else if (assertionCredentialId) {
                     $wire.biometricLoginByCredential(assertionCredentialId);
                 } else {
                     this.biometricAuthenticating = false;
                     this.biometricError = 'Please enter your phone/email or sign in with password to link your biometric.';
                 }
             } catch(err) {
                 this.biometricAuthenticating = false;
                 this.biometricError = 'Biometric scan was cancelled or unverified.';
             }
         },

         forgetDeviceBiometrics() {
             if (confirm('Remove biometric credentials for ' + (this.biometricUser?.name || 'this account') + ' on this device?')) {
                 localStorage.removeItem('catholic_youth_biometric_auth');
                 this.hasBiometric = false;
                 this.biometricUser = null;
                 this.showPasswordForm = true;
             }
         }
     }">
    
    <!-- SUBTLE BACKGROUND GEOMETRY (FAINT DIOCESAN WATERMARK) -->
    <div class="absolute inset-0 pointer-events-none flex items-center justify-center -z-10 opacity-[0.03] dark:opacity-[0.04]">
        <svg class="w-96 h-96 text-purple-950 dark:text-purple-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20m-7-14h14M8 4h8"/>
        </svg>
    </div>

    <!-- DIOCESAN BRANDING HEADER -->
    <div class="text-center mb-6 space-y-2">
        <!-- Brand Mark Container -->
        <div class="relative w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200/80 dark:border-purple-800/80 text-purple-700 dark:text-purple-300 mx-auto flex items-center justify-center shadow-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-13h12"/>
            </svg>
        </div>

        <div class="space-y-1">
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-[10px] font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300">
                Livingstone Diocese &bull; Zambia
            </span>
            <h1 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catholic Youth Ministry</h1>
            <h2 class="text-2xl font-bold font-serif text-slate-900 dark:text-white tracking-tight">Welcome Back</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Continue your faith formation journey in Scripture &amp; Catechism.</p>
        </div>
    </div>

    <!-- 1. BIOMETRIC QUICK SIGN-IN PROMPT (WHEN ENROLLED ON THIS DEVICE) -->
    <div x-show="hasBiometric && !showPasswordForm" x-cloak class="space-y-4 animate-fade-in">
        <div class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-5 text-center shadow-sm">
            
            <!-- User Avatar & Identity Header -->
            <div class="space-y-2">
                <div class="relative w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-950/60 border-2 border-purple-300 dark:border-purple-700 mx-auto flex items-center justify-center overflow-hidden shadow-sm">
                    <template x-if="biometricUser?.avatar">
                        <img :src="biometricUser.avatar" :alt="biometricUser.name" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!biometricUser?.avatar">
                        <span class="text-xl font-bold text-purple-700 dark:text-purple-300" x-text="biometricUser?.name ? biometricUser.name.charAt(0) : 'U'"></span>
                    </template>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="biometricUser?.name || 'Parish Youth'"></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="biometricUser?.parish || 'Livingstone Diocese'"></p>
                </div>
            </div>

            <!-- Error Banner -->
            @if($errors->has('identifier'))
                <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 rounded-xl text-xs text-red-700 dark:text-red-300 leading-snug flex items-start gap-2 text-left animate-fade-in">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('identifier') }}</span>
                </div>
            @endif

            <div x-show="biometricError" x-cloak class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-xl text-xs text-amber-800 dark:text-amber-300 leading-snug flex items-start gap-2 text-left animate-fade-in">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="biometricError"></span>
            </div>

            <!-- Biometric Scan Action Button -->
            <button 
                type="button" 
                @click="triggerBiometricLogin()"
                :disabled="biometricAuthenticating"
                class="w-full py-3.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2.5 transition-all shadow-md touch-press disabled:opacity-75">
                
                <template x-if="!biometricAuthenticating">
                    <div class="flex items-center gap-2">
                        <!-- Fingerprint & Face ID SVG Icon -->
                        <svg class="w-5 h-5 text-purple-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.5"/>
                        </svg>
                        <span>Sign In with Face ID / Fingerprint</span>
                    </div>
                </template>

                <template x-if="biometricAuthenticating">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Verifying Biometrics...</span>
                    </div>
                </template>
            </button>

            <!-- Switch to Password Login -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <button 
                    type="button" 
                    @click="showPasswordForm = true" 
                    class="text-purple-600 dark:text-purple-400 font-bold hover:underline">
                    Use Password instead
                </button>

                <button 
                    type="button" 
                    @click="forgetDeviceBiometrics()" 
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-[11px]">
                    Switch Account
                </button>
            </div>
        </div>
    </div>

    <!-- 2. MAIN LOGIN PANEL -->
    <div x-show="!hasBiometric || showPasswordForm" x-cloak>
        <form wire:submit.prevent="login" 
              @submit="try { sessionStorage.setItem('show_login_preloader', 'true'); } catch(e) {}"
              class="bg-white dark:bg-[#121826] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-4 shadow-sm animate-fade-in">
            
            <!-- Error Alert -->
            @if($errors->has('identifier'))
                <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 rounded-xl text-xs text-red-700 dark:text-red-300 leading-snug flex items-start gap-2 animate-fade-in">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('identifier') }}</span>
                </div>
            @endif

            <div x-show="biometricError" x-cloak class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-xl text-xs text-amber-800 dark:text-amber-300 leading-snug flex items-start gap-2 text-left animate-fade-in">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="biometricError"></span>
            </div>

            <!-- Identifier Input (Phone or Email) -->
            <div class="space-y-1">
                <label for="login-identifier" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                    Phone Number or Email
                </label>
                <div class="relative">
                    <input 
                        id="login-identifier"
                        type="text" 
                        wire:model="identifier" 
                        autocomplete="username"
                        required
                        placeholder="e.g. +260970000003 or youth@parish.com"
                        class="w-full pl-3.5 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
                </div>
            </div>

            <!-- Password Input with Visibility Toggle -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label for="login-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Password
                    </label>
                    <a href="javascript:void(0)" 
                       onclick="alert('Please contact your Parish Chairperson or Youth Executive to reset your password.')" 
                       class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                        Forgot password?
                    </a>
                </div>
                <div class="relative">
                    <input 
                        id="login-password"
                        :type="showPassword ? 'text' : 'password'" 
                        wire:model="password" 
                        autocomplete="current-password"
                        required
                        placeholder="••••••••"
                        class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-1 focus:ring-purple-600 transition-colors">
                    
                    <!-- Password Visibility Toggle Button -->
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword"
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                        title="Toggle password visibility">
                        <!-- Eye open -->
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <!-- Eye closed -->
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model="remember"
                        class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 dark:border-slate-700 dark:bg-slate-900">
                    <span class="font-medium">Remember this device</span>
                </label>
            </div>

            <!-- Action Buttons: Password Sign In & Biometric Sign In on Main Panel -->
            <div class="space-y-2.5 pt-1">
                <!-- Primary Password Sign In Button -->
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 disabled:opacity-75 text-white font-bold rounded-xl transition-colors text-xs touch-press flex items-center justify-center gap-2 shadow-sm">
                    <span wire:loading.remove>Sign In with Password</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Signing In...</span>
                    </span>
                </button>

                <!-- Biometric Sign-In Button on Main Panel -->
                <button 
                    type="button" 
                    @click="triggerBiometricLogin()"
                    :disabled="biometricAuthenticating"
                    class="w-full py-2.5 px-4 bg-slate-100 dark:bg-slate-800/90 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition-all border border-slate-200/80 dark:border-slate-700/80 touch-press disabled:opacity-75">
                    
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.5"/>
                    </svg>
                    <span x-show="!biometricAuthenticating">Sign In with Fingerprint / Face ID</span>
                    <span x-show="biometricAuthenticating" x-cloak class="flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-purple-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Scanning Fingerprint...</span>
                    </span>
                </button>
            </div>

            <!-- Switch to Register -->
            <div class="pt-3 text-center border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    New youth member?
                    <a href="/register" class="text-purple-600 dark:text-purple-400 font-bold hover:underline block mt-1">
                        Register under your Parish &rarr;
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
