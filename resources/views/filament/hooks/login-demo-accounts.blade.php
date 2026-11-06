<div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
    <div class="text-center mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
            <svg class="w-3.5 h-3.5 text-teal-700 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            1-Click Demo Personas
        </span>
        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1.5 font-medium">Select a role below to auto-fill hospital credentials:</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        <!-- Administrator / Chief of Hospital -->
        <button type="button" onclick="fillLogin('admin@rmph.gov.ph', 'password')"
            class="group p-3 text-left bg-slate-50 dark:bg-slate-800/90 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 min-h-[56px]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 truncate">
                        Chief of Hospital II
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">Full system governance</div>
                </div>
            </div>
        </button>

        <!-- Pharmacy Consignment Officer -->
        <button type="button" onclick="fillLogin('kristine.batan@rmph.gov.ph', 'password')"
            class="group p-3 text-left bg-slate-50 dark:bg-slate-800/90 hover:bg-sky-50 dark:hover:bg-sky-950/40 border border-slate-200 dark:border-slate-700 hover:border-sky-300 dark:hover:border-sky-600 rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 min-h-[56px]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-sky-700 dark:group-hover:text-sky-300 truncate">
                        Kristine Mae Batan
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">Consignment (Pharmacy)</div>
                </div>
            </div>
        </button>

        <!-- Medical Supplies Consignment Officer -->
        <button type="button" onclick="fillLogin('aliana.dula@rmph.gov.ph', 'password')"
            class="group p-3 text-left bg-slate-50 dark:bg-slate-800/90 hover:bg-teal-50 dark:hover:bg-teal-950/40 border border-slate-200 dark:border-slate-700 hover:border-teal-300 dark:hover:border-teal-600 rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-teal-500 min-h-[56px]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-800 dark:text-teal-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-teal-800 dark:group-hover:text-teal-300 truncate">
                        Aliana Marie Dula
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">Consignment (Med Supplies)</div>
                </div>
            </div>
        </button>

        <!-- CSR / HDU Clinical Requester -->
        <button type="button" onclick="fillLogin('csr.nurse@rmph.gov.ph', 'password')"
            class="group p-3 text-left bg-slate-50 dark:bg-slate-800/90 hover:bg-purple-50 dark:hover:bg-purple-950/40 border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[56px]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-purple-700 dark:group-hover:text-purple-300 truncate">
                        Mia Buenvenida, RN
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400">CSR / HDU Requester</div>
                </div>
            </div>
        </button>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        const emailInput = document.querySelector('input[type="email"]') || document.querySelector('input[name*="email"]');
        const passInput = document.querySelector('input[type="password"]') || document.querySelector('input[name*="password"]');

        if (emailInput) {
            emailInput.value = email;
            emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            emailInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (passInput) {
            passInput.value = password;
            passInput.dispatchEvent(new Event('input', { bubbles: true }));
            passInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
</script>
