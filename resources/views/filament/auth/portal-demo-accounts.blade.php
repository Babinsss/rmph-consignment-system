<div class="mt-6 pt-5 border-t border-gray-200 text-xs">
    @if (! app()->environment('production'))
        <div class="flex items-center justify-between mb-3">
            <span class="font-semibold text-gray-700">Ward Staff Demo Accounts (Click to Fill)</span>
            <span class="text-gray-500">Password: <code class="px-1.5 py-0.5 rounded bg-gray-100 text-teal-700 font-mono font-semibold">password</code></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <button type="button"
                    onclick="fillPortalLogin('csr@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    CSR Staff
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">csr@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillPortalLogin('hdu@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    HDU Staff
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">hdu@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillPortalLogin('dialysis@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Gugma Dialysis
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">dialysis@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillPortalLogin('pharmacy.main@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Pharmacy Main
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">pharmacy.main@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillPortalLogin('pharmacy.opd@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Pharmacy OPD
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">pharmacy.opd@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillPortalLogin('yakap@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Yakap Clinic
                </span>
                <span class="text-[10px] text-gray-500 font-mono truncate w-full">yakap@rmph.local</span>
            </button>
        </div>

        <script>
            function fillPortalLogin(email) {
                const emailInput = document.getElementById('form.email') || document.querySelector('input[type="email"]');
                const passwordInput = document.getElementById('form.password') || document.querySelector('input[type="password"]');
                if (emailInput) {
                    emailInput.value = email;
                    emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                    emailInput.dispatchEvent(new Event('change', { bubbles: true }));
                    emailInput.focus();
                }
                if (passwordInput) {
                    passwordInput.value = 'password';
                    passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
                    passwordInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        </script>
    @endif

    <div class="mt-4 pt-3 border-t border-dashed border-gray-200 text-center">
        <span class="text-gray-500">Are you an administrator or custodian?</span>
        <a href="{{ url('/admin/login') }}" class="font-semibold text-teal-700 hover:text-teal-600 underline ml-1">
            Open Central Operations &rarr;
        </a>
    </div>
</div>
