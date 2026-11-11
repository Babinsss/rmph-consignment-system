<div class="mt-6 pt-5 border-t border-slate-200 text-xs">
    @if (! app()->environment('production'))
        <div class="flex items-center justify-between mb-3">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <x-filament::icon icon="heroicon-m-user-group" class="w-4 h-4 text-teal-700" />
                <span>Ward Staff Demo Accounts (Click to Fill)</span>
            </span>
            <span class="text-slate-500">
                Key: <code class="px-1.5 py-0.5 rounded-md bg-slate-100 text-teal-800 font-mono font-bold text-xs">password</code>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            {{-- 1. CSR --}}
            <button type="button"
                    onclick="fillPortalLogin('csr@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-cube" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">CSR Staff</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">csr@rmph.local</span>
                </div>
            </button>

            {{-- 2. HDU --}}
            <button type="button"
                    onclick="fillPortalLogin('hdu@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-shield-check" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">HDU Staff</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">hdu@rmph.local</span>
                </div>
            </button>

            {{-- 3. Dialysis --}}
            <button type="button"
                    onclick="fillPortalLogin('dialysis@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-sparkles" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">Gugma Dialysis</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">dialysis@rmph.local</span>
                </div>
            </button>

            {{-- 4. Pharmacy Main --}}
            <button type="button"
                    onclick="fillPortalLogin('pharmacy.main@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-beaker" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">Pharmacy Main</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">pharmacy.main@rmph.local</span>
                </div>
            </button>

            {{-- 5. Pharmacy OPD --}}
            <button type="button"
                    onclick="fillPortalLogin('pharmacy.opd@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-building-storefront" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">Pharmacy OPD</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">pharmacy.opd@rmph.local</span>
                </div>
            </button>

            {{-- 6. Yakap --}}
            <button type="button"
                    onclick="fillPortalLogin('yakap@rmph.local', this)"
                    class="min-h-[48px] flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white hover:border-teal-600 hover:bg-teal-50/50 active:scale-[0.98] text-left transition group shadow-2xs">
                <div class="w-7 h-7 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-heart" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-slate-800 group-hover:text-teal-700 block truncate">Yakap Clinic</span>
                    <span class="text-xs text-slate-500 font-mono block truncate">yakap@rmph.local</span>
                </div>
            </button>
        </div>

        <script>
            function fillPortalLogin(email, btn) {
                const emailInput = document.getElementById('data.email') || document.querySelector('input[type="email"]');
                const passwordInput = document.getElementById('data.password') || document.querySelector('input[type="password"]');

                if (emailInput) {
                    emailInput.value = email;
                    emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                    emailInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (passwordInput) {
                    passwordInput.value = 'password';
                    passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
                    passwordInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (btn) {
                    btn.classList.add('ring-2', 'ring-teal-600', 'bg-teal-50');
                    setTimeout(() => {
                        btn.classList.remove('ring-2', 'ring-teal-600', 'bg-teal-50');
                    }, 400);
                }

                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.focus();
                }
            }
        </script>
    @endif

    {{-- Fast Cross-Portal Switcher --}}
    <div class="mt-5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
        <div>
            <span class="font-bold text-slate-800 block text-xs">Warehouse Custodian or Administrator?</span>
            <span class="text-slate-500 text-xs">Manage catalog, consignor deliveries, and lots</span>
        </div>
        <a href="{{ url('/admin/login') }}"
           class="min-h-[44px] px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-teal-800 font-bold hover:bg-teal-50 hover:border-teal-300 transition flex items-center justify-center gap-1.5 shadow-2xs shrink-0 text-xs">
            <span>Open Central Operations</span>
            <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5 text-teal-700" />
        </a>
    </div>

    {{-- Hospital Security & Support Note --}}
    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
        <span class="flex items-center gap-1">
            <x-filament::icon icon="heroicon-m-shield-check" class="w-3.5 h-3.5 text-slate-400" />
            <span>RMPH Clinical Network</span>
        </span>
        <span>IT Helpdesk: Ext. 104</span>
    </div>
</div>
