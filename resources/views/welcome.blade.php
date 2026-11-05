<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMPH Consignment Inventory Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 flex items-center justify-center text-white text-xl shadow-md shadow-teal-500/20">
                    🏥
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900 leading-none">RMPH Consignment System</h1>
                    <span class="text-[11px] font-semibold text-teal-600 uppercase tracking-wider">Healthcare Inventory & FEFO Ledger</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('portal') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                    <span>🛒</span> Requisition Portal
                </a>
                <a href="{{ route('filament.admin.auth.login') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition active:scale-95">
                    <span>Staff Login</span> →
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main>
        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200 mb-6">
                        <span>✨</span> Tailored for Hospital & Clinical Consignments
                    </div>
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                        Precision Healthcare <span class="bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">Consignment Inventory</span>
                    </h2>
                    <p class="mt-6 text-base sm:text-lg text-slate-600 leading-relaxed">
                        End-to-end consignment tracking separating <strong>Pharmacy</strong> and <strong>Medical Supplies</strong>. Automated FEFO batch allocation, perpetual stockcards, supplier delivery logs, and ward requisition workflows.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('portal') }}"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/25 transition active:scale-95">
                            <span>🛒</span> Open Department Requisition Portal
                        </a>
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/25 transition active:scale-95">
                            <span>🚀</span> Consignment Operations Center
                        </a>
                        <a href="{{ route('filament.admin.pages.inventory-reports') }}"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 shadow-sm transition active:scale-95">
                            <span>📊</span> View Audit Reports
                        </a>
                    </div>
                </div>

                <!-- 1-Click Interactive Persona Switcher -->
                <div class="mt-16 max-w-4xl mx-auto bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 sm:p-8">
                    <div class="text-center mb-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-teal-600">Explore Role-Based Access Control (RBAC)</h3>
                        <p class="text-xs text-slate-500 mt-1">Select a healthcare persona to test departmental partitioning:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Admin -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-teal-50/50 hover:border-teal-300 transition text-left flex flex-col justify-between">
                            <div>
                                <span class="text-2xl">👑</span>
                                <div class="font-bold text-slate-900 text-sm mt-2">Dr. Florencio Luching Jr.</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">coh@rmph.gov.ph</div>
                                <div class="text-[11px] text-teal-600 font-semibold mt-1">Chief of Hospital II Oversight</div>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                                Login as Chief of Hospital →
                            </a>
                        </div>

                        <!-- Pharmacy -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50/50 hover:border-blue-300 transition text-left flex flex-col justify-between">
                            <div>
                                <span class="text-2xl">💊</span>
                                <div class="font-bold text-slate-900 text-sm mt-2">Kristine Mae Batan</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">kristine.batan@rmph.gov.ph</div>
                                <div class="text-[11px] text-blue-600 font-semibold mt-1">Consignment Officer (Pharmacy)</div>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                Login as Pharmacy Lead →
                            </a>
                        </div>

                        <!-- Medical Supplies -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50/50 hover:border-emerald-300 transition text-left flex flex-col justify-between">
                            <div>
                                <span class="text-2xl">🩺</span>
                                <div class="font-bold text-slate-900 text-sm mt-2">Aliana Marie Dula</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">aliana.dula@rmph.gov.ph</div>
                                <div class="text-[11px] text-emerald-600 font-semibold mt-1">Consignment Officer (Med Supplies)</div>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                Login as Med Supplies Lead →
                            </a>
                        </div>

                        <!-- Authorized Requester -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-purple-50/50 hover:border-purple-300 transition text-left flex flex-col justify-between">
                            <div>
                                <span class="text-2xl">🏥</span>
                                <div class="font-bold text-slate-900 text-sm mt-2">Mia Buenvenida, RN</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">csr.nurse@rmph.gov.ph</div>
                                <div class="text-[11px] text-purple-600 font-semibold mt-1">CSR / HDU / Dialysis Requester</div>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                                Login as CSR Requester →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Grid -->
        <section class="py-16 bg-white border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Core System Capabilities</h3>
                    <p class="text-sm text-slate-600 mt-2">Engineered to eliminate stockouts, prevent expired consignment losses, and streamline billing.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl mb-4">
                            🔄
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">FEFO Batch Management</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            First-Expired, First-Out automatic allocation ensures medicines and sterile supplies with earliest expiration dates are dispensed first, preventing costly consignment write-offs.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl mb-4">
                            🖨️
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">Printable Stockcards & RIS</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Full audit compliance with standard hospital perpetual stockcard printouts and 4-signature Requisition and Issue Slips (RIS) ready for immediate physical paper documentation.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl mb-4">
                            📈
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">Consignment Valuation Reports</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            On-demand weekly, monthly, and yearly reports calculating supplier liabilities, hospital consumption costs, beginning balances, and total physical inventory value on hand.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800 text-center text-xs">
        <div class="max-w-7xl mx-auto px-4">
            <p>Roxas Memorial Provincial Hospital (RMPH) • Hospital Extensive Consignment System &copy; {{ date('Y') }}. Capiz Provincial Government.</p>
        </div>
    </footer>

</body>
</html>
