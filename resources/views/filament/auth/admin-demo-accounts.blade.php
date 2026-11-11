<div class="mt-6 space-y-4">
    {{-- Secondary Navigation Button to Ward Portal --}}
    <div>
        <x-filament::button
            :href="url('/portal/login')"
            tag="a"
            color="gray"
            icon="heroicon-m-arrow-top-right-on-square"
            icon-position="after"
            class="w-full min-h-[48px] justify-center text-xs font-semibold shadow-2xs"
        >
            Ward staff? Open the Ward Portal
        </x-filament::button>
    </div>

    {{-- Collapsible Demo Account Selector (APP_ENV=local only) --}}
    @if (app()->environment('local'))
        <div class="pt-1">
            <details class="group rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-600 transition">
                <summary class="flex cursor-pointer items-center justify-between font-medium text-slate-700 hover:text-teal-700 select-none py-0.5">
                    <span class="flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-m-user-circle" class="w-4 h-4 text-slate-400 group-hover:text-teal-600" />
                        <span>Use a demo account</span>
                    </span>
                    <x-filament::icon icon="heroicon-m-chevron-down" class="w-3.5 h-3.5 transition duration-200 group-open:rotate-180 text-slate-400" />
                </summary>

                <div class="mt-3 pt-2.5 border-t border-slate-200/80 space-y-2">
                    <label for="demo-account-select" class="block text-[11px] font-medium text-slate-500">
                        Select a pre-configured role to populate email:
                    </label>
                    <div class="relative">
                        <select
                            id="demo-account-select"
                            onchange="fillDemoEmail(this.value)"
                            class="w-full min-h-[48px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 shadow-2xs focus:border-teal-600 focus:outline-hidden focus:ring-2 focus:ring-teal-600/20"
                        >
                            <option value="">-- Choose a demo account --</option>
                            <option value="admin@rmph.local">IT Administrator (admin@rmph.local)</option>
                            <option value="aliana.dula@rmph.local">Supplies Custodian (aliana.dula@rmph.local)</option>
                            <option value="kristine.batan@rmph.local">Drugs Custodian (kristine.batan@rmph.local)</option>
                            <option value="chief@rmph.local">Chief of Hospital (chief@rmph.local)</option>
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
