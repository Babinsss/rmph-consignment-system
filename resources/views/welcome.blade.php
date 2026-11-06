<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMPH Consignment Inventory Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-teal-700 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-800 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 leading-none">RMPH Consignment System</h1>
                    <p class="text-xs font-semibold text-teal-800 tracking-wide mt-1">Roxas Memorial Provincial Hospital</p>
                </div>
            </div>

            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('portal') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-emerald-50 text-emerald-900 border border-emerald-300 hover:bg-emerald-100 transition focus:outline-none focus:ring-2 focus:ring-emerald-600">
                    <svg class="w-4 h-4 text-emerald-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Ward Requisition</span>
                </a>
                <a href="{{ route('filament.admin.auth.login') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-teal-800 hover:bg-teal-900 text-white shadow-sm transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-teal-700">
                    <span>Staff Login</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-12 pb-16 lg:pt-18 lg:pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-900 border border-teal-200 mb-6">
                        <span class="w-2 h-2 rounded-full bg-teal-700"></span>
                        Capiz Provincial Government Healthcare System
                    </div>
                    <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight [text-wrap:balance]">
                        Precision Healthcare <span class="text-teal-800">Consignment Inventory</span>
                    </h2>
                    <p class="mt-6 text-base sm:text-lg text-slate-700 leading-relaxed max-w-[65ch] mx-auto [text-wrap:pretty]">
                        End-to-end hospital consignment tracking partitioning Pharmacy and Medical Supplies with automated FEFO batch allocation, perpetual stockcards, supplier delivery logs, and ward requisition workflows.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-3 sm:gap-4">
                        <a href="{{ route('portal') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-700">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Open Ward Requisition Portal</span>
                        </a>
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold bg-teal-800 hover:bg-teal-900 text-white shadow-sm transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-teal-800">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Operations Center</span>
                        </a>
                        <a href="{{ route('filament.admin.pages.inventory-reports') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 shadow-sm transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Audit & Valuation Reports</span>
                        </a>
                    </div>
                </div>

                <!-- 1-Click Interactive Persona Switcher -->
                <div class="mt-14 max-w-5xl mx-auto">
                    <div class="text-center mb-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-teal-800">Hospital Roles & Access Control</h3>
                        <p class="text-xs text-slate-600 mt-1 max-w-[60ch] mx-auto [text-wrap:pretty]">Select a hospital persona below to preview compartmentalized operations:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Admin -->
                        <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-indigo-300 hover:shadow-md transition-all duration-150 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-indigo-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <h4 class="font-bold text-slate-900 text-sm">Dr. Florencio Luching Jr.</h4>
                                </div>
                                <p class="text-xs text-slate-600">coh@rmph.gov.ph</p>
                                <p class="text-xs text-indigo-800 font-semibold mt-1">Chief of Hospital II Oversight</p>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-indigo-700 hover:text-indigo-900 inline-flex items-center gap-1 group">
                                <span>Sign In as Chief of Hospital</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <!-- Pharmacy -->
                        <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-sky-300 hover:shadow-md transition-all duration-150 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-sky-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    <h4 class="font-bold text-slate-900 text-sm">Kristine Mae Batan</h4>
                                </div>
                                <p class="text-xs text-slate-600">kristine.batan@rmph.gov.ph</p>
                                <p class="text-xs text-sky-800 font-semibold mt-1">Consignment Lead (Pharmacy)</p>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-sky-700 hover:text-sky-900 inline-flex items-center gap-1 group">
                                <span>Sign In as Pharmacy Lead</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <!-- Medical Supplies -->
                        <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-teal-300 hover:shadow-md transition-all duration-150 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h4 class="font-bold text-slate-900 text-sm">Aliana Marie Dula</h4>
                                </div>
                                <p class="text-xs text-slate-600">aliana.dula@rmph.gov.ph</p>
                                <p class="text-xs text-teal-800 font-semibold mt-1">Consignment Lead (Med Supplies)</p>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-teal-800 hover:text-teal-950 inline-flex items-center gap-1 group">
                                <span>Sign In as Med Supplies Lead</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <!-- Authorized Requester -->
                        <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-purple-300 hover:shadow-md transition-all duration-150 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-purple-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h4 class="font-bold text-slate-900 text-sm">Mia Buenvenida, RN</h4>
                                </div>
                                <p class="text-xs text-slate-600">csr.nurse@rmph.gov.ph</p>
                                <p class="text-xs text-purple-800 font-semibold mt-1">CSR / HDU / Dialysis Requester</p>
                            </div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="mt-4 text-xs font-bold text-purple-700 hover:text-purple-900 inline-flex items-center gap-1 group">
                                <span>Sign In as CSR Requester</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Overview -->
        <section class="py-16 bg-white border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight [text-wrap:balance]">Core System Capabilities</h3>
                    <p class="text-sm sm:text-base text-slate-700 mt-2 max-w-[65ch] mx-auto [text-wrap:pretty]">Engineered to eliminate stockouts, prevent expired consignment losses, and streamline hospital audit compliance.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-50 border border-slate-200 hover:border-teal-300 transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="w-5 h-5 text-teal-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <h4 class="font-bold text-slate-900 text-base">FEFO Batch Management</h4>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed [text-wrap:pretty]">
                            First-Expired, First-Out automatic allocation ensures medicines and sterile supplies with earliest expiration dates are dispensed first, preventing costly consignment write-offs.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-50 border border-slate-200 hover:border-sky-300 transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="w-5 h-5 text-sky-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <h4 class="font-bold text-slate-900 text-base">Printable Stockcards & RIS</h4>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed [text-wrap:pretty]">
                            Full audit compliance with standard hospital perpetual stockcard printouts and 4-signature Requisition and Issue Slips (RIS) ready for immediate physical paper documentation.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-300 transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="w-5 h-5 text-emerald-800 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h4 class="font-bold text-slate-900 text-base">Consignment Valuation Reports</h4>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed [text-wrap:pretty]">
                            On-demand weekly, monthly, and yearly reports calculating supplier liabilities, hospital consumption costs, beginning balances, and total physical inventory value on hand.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-10 border-t border-slate-800 text-center text-xs sm:text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-slate-300 font-medium">Roxas Memorial Provincial Hospital (RMPH) • Hospital Extensive Consignment System</p>
            <p class="text-slate-400 mt-1">&copy; {{ date('Y') }} Capiz Provincial Government Health Services. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
