<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="text-center mb-3">
        <span class="text-xs font-semibold uppercase tracking-wider text-teal-600 dark:text-teal-400">
            🏥 1-Click Demo Accounts (Test RBAC)
        </span>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Click any persona below to auto-fill credentials:</p>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs">
        <button type="button" onclick="fillLogin('admin@rmph.gov.ph', 'password')"
            class="p-2 text-left bg-gray-50 dark:bg-gray-800 hover:bg-teal-50 dark:hover:bg-teal-900/30 border border-gray-200 dark:border-gray-700 rounded-lg transition group">
            <div class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-teal-600 dark:group-hover:text-teal-400 flex items-center gap-1.5">
                <span>👑</span> Administrator
            </div>
            <div class="text-[10px] text-gray-500 dark:text-gray-400">Full system oversight</div>
        </button>

        <button type="button" onclick="fillLogin('kristine.batan@rmph.gov.ph', 'password')"
            class="p-2 text-left bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 border border-gray-200 dark:border-gray-700 rounded-lg transition group">
            <div class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 flex items-center gap-1.5">
                <span>💊</span> Kristine Mae Batan
            </div>
            <div class="text-[10px] text-gray-500 dark:text-gray-400">Consignment (Pharmacy)</div>
        </button>

        <button type="button" onclick="fillLogin('aliana.dula@rmph.gov.ph', 'password')"
            class="p-2 text-left bg-gray-50 dark:bg-gray-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border border-gray-200 dark:border-gray-700 rounded-lg transition group">
            <div class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 flex items-center gap-1.5">
                <span>🩺</span> Aliana Marie Dula
            </div>
            <div class="text-[10px] text-gray-500 dark:text-gray-400">Consignment (Med Supplies)</div>
        </button>

        <button type="button" onclick="fillLogin('csr.nurse@rmph.gov.ph', 'password')"
            class="p-2 text-left bg-gray-50 dark:bg-gray-800 hover:bg-purple-50 dark:hover:bg-purple-900/30 border border-gray-200 dark:border-gray-700 rounded-lg transition group">
            <div class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 flex items-center gap-1.5">
                <span>🏥</span> Mia Buenvenida, RN
            </div>
            <div class="text-[10px] text-gray-500 dark:text-gray-400">CSR / HDU Requester</div>
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
