<div class="mt-6 space-y-4">
    {{-- Secondary Navigation Button to Operations Login --}}
    <div>
        <x-filament::button
            :href="url('/admin/login')"
            tag="a"
            color="gray"
            icon="heroicon-m-arrow-top-right-on-square"
            icon-position="after"
            class="w-full min-h-[52px] justify-center text-xs font-semibold shadow-2xs"
        >
            Central Operations staff? Open the Operations login
        </x-filament::button>
    </div>

    {{-- Collapsible Demo Account Selector (APP_ENV=local only) --}}
    @if (app()->environment('local'))
        <div class="pt-1">
            <details class="group rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-600 transition">
                <summary class="flex cursor-pointer items-center justify-between font-medium text-slate-700 hover:text-teal-700 select-none py-1">
                    <span class="flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-user-circle" class="w-4 h-4 text-slate-400 group-hover:text-teal-600" />
                        <span>Use a demo account</span>
                    </span>
                    <x-filament::icon icon="heroicon-m-chevron-down" class="w-3.5 h-3.5 transition duration-200 group-open:rotate-180 text-slate-400" />
                </summary>

                <div class="mt-3 pt-2.5 border-t border-slate-200/80 space-y-2">
                    <label for="portal-demo-select" class="block text-[11px] font-medium text-slate-500">
                        Select a ward staff account to populate email:
                    </label>
                    <div class="relative">
                        <select
                            id="portal-demo-select"
                            onchange="fillDemoEmail(this.value)"
                            class="w-full min-h-[52px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 shadow-2xs focus:border-teal-600 focus:outline-hidden focus:ring-2 focus:ring-teal-600/20"
                        >
                            <option value="">-- Choose a ward account --</option>
                            <option value="csr@rmph.local">CSR Staff (csr@rmph.local)</option>
                            <option value="hdu@rmph.local">HDU Staff (hdu@rmph.local)</option>
                            <option value="dialysis@rmph.local">Gugma Dialysis Staff (dialysis@rmph.local)</option>
                            <option value="pharmacy.main@rmph.local">Pharmacy Main Staff (pharmacy.main@rmph.local)</option>
                            <option value="pharmacy.opd@rmph.local">Pharmacy OPD Staff (pharmacy.opd@rmph.local)</option>
                            <option value="yakap@rmph.local">Yakap Clinic Staff (yakap@rmph.local)</option>
                        </select>
                    </div>
                </div>
            </details>
        </div>

        <script>
            function fillDemoEmail(email) {
                if (!email) return;
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
            }
        </script>
    @endif
</div>
