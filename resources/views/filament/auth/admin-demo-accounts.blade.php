<div class="mt-6 pt-5 border-t border-gray-200 text-xs">
    @if (! app()->environment('production'))
        <div class="flex items-center justify-between mb-3">
            <span class="font-semibold text-gray-700">Demo Accounts (Click to Fill)</span>
            <span class="text-gray-500">Password: <code class="px-1.5 py-0.5 rounded bg-gray-100 text-teal-700 font-mono font-semibold">password</code></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button type="button"
                    onclick="fillAdminLogin('admin@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    IT Administrator
                </span>
                <span class="text-[11px] text-gray-500 font-mono">admin@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillAdminLogin('aliana.dula@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Supplies Custodian
                </span>
                <span class="text-[11px] text-gray-500 font-mono">aliana.dula@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillAdminLogin('kristine.batan@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Drugs Custodian
                </span>
                <span class="text-[11px] text-gray-500 font-mono">kristine.batan@rmph.local</span>
            </button>

            <button type="button"
                    onclick="fillAdminLogin('chief@rmph.local')"
                    class="flex flex-col items-start p-2.5 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50/50 text-left transition group">
                <span class="font-semibold text-gray-800 group-hover:text-teal-700">
                    Chief of Hospital
                </span>
                <span class="text-[11px] text-gray-500 font-mono">chief@rmph.local</span>
            </button>
        </div>

        <script>
            function fillAdminLogin(email) {
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
        <span class="text-gray-500">Are you requisitioning for a clinical ward?</span>
        <a href="{{ url('/portal/login') }}" class="font-semibold text-teal-700 hover:text-teal-600 underline ml-1">
            Open Ward Portal &rarr;
        </a>
    </div>
</div>
