<!-- GLOBAL BEAUTIFIED CONFIRMATION & ALERT MODAL + TOAST NOTIFICATION SYSTEM -->
<div x-data="{
        isOpen: false,
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        subtext: '',
        type: 'danger', // 'danger' | 'warning' | 'info' | 'success'
        confirmText: 'Yes, Proceed',
        cancelText: 'Cancel',
        showCancel: true,
        resolve: null,
        loading: false,

        // Toast notifications stack
        toasts: [],

        openConfirm(detail) {
            this.title = detail.title || this.getDefaultTitle(detail.message, detail.type);
            this.message = detail.message || 'Are you sure you want to proceed?';
            this.type = detail.type || this.detectType(detail.message);
            this.confirmText = detail.confirmText || this.getDefaultConfirmText(this.type, detail.message);
            this.cancelText = detail.cancelText || 'Cancel';
            this.showCancel = detail.showCancel !== false;
            this.resolve = detail.resolve || null;
            this.loading = false;
            this.isOpen = true;
        },

        confirm() {
            this.loading = true;
            if (this.resolve) {
                this.resolve(true);
            }
            setTimeout(() => {
                this.isOpen = false;
                this.loading = false;
                this.resolve = null;
            }, 120);
        },

        cancel() {
            if (this.resolve) {
                this.resolve(false);
            }
            this.isOpen = false;
            this.loading = false;
            this.resolve = null;
        },

        detectType(msg) {
            if (!msg) return 'danger';
            if (/delete|remove|destroy|purge|drop|revoke|deactivate|cannot be undone/i.test(msg)) return 'danger';
            if (/warn|reset|clear|overwrite|suspend/i.test(msg)) return 'warning';
            if (/update|change|publish|approve|transfer|save/i.test(msg)) return 'info';
            return 'danger';
        },

        getDefaultTitle(msg, explicitType) {
            const type = explicitType || this.detectType(msg);
            if (type === 'danger') return 'Confirm Deletion';
            if (type === 'warning') return 'Warning & Confirmation';
            if (type === 'info') return 'Confirm Action';
            return 'Notice';
        },

        getDefaultConfirmText(type, msg) {
            if (type === 'danger') {
                return /delete/i.test(msg) ? 'Yes, Delete' : 'Yes, Remove';
            }
            if (type === 'warning') return 'Proceed';
            if (type === 'info') return 'Confirm';
            return 'OK';
        },

        addToast(toast) {
            const id = Date.now() + Math.random();
            const toastType = toast.type || 'success';
            const defaultDuration = toastType === 'error' ? 3000 : 2200;
            const newToast = {
                id,
                title: toast.title || (toastType === 'error' ? 'Error' : 'Notification'),
                message: typeof toast === 'string' ? toast : toast.message,
                type: toastType,
                duration: toast.duration || defaultDuration
            };
            this.toasts.push(newToast);

            setTimeout(() => {
                this.removeToast(id);
            }, newToast.duration);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        init() {
            window.addEventListener('open-custom-confirm', (e) => {
                this.openConfirm(e.detail);
            });

            window.addEventListener('catholic-toast', (e) => {
                this.addToast(e.detail);
            });

            // Universal promise-based API for JavaScript
            window.CatholicConfirm = {
                show: ({ title, message, type, confirmText, cancelText }) => {
                    return new Promise((resolve) => {
                        window.dispatchEvent(new CustomEvent('open-custom-confirm', {
                            detail: { title, message, type, confirmText, cancelText, resolve, showCancel: true }
                        }));
                    });
                }
            };

            window.CatholicAlert = {
                show: ({ title, message, type = 'info', buttonText = 'OK' }) => {
                    return new Promise((resolve) => {
                        window.dispatchEvent(new CustomEvent('open-custom-confirm', {
                            detail: { title, message, type, confirmText: buttonText, showCancel: false, resolve }
                        }));
                    });
                }
            };

            window.CatholicToast = {
                show: (msg, type = 'success') => {
                    window.dispatchEvent(new CustomEvent('catholic-toast', {
                        detail: typeof msg === 'object' ? msg : { message: msg, type }
                    }));
                },
                success: (msg, title = 'Success') => {
                    window.dispatchEvent(new CustomEvent('catholic-toast', {
                        detail: { message: msg, title, type: 'success' }
                    }));
                },
                error: (msg, title = 'Error') => {
                    window.dispatchEvent(new CustomEvent('catholic-toast', {
                        detail: { message: msg, title, type: 'error' }
                    }));
                },
                info: (msg, title = 'Notice') => {
                    window.dispatchEvent(new CustomEvent('catholic-toast', {
                        detail: { message: msg, title, type: 'info' }
                    }));
                }
            };

            // Intercept Livewire confirm and wire:confirm elements
            const enhanceElement = (el) => {
                if (!el || el.__livewire_confirm_beautified) return;
                
                const rawConfirm = el.getAttribute('wire:confirm');
                if (rawConfirm !== null) {
                    el.__livewire_confirm = (action, instead) => {
                        const isDelete = /delete|remove|destroy|purge|drop|revoke|deactivate/i.test(rawConfirm);
                        const isWarning = /warn|reset|clear|overwrite|suspend/i.test(rawConfirm);
                        const type = isDelete ? 'danger' : (isWarning ? 'warning' : 'info');
                        const title = isDelete ? 'Confirm Deletion' : (isWarning ? 'Warning & Confirmation' : 'Confirm Action');
                        const confirmText = isDelete ? 'Yes, Delete' : (isWarning ? 'Proceed' : 'Confirm');

                        window.CatholicConfirm.show({
                            title,
                            message: rawConfirm,
                            type,
                            confirmText,
                            cancelText: 'Cancel'
                        }).then((confirmed) => {
                            if (confirmed) {
                                action();
                            } else {
                                if (instead) instead();
                            }
                        });
                    };
                    el.__livewire_confirm_beautified = true;
                }
            };

            // Capture phase click interceptor ensures newly rendered or dynamically morphed elements are wrapped
            document.addEventListener('click', (e) => {
                const target = e.target?.closest ? e.target.closest('[wire\\:confirm]') : null;
                if (target) {
                    enhanceElement(target);
                }
            }, true);

            // MutationObserver to pre-wrap all elements
            const observer = new MutationObserver(() => {
                document.querySelectorAll('[wire\\:confirm]').forEach(enhanceElement);
            });
            observer.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['wire:confirm'] });

            document.querySelectorAll('[wire\\:confirm]').forEach(enhanceElement);

            // Hook into Livewire lifecycle events
            document.addEventListener('livewire:init', () => {
                if (window.Livewire && window.Livewire.hook) {
                    window.Livewire.hook('morph.updated', ({ el }) => {
                        if (el.querySelectorAll) el.querySelectorAll('[wire\\:confirm]').forEach(enhanceElement);
                        if (el.matches && el.matches('[wire\\:confirm]')) enhanceElement(el);
                    });
                    window.Livewire.hook('morph.added', ({ el }) => {
                        if (el.querySelectorAll) el.querySelectorAll('[wire\\:confirm]').forEach(enhanceElement);
                        if (el.matches && el.matches('[wire\\:confirm]')) enhanceElement(el);
                    });
                }
            });
        }
     }"
     x-init="init()"
     @keydown.escape.window="if (isOpen) cancel()"
     class="relative z-[9999]"
     x-cloak>

    <!-- 1. BEAUTIFUL CONFIRMATION / ALERT MODAL DIALOG -->
    <div x-show="isOpen" 
         class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Frosted Backdrop with Ambient Blur -->
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
             @click="cancel()"></div>

        <!-- Modal Surface Card -->
        <div class="relative w-full max-w-md bg-white dark:bg-[#121826] border border-slate-200/90 dark:border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden transform transition-all p-6 sm:p-7 space-y-5"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             @click.stop>

            <!-- Top Ambient Glow Accent Header -->
            <div class="flex items-start gap-4">
                <!-- Dynamic Animated Icon Badge -->
                <div class="relative flex-shrink-0">
                    <!-- Danger Icon (Red) -->
                    <template x-if="type === 'danger'">
                        <div class="relative">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-rose-500/20 to-red-500/10 dark:from-rose-950/60 dark:to-red-900/40 border border-rose-200 dark:border-rose-800/80 text-rose-600 dark:text-rose-400 flex items-center justify-center shadow-lg shadow-rose-500/10">
                                <svg class="w-7 h-7 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500"></span>
                            </span>
                        </div>
                    </template>

                    <!-- Warning Icon (Amber) -->
                    <template x-if="type === 'warning'">
                        <div class="relative">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-amber-500/20 to-yellow-500/10 dark:from-amber-950/60 dark:to-yellow-900/40 border border-amber-200 dark:border-amber-800/80 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/10">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                    </template>

                    <!-- Info / Update Icon (Liturgical Purple) -->
                    <template x-if="type === 'info'">
                        <div class="relative">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-purple-500/20 to-indigo-500/10 dark:from-purple-950/60 dark:to-indigo-900/40 border border-purple-200 dark:border-purple-800/80 text-purple-600 dark:text-purple-400 flex items-center justify-center shadow-lg shadow-purple-500/10">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </template>

                    <!-- Success Icon (Emerald) -->
                    <template x-if="type === 'success'">
                        <div class="relative">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 dark:from-emerald-950/60 dark:to-teal-900/40 border border-emerald-200 dark:border-emerald-800/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/10">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Text Headings & Badge -->
                <div class="min-w-0 flex-1 pt-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border"
                              :class="{
                                  'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800': type === 'danger',
                                  'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800': type === 'warning',
                                  'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800': type === 'info',
                                  'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800': type === 'success'
                              }"
                              x-text="type === 'danger' ? 'Irreversible Action' : (type === 'warning' ? 'Important Warning' : (type === 'info' ? 'Action Required' : 'Success'))">
                        </span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1 leading-snug" x-text="title"></h3>
                </div>

                <!-- Close / Dismiss (X) button -->
                <button @click="cancel()" 
                        type="button"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl p-1.5 transition-colors touch-press flex-shrink-0" 
                        aria-label="Close dialog">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Message Card Content -->
            <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 space-y-2">
                <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium" x-text="message"></p>

                <template x-if="/cannot be undone/i.test(message)">
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-rose-600 dark:text-rose-400 pt-1">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>This change will be recorded in the Diocesan Audit Trail.</span>
                    </div>
                </template>
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex items-center justify-end gap-3 pt-1">
                <template x-if="showCancel">
                    <button @click="cancel()"
                            type="button"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-300 text-xs sm:text-sm font-semibold transition-all shadow-sm touch-press">
                        <span x-text="cancelText"></span>
                    </button>
                </template>

                <button @click="confirm()"
                        type="button"
                        :disabled="loading"
                        class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-bold transition-all shadow-lg touch-press flex items-center justify-center gap-2 min-w-[100px]"
                        :class="{
                            'bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 shadow-red-500/25 active:scale-95': type === 'danger',
                            'bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 shadow-amber-500/25 active:scale-95': type === 'warning',
                            'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-purple-500/25 active:scale-95': type === 'info',
                            'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-emerald-500/25 active:scale-95': type === 'success'
                        }">
                    <template x-if="loading">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. GLOBAL FLOATING TOAST NOTIFICATIONS STACK -->
    <div class="fixed top-4 right-4 left-4 sm:left-auto sm:w-96 z-[99999] pointer-events-none flex flex-col gap-2.5 items-end">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto w-full rounded-2xl bg-white/95 dark:bg-[#121826]/95 backdrop-blur-md border shadow-2xl p-3.5 flex items-start gap-3 transform transition-all duration-300"
                 :class="{
                     'border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100': toast.type === 'success',
                     'border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-100': toast.type === 'error',
                     'border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-100': toast.type === 'info',
                     'border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-100': toast.type === 'warning'
                 }"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 translate-x-6 scale-95">

                <!-- Toast Status Icon -->
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                     :class="{
                         'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400': toast.type === 'success',
                         'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400': toast.type === 'error',
                         'bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400': toast.type === 'info',
                         'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400': toast.type === 'warning'
                     }">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>
                </div>

                <!-- Toast Body -->
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold leading-tight" x-text="toast.title"></h4>
                    <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5 leading-snug" x-text="toast.message"></p>
                </div>

                <!-- Toast Dismiss -->
                <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs p-1 rounded-lg">
                    &times;
                </button>
            </div>
        </template>
    </div>
</div>
